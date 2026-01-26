<!-- markdownlint-disable MD041 -->
<p align="center">
    <a href="https://github.com/php-forge/support" target="_blank">
        <img src="https://avatars.githubusercontent.com/u/103309199?s%25253D400%252526u%25253Dca3561c692f53ed7eb290d3bb226a2828741606f%252526v%25253D4" height="150px" alt="PHP Forge">
    </a>
    <h1 align="center">Support</h1>
    <br>
</p>
<!-- markdownlint-enable MD041 -->

<p align="center">
    <a href="https://github.com/php-forge/support/actions/workflows/build.yml" target="_blank">
        <img src="https://img.shields.io/github/actions/workflow/status/php-forge/support/build.yml?style=for-the-badge&label=PHPUnit&logo=github" alt="PHPUnit">
    </a>
    <a href="https://dashboard.stryker-mutator.io/reports/github.com/php-forge/support/main" target="_blank">
        <img src="https://img.shields.io/endpoint?style=for-the-badge&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fphp-forge%2Fsupport%2Fmain" alt="Mutation Testing">
    </a>
    <a href="https://github.com/php-forge/support/actions/workflows/static.yml" target="_blank">
        <img src="https://img.shields.io/github/actions/workflow/status/php-forge/support/static.yml?style=for-the-badge&label=PHPStan&logo=github" alt="PHPStan">
    </a>
</p>

<p align="center">
    <strong>Support utilities for PHPUnit-focused development</strong><br>
    <em>Reflection helpers, line ending normalization, and filesystem cleanup for deterministic tests.</em>
</p>

## Features

<picture>
    <source media="(min-width: 768px)" srcset="./docs/svgs/features.svg">
    <img src="./docs/svgs/features-mobile.svg" alt="Feature Overview" style="width: 100%;">
</picture>

### Installation

```bash
composer require php-forge/support:^0.2 --dev
```

### Quick start

This package provides helper classes for PHPUnit tests.

It supports reflection-based access to non-public members, deterministic string comparisons across platforms, and filesystem cleanup for isolated test environments.

#### Accessing private properties

```php
<?php

declare(strict_types=1);

use PHPForge\Support\ReflectionHelper;
use PHPUnit\Framework\TestCase;

final class AccessPrivatePropertyTest extends TestCase
{

    public function testInaccessibleProperty(): void
    {
        $object = new class () {
            private string $secretValue = 'hidden';
        };

        $value = ReflectionHelper::inaccessibleProperty($object, 'secretValue');

        self::assertSame('hidden', $value);
    }
}
```

#### Invoking protected methods

```php
<?php

declare(strict_types=1);

use PHPForge\Support\ReflectionHelper;
use PHPUnit\Framework\TestCase;

final class InvokeProtectedMethodTest extends TestCase
{

    public function testInvokeMethod(): void
    {
        $object = new class () {
            protected function calculate(int $a, int $b): int
            {
                return $a + $b;
            }
        };

        $result = ReflectionHelper::invokeMethod($object, 'calculate', [5, 3]);

        self::assertSame(8, $result);
    }
}
```

#### Normalize line endings

```php
<?php

declare(strict_types=1);

use PHPForge\Support\LineEndingNormalizer;
use PHPUnit\Framework\TestCase;

final class NormalizeLineEndingsTest extends TestCase
{

    public function testNormalizedComparison(): void
    {
        self::assertSame(
            LineEndingNormalizer::normalize("Foo\r\nBar"),
            LineEndingNormalizer::normalize("Foo\nBar"),
        );
    }
}
```

#### Remove files from directory

```php
<?php

declare(strict_types=1);

use PHPForge\Support\DirectoryCleaner;
use PHPUnit\Framework\TestCase;

final class RemoveFilesFromDirectoryTest extends TestCase
{

    public function testCleanup(): void
    {
        $testDir = sys_get_temp_dir() . '/php-forge-support-' . bin2hex(random_bytes(8));
        mkdir($testDir);

        try {
            file_put_contents($testDir . '/artifact.txt', 'test');
            file_put_contents($testDir . '/.gitignore', "*\n");

            DirectoryCleaner::clean($testDir);

            self::assertFileDoesNotExist($testDir . '/artifact.txt');
            self::assertFileExists($testDir . '/.gitignore');
        } finally {
            @unlink($testDir . '/.gitignore');
            @rmdir($testDir);
        }
    }
}
```

#### Set inaccessible property

