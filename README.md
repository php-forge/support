<p align="center">
    <a href="https://github.com/php-forge/support" target="_blank">
        <img src="https://avatars.githubusercontent.com/u/103309199?s%25253D400%252526u%25253Dca3561c692f53ed7eb290d3bb226a2828741606f%252526v%25253D4" height="100px">
    </a>
    <h1 align="center">Support.</h1>
    <br>
</p>

<p align="center">
    <a href="https://github.com/php-forge/support/actions/workflows/build.yml" target="_blank">
        <img src="https://github.com/php-forge/support/actions/workflows/build.yml/badge.svg" alt="PHPUnit">
    </a>
    <a href="https://codecov.io/gh/php-forge/support" target="_blank">
        <img src="https://codecov.io/gh/php-forge/support/branch/main/graph/badge.svg?token=MF0XUGVLYC" alt="Codecov">
    </a>
    <a href="https://dashboard.stryker-mutator.io/reports/github.com/php-forge/support/main" target="_blank">
        <img src="https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyii2-extensions%2Fasset-bootstrap5%2Fmain" alt="Infection">
    </a>
    <a href="https://github.com/php-forge/support/actions/workflows/static.yml" target="_blank">
        <img src="https://github.com/php-forge/support/actions/workflows/static.yml/badge.svg" alt="Psalm">
    </a>
    <a href="https://shepherd.dev/github/php-forge/support" target="_blank">
        <img src="https://shepherd.dev/github/php-forge/support/coverage.svg" alt="Psalm Coverage">
    </a>
    <a href="https://github.styleci.io/repos/661073468?branch=main" target="_blank">
        <img src="https://github.styleci.io/repos/661073468/shield?branch=main" alt="Style ci">
    </a>           
</p>

## Installation

The preferred way to install this extension is through [composer](https://getcomposer.org/download/).

Either run

```shell
composer require --prefer-dist php-forge/support
```

or add

```json
"php-forge/support": "^0.1"
```

to the require-dev section of your `composer.json` file. 

## Usage

### Equals without line ending

```php
<?php

declare(strict_types=1);

namespace PHPForge\Support\Tests;

use PHPForge\Support\Assert;

Assert::equalsWithoutLE(
    <<<Text
    Foo
    Bar
    Text,
    "Foo\nBar"
);
```

### Inaccessible property

```php
<?php

declare(strict_types=1);

namespace PHPForge\Support\Tests;

use PHPForge\Support\Assert;

$object = new class () {
    private string $foo = 'bar';
};

$this->assertSame('bar', Assert::inaccessibleProperty($object, 'foo'));
```

### Invoke method

```php
<?php

declare(strict_types=1);

namespace PHPForge\Support\Tests;

use PHPForge\Support\Assert;

$object = new class () {
    protected function foo(): string
    {
        return 'foo';
    }
};

$this->assertSame('foo', Assert::invokeMethod($object, 'foo'));
```

### Set inaccessible property

```php
<?php

declare(strict_types=1);

namespace PHPForge\Support\Tests;

use PHPForge\Support\Assert;

$object = new class () {
    private string $foo = 'bar';
};

Assert::setInaccessibleProperty($object, 'foo', 'baz');

$this->assertSame('baz', Assert::inaccessibleProperty($object, 'foo'));
```

### Remove files from directory

```php
<?php

declare(strict_types=1);

namespace PHPForge\Support\Tests;

use PHPForge\Support\Assert;

$dir = __DIR__ . '/runtime';

mkdir($dir);
mkdir($dir . '/subdir');
touch($dir . '/test.txt');
touch($dir . '/subdir/test.txt');

Assert::removeFilesFromDirectory($dir);

$this->assertFileDoesNotExist($dir . '/test.txt');

rmdir(__DIR__ . '/runtime');
```

## Support versions

[![PHP81](https://img.shields.io/badge/PHP-%3E%3D8.1-787CB5)](https://www.php.net/releases/8.1/en.php)
[![Yii30](https://img.shields.io/badge/Yii%20version-3.0-blue)](https://yiiframework.com)

## Testing

[Check the documentation testing](/docs/testing.md) to learn about testing.

## Our social networks

[![Twitter](https://img.shields.io/badge/twitter-follow-1DA1F2?logo=twitter&logoColor=1DA1F2&labelColor=555555?style=flat)](https://twitter.com/Terabytesoftw)

## License

The MIT License. Please see [License File](LICENSE.md) for more information.
