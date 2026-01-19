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

**Advanced Reflection Utilities**
- Access and modify private and protected properties via reflection.
- Invoke inaccessible methods to expand testing coverage.

**Cross-Platform String Assertions**
- Avoid false positives and negatives caused by Windows vs. Unix line ending differences.
- Normalize line endings for consistent string comparisons across platforms.

**File System Test Management**
- Recursively clean files and directories for isolated test environments.
- Safe removal that preserves Git-tracking files (for example, `.gitignore`, `.gitkeep`).

**Enum Test Data Generation**
- Generate structured, deterministic datasets for enum-based attribute scenarios via `PHPForge\Support\EnumDataGenerator`.

### Installation

```bash
composer require php-forge/support:^0.2 --dev
```

### Quick start

This package provides the `PHPForge\Support\TestSupport` trait for PHPUnit tests.

It supports reflection-based access to non-public members, deterministic string comparisons across platforms, and filesystem cleanup for isolated test environments.

#### Accessing private properties

```php
<?php

declare(strict_types=1);

use PHPForge\Support\TestSupport;
use PHPUnit\Framework\TestCase;

final class AccessPrivatePropertyTest extends TestCase
{
    use TestSupport;

    public function testInaccessibleProperty(): void
    {
        $object = new class () {
            private string $secretValue = 'hidden';
        };

        $value = self::inaccessibleProperty($object, 'secretValue');

        self::assertSame('hidden', $value);
    }
}
```

#### Invoking protected methods

```php
<?php

declare(strict_types=1);

use PHPForge\Support\TestSupport;
use PHPUnit\Framework\TestCase;

final class InvokeProtectedMethodTest extends TestCase
{
    use TestSupport;

    public function testInvokeMethod(): void
    {
        $object = new class () {
            protected function calculate(int $a, int $b): int
            {
                return $a + $b;
            }
        };

        $result = self::invokeMethod($object, 'calculate', [5, 3]);

        self::assertSame(8, $result);
    }
}
```

#### Normalize line endings

```php
<?php

declare(strict_types=1);

use PHPForge\Support\TestSupport;
use PHPUnit\Framework\TestCase;

final class NormalizeLineEndingsTest extends TestCase
{
    use TestSupport;

    public function testNormalizedComparison(): void
    {
        self::assertSame(
            self::normalizeLineEndings("Foo\r\nBar"),
            self::normalizeLineEndings("Foo\nBar"),
        );
    }
}
```

#### Remove files from directory

```php
<?php

declare(strict_types=1);

use PHPForge\Support\TestSupport;
use PHPUnit\Framework\TestCase;

final class RemoveFilesFromDirectoryTest extends TestCase
{
    use TestSupport;

    public function testCleanup(): void
    {
        $testDir = sys_get_temp_dir() . '/php-forge-support-' . bin2hex(random_bytes(8));
        mkdir($testDir);

        try {
            file_put_contents($testDir . '/artifact.txt', 'test');
            file_put_contents($testDir . '/.gitignore', "*\n");

            self::removeFilesFromDirectory($testDir);

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

use PHPForge\Support\TestSupport;
use PHPUnit\Framework\TestCase;

final class SetInaccessiblePropertyTest extends TestCase
{
    use TestSupport;

    public function testSetProperty(): void
    {
        $object = new class () {
            private string $config = 'default';
        };

        self::setInaccessibleProperty($object, 'config', 'test-mode');

        $newValue = self::inaccessibleProperty($object, 'config');

        self::assertSame('test-mode', $newValue);
    }
}
```

#### Enum data generator

Use `PHPForge\Support\EnumDataGenerator` to build deterministic datasets from `UnitEnum::cases()` and `UIAwesome\Html\Helper\Enum::normalizeValue()`.

##### Attribute fragment output (HTML)

```php
<?php

declare(strict_types=1);

namespace App\Tests;

use PHPForge\Support\EnumDataGenerator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use UIAwesome\Html\Helper\Enum;
use UnitEnum;

enum Size
{
    case Small;
    case Large;
}

final class EnumDataGeneratorHtmlTest extends TestCase
{
    public static function provideEnumAttributes(): array
    {
        return EnumDataGenerator::cases(Size::class, 'data-size', true);
    }

    #[DataProvider('provideEnumAttributes')]
    public function testBuildsAttributeFragment(UnitEnum $case, array $args, string $expected, string $message): void
    {
        $normalized = (string) Enum::normalizeValue($case);
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

use PHPForge\Support\EnumDataGenerator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use UnitEnum;

enum State
{
    case Active;
    case Disabled;
}

final class EnumDataGeneratorEnumTest extends TestCase
{
    public static function provideEnumInstances(): array
    {
        return EnumDataGenerator::cases(State::class, 'state', false);
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

use PHPForge\Support\EnumDataGenerator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use UIAwesome\Html\Helper\Enum;

enum Heading
{
    case H1;
    case H2;
}

final class EnumDataGeneratorTagCasesTest extends TestCase
{
    public static function provideTags(): array
    {
        return EnumDataGenerator::tagCases(Heading::class, 'heading');
    }

    #[DataProvider('provideTags')]
    public function testProvidesNormalizedTag(Heading $case, string $normalized): void
    {
        self::assertSame((string) Enum::normalizeValue($case), $normalized);
    }
}
```

## Documentation

For detailed configuration options and advanced usage.

- [Testing Guide](docs/testing.md)
- [Development Guide](docs/development.md)

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