```php
<?php

declare(strict_types=1);

use PHPForge\Support\ReflectionHelper;
use PHPUnit\Framework\TestCase;

final class SetInaccessiblePropertyTest extends TestCase
{

    public function testSetProperty(): void
    {
        $object = new class () {
            private string $config = 'default';
        };

        ReflectionHelper::setInaccessibleProperty($object, 'config', 'test-mode');

        $newValue = ReflectionHelper::inaccessibleProperty($object, 'config');

        self::assertSame('test-mode', $newValue);
    }
}
```

#### Enum data provider

Use `PHPForge\Support\EnumDataProvider` to build deterministic datasets from `UnitEnum::cases()` and normalized enum values.

##### Attribute fragment output (HTML)

```php
<?php

declare(strict_types=1);

namespace App\Tests;

use PHPForge\Support\EnumDataProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use UnitEnum;

enum Size
{
    case Small;
    case Large;
}

final class EnumDataProviderHtmlTest extends TestCase
{
    public static function provideEnumAttributes(): array
    {
        return EnumDataProvider::attributeCases(Size::class, 'data-size', true);
    }

    #[DataProvider('provideEnumAttributes')]
    public function testBuildsAttributeFragment(UnitEnum $case, array $args, string $expected, string $message): void
    {
        $normalized = $case->name;
        $attributeFragment = " data-size=\"{$normalized}\"";

        self::assertSame($expected, $attributeFragment, $message);
    }
}
```

##### Enum instance output (non-HTML)

```php
<?php

declare(strict_types=1);

namespace App\Tests;

use PHPForge\Support\EnumDataProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use UnitEnum;

enum State
{
    case Active;
    case Disabled;
}

final class EnumDataProviderEnumTest extends TestCase
{
    public static function provideEnumInstances(): array
    {
        return EnumDataProvider::attributeCases(State::class, 'state', false);
    }

    #[DataProvider('provideEnumInstances')]
    public function testReturnsEnumInstance(UnitEnum $case, array $args, UnitEnum $expected, string $message): void
    {
        self::assertSame($case, $expected, $message);
    }
}
```

##### Tag cases

```php
<?php

declare(strict_types=1);

namespace App\Tests;

use PHPForge\Support\EnumDataProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

enum Heading
{
    case H1;
    case H2;
}

final class EnumDataProviderTagCasesTest extends TestCase
{
    public static function provideTags(): array
    {
        return EnumDataProvider::tagCases(Heading::class, 'heading');
    }

    #[DataProvider('provideTags')]
    public function testProvidesNormalizedTag(Heading $case, string $normalized): void
    {
        self::assertSame($case->name, $normalized);
    }
}
```

## Documentation

For detailed configuration options and advanced usage.

- 🧪 [Testing Guide](docs/testing.md)
- 🛠️ [Development Guide](docs/development.md)

## Package information

[![PHP](https://img.shields.io/badge/%3E%3D8.1-777BB4.svg?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/releases/8.1/en.php)
[![Latest Stable Version](https://img.shields.io/packagist/v/php-forge/support.svg?style=for-the-badge&logo=packagist&logoColor=white&label=Stable)](https://packagist.org/packages/php-forge/support)
[![Total Downloads](https://img.shields.io/packagist/dt/php-forge/support.svg?style=for-the-badge&logo=composer&logoColor=white&label=Downloads)](https://packagist.org/packages/php-forge/support)

## Quality code

[![Codecov](https://img.shields.io/codecov/c/github/php-forge/support.svg?style=for-the-badge&logo=codecov&logoColor=white&label=Coverage)](https://codecov.io/github/php-forge/support)
[![PHPStan Level Max](https://img.shields.io/badge/PHPStan-Level%20Max-4F5D95.svg?style=for-the-badge&logo=github&logoColor=white)](https://github.com/php-forge/support/actions/workflows/static.yml)
[![Super-Linter](https://img.shields.io/github/actions/workflow/status/php-forge/support/linter.yml?style=for-the-badge&label=Super-Linter&logo=github)](https://github.com/php-forge/support/actions/workflows/linter.yml)
[![StyleCI](https://img.shields.io/badge/StyleCI-Passed-44CC11.svg?style=for-the-badge&logo=github&logoColor=white)](https://github.styleci.io/repos/779611775?branch=main)

## Our social networks

[![Follow on X](https://img.shields.io/badge/-Follow%20on%20X-1DA1F2.svg?style=for-the-badge&logo=x&logoColor=white&labelColor=000000)](https://x.com/Terabytesoftw)

## License

[![License](https://img.shields.io/badge/License-BSD--3--Clause-brightgreen.svg?style=for-the-badge&logo=opensourceinitiative&logoColor=white&labelColor=555555)](LICENSE)
