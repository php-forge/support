<p align="center">
    <a href="https://github.com/php-forge/support" target="_blank">
        <img src="https://avatars.githubusercontent.com/u/103309199?s%25253D400%252526u%25253Dca3561c692f53ed7eb290d3bb226a2828741606f%252526v%25253D4" height="100px">
    </a>
    <h1 align="center">Support.</h1>
    <br>
</p>

<p align="center">
    <a href="https://www.php.net/releases/8.1/en.php" target="_blank">
        <img src="https://img.shields.io/badge/PHP-%3E%3D8.1-787CB5" alt="PHP Version">
    </a>
    <a href="https://github.com/php-forge/support/actions/workflows/build.yml" target="_blank">
        <img src="https://github.com/php-forge/support/actions/workflows/build.yml/badge.svg" alt="PHPUnit">
    </a>
    <a href="https://dashboard.stryker-mutator.io/reports/github.com/php-forge/support/main" target="_blank">
        <img src="https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fphp-forge%2Fsupport%2Fmain" alt="Infection">
    </a>        
        <a href="https://github.com/php-forge/support/actions/workflows/static.yml" target="_blank">        
        <img src="https://github.com/php-forge/support/actions/workflows/static.yml/badge.svg" alt="Static Analysis">
    </a>  
</p>

## Features

✅ **Advanced Reflection Utilities**
- Access and modify private/protected properties via reflection.
- Invoke inaccessible methods for comprehensive testing coverage.

✅ **Cross-Platform String Assertions**
- Eliminate false negatives from Windows/Unix line ending differences.
- Normalize line endings for consistent string comparisons across platforms.

✅ **File System Test Management**
- Recursive file and directory cleanup for isolated test environments.
- Safe removal operations preserving Git tracking files.

## Quick start

### System requirements

- [`PHP`](https://www.php.net/downloads) 8.1 or higher.
- [`Composer`](https://getcomposer.org/download/) for dependency management.
- [`PHPUnit`](https://phpunit.de/) for testing framework integration.

### Installation

#### Method 1: Using [Composer](https://getcomposer.org/download/) (recommended)

Install the extension.

```bash
composer require --dev --prefer-dist php-forge/support:^0.1
```

#### Method 2: Manual installation

Add to your `composer.json`.

```json
{
    "require-dev": {
        "php-forge/support": "^0.1"
    }
}
```

Then run.

```bash
composer update
```

## Basic Usage

### Accessing private properties

```php
<?php

declare(strict_types=1);

use PHPForge\Support\Assert;

$object = new class () {
    private string $secretValue = 'hidden';
};

// access private properties for testing
$value = Assert::inaccessibleProperty($object, 'secretValue');

self::assertSame('hidden', $value);
```

### Equals without line ending

```php
<?php

declare(strict_types=1);

use PHPForge\Support\Assert;

// normalize line endings for consistent comparisons
Assert::equalsWithoutLE(
    "Foo\r\nBar",
    "Foo\nBar",
    "Should match regardless of line ending style"
);
```

### Invoking protected methods

```php
<?php

declare(strict_types=1);

use PHPForge\Support\Assert;

$object = new class () {
    protected function calculate(int $a, int $b): int
    {
        return $a + $b;
    }
};

// test protected method behavior
$result = Assert::invokeMethod($object, 'calculate', [5, 3]);

self::assertSame(8, $result);
```

### Remove files from directory

```php
<?php

declare(strict_types=1);

use PHPForge\Support\Assert;

$testDir = dirname(__DIR__) . '/runtime';

// clean up test artifacts (preserves '.gitignore' and '.gitkeep')
Assert::removeFilesFromDirectory($testDir);
```

### Set inaccessible property

```php
<?php

declare(strict_types=1);

use PHPForge\Support\Assert;

$object = new class () {
    private string $config = 'default';
};

// set private property for testing scenarios
Assert::setInaccessibleProperty($object, 'config', 'test-mode');

$newValue = Assert::inaccessibleProperty($object, 'config');

self::assertSame('test-mode', $newValue);
```

## Documentation

For comprehensive testing guidance, see:

- 🧪 [Testing Guide](docs/testing.md)

## Quality code

[![Latest Stable Version](https://poser.pugx.org/php-forge/support/v)](https://github.com/php-forge/support/releases)
[![Total Downloads](https://poser.pugx.org/php-forge/support/downloads)](https://packagist.org/packages/php-forge/support)
[![codecov](https://codecov.io/gh/php-forge/support/graph/badge.svg?token=Upc4yA23YN)](https://codecov.io/gh/php-forge/support)
[![phpstan-level](https://img.shields.io/badge/PHPStan%20level-max-blue)](https://github.com/php-forge/support/actions/workflows/static.yml)
[![StyleCI](https://github.styleci.io/repos/661073468/shield?branch=main)](https://github.styleci.io/repos/661073468?branch=main)

## Our social networks

[![X](https://img.shields.io/badge/follow-@terabytesoftw-1DA1F2?logo=x&logoColor=1DA1F2&labelColor=555555&style=flat)](https://x.com/Terabytesoftw)

## License

[![License](https://img.shields.io/github/license/php-forge/support?cacheSeconds=0)](LICENSE.md)
