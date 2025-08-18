<?php

declare(strict_types=1);

namespace PHPForge\Support\Tests;

use PHPForge\Support\Tests\Stub\{TestBaseClass, TestClass};
use PHPForge\Support\TestSupport;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use RuntimeException;

/**
 * Test suite for {@see TestSupport} trait utility methods.
 *
 * Verifies the behavior of utility methods provided by the {@see TestSupport} trait for testing inaccessible
 * properties, parent properties, and methods, as well as file system operations for test directories.
 *
 * These tests ensure correct access and mutation of private/protected members, invocation of inaccessible methods, and
 * robust file removal logic, including error handling for non-existent directories.
 *
 * Test coverage:
 * - Accessing and asserting values of inaccessible properties and parent properties.
 * - Ensuring correct exception handling for invalid operations.
 * - Invoking inaccessible methods and parent methods.
 * - Normalizing line endings.
 * - Removing files from directories and handling missing directories.
 * - Setting values for inaccessible properties and parent properties.
 *
 * @copyright Copyright (C) 2025 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class TestSupportTest extends TestCase
{
    use TestSupport;

    /**
     * @throws ReflectionException
     */
    public function testInaccessibleParentPropertyReturnsExpectedValue(): void
    {
        self::assertSame(
            'valueParent',
            self::inaccessibleParentProperty(
                new TestClass(),
                TestBaseClass::class,
                'propertyParent',
            ),
            "Should return the value of the parent property 'propertyParent' when accessed via reflection.",
        );
    }

    public function testNormalizeLineEndingsWhenStringsAreIdenticalWithLineEndings(): void
    {
        self::assertSame(
            self::normalizeLineEndings("foo\r\nbar"),
            self::normalizeLineEndings("foo\r\nbar"),
            "Should return 'true' when both strings are identical including line endings.",
        );
    }

    public function testRemoveFilesFromDirectoryRemovesAllFiles(): void
    {
        $dir = dirname(__DIR__) . '/runtime';

        mkdir("{$dir}/subdir");
        touch("{$dir}/test.txt");
        touch("{$dir}/subdir/test.txt");

        self::removeFilesFromDirectory($dir);

        $this->assertFileDoesNotExist(
            "{$dir}/test.txt",
            "File 'test.txt' should not exist after 'removeFilesFromDirectory' method is called.",
        );
        $this->assertFileDoesNotExist(
            "{$dir}/subdir/test.txt",
            "File 'subdir/test.txt' should not exist after 'removeFilesFromDirectory' method is called.",
        );
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnInaccessiblePropertyValueWhenPropertyIsPrivate(): void
    {
        self::assertSame(
            'value',
            self::inaccessibleProperty(new TestClass(), 'property'),
            "Should return the value of the private property 'property' when accessed via reflection.",
        );
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnValueWhenInvokingInaccessibleMethod(): void
    {
        $this->assertSame(
            'value',
            self::invokeMethod(new TestClass(), 'inaccessibleMethod'),
            "Should return 'value' when invoking the inaccessible method 'inaccessibleParentMethod' on 'TestClass' " .
            'via reflection.',
        );
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnValueWhenInvokingInaccessibleParentMethod(): void
    {
        $this->assertSame(
            'valueParent',
            self::invokeParentMethod(
                new TestClass(),
                TestBaseClass::class,
                'inaccessibleParentMethod',
            ),
            "Should return 'valueParent' when invoking the inaccessible parent method 'inaccessibleParentMethod' on " .
            "'TestClass' via reflection.",
        );
    }

    /**
     * @throws ReflectionException
     */
    public function testSetInaccessibleParentProperty(): void
    {
        $object = new TestClass();

        self::setInaccessibleParentProperty($object, TestBaseClass::class, 'propertyParent', 'foo');

        $this->assertSame(
            'foo',
            self::inaccessibleParentProperty($object, TestBaseClass::class, 'propertyParent'),
            "Should return 'foo' after setting the parent property 'propertyParent' via " .
            "'setInaccessibleParentProperty' method.",
        );
    }

    /**
     * @throws ReflectionException
     */
    public function testSetInaccessiblePropertySetsValueCorrectly(): void
    {
        $object = new TestClass();

        self::setInaccessibleProperty($object, 'property', 'foo');

        $this->assertSame(
            'foo',
            self::inaccessibleProperty($object, 'property'),
            "Should return 'foo' after setting the private property 'property' via 'setInaccessibleProperty' method.",
        );
    }

    public function testThrowRuntimeExceptionWhenRemoveFilesFromDirectoryNonExistingDirectory(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to open directory: non-existing-directory');

        self::removeFilesFromDirectory(__DIR__ . '/non-existing-directory');
    }
}
