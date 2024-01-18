<?php

declare(strict_types=1);

namespace PHPForge\Support\Tests;

use PHPForge\Support\Assert;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class AssertTest extends TestCase
{
    public function testEqualsWithoutLE(): void
    {
        Assert::equalsWithoutLE(
            <<<Text
            Foo
            Bar
            Text,
            "Foo\nBar"
        );
    }

    public function testInaccessibleProperty(): void
    {
        $object = new class () {
            private string $foo = 'bar';
        };

        $this->assertSame('bar', Assert::inaccessibleProperty($object, 'foo'));
    }

    public function testInvokeMethod(): void
    {
        $object = new class () {
            protected function foo(): string
            {
                return 'foo';
            }
        };

        $this->assertSame('foo', Assert::invokeMethod($object, 'foo'));
    }

    public function testSetInaccessibleProperty(): void
    {
        $object = new class () {
            private string $foo = 'bar';
        };

        Assert::setInaccessibleProperty($object, 'foo', 'baz');

        $this->assertSame('baz', Assert::inaccessibleProperty($object, 'foo'));
    }

    public function testRemoveFilesFromDirectory(): void
    {
        $dir = __DIR__ . '/runtime';

        mkdir($dir);
        mkdir($dir . '/subdir');
        touch($dir . '/test.txt');
        touch($dir . '/subdir/test.txt');

        Assert::removeFilesFromDirectory($dir);

        $this->assertFileDoesNotExist($dir . '/test.txt');

        rmdir(__DIR__ . '/runtime');
    }

    public function testRemoveFilesFromDirectoryWithException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to open directory: non-existing-directory');

        Assert::removeFilesFromDirectory(__DIR__ . '/non-existing-directory');
    }
}
