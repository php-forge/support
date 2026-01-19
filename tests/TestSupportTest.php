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

    public function testNormalizeLineEndingsReturnsUnchangedStringWhenNoNormalizationNeeded(): void
    {
        self::assertSame(
            "foo\nbar",
            self::normalizeLineEndings("foo\nbar"),
            'Should return the original string when no normalization is required.',
        );
    }

    public function testNormalizeLineEndingsWhenStringsAreIdenticalWithLineEndings(): void
    {
        self::assertSame(
            "foo\nbar",
            self::normalizeLineEndings("foo\r\nbar"),
            "Should normalize 'CRLF' inputs to 'LF'.",
        );
    }

    public function testRemoveFilesFromDirectoryRemovesAllFiles(): void
    {
        $dir = sys_get_temp_dir() . '/php-forge-support-' . bin2hex(random_bytes(8));
        mkdir($dir);

        try {
            mkdir("{$dir}/subdir");
            touch("{$dir}/test.txt");
            touch("{$dir}/subdir/test.txt");
            touch("{$dir}/.gitignore");
            touch("{$dir}/.gitkeep");

            self::removeFilesFromDirectory($dir);

            self::assertFileDoesNotExist(
                "{$dir}/test.txt",
                "File 'test.txt' should not exist after 'removeFilesFromDirectory' method is called.",
            );
            self::assertFileDoesNotExist(
                "{$dir}/subdir/test.txt",
                "File 'subdir/test.txt' should not exist after 'removeFilesFromDirectory' method is called.",
            );
            self::assertFileExists(
                "{$dir}/.gitignore",
                "File '.gitignore' should remain after 'removeFilesFromDirectory' method is called.",
            );
            self::assertFileExists(
                "{$dir}/.gitkeep",
                "File '.gitkeep' should remain after 'removeFilesFromDirectory' method is called.",
            );
        } finally {
            @unlink("{$dir}/.gitignore");
            @unlink("{$dir}/.gitkeep");
            @rmdir($dir);
        }
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
        self::assertSame(
            'value',
            self::invokeMethod(new TestClass(), 'inaccessibleMethod'),
            "Should return 'value' when invoking the inaccessible method 'inaccessibleParentMethod' on 'TestClass' "
            . 'via reflection.',
        );
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnValueWhenInvokingInaccessibleParentMethod(): void
    {
        self::assertSame(
            'valueParent',
            self::invokeParentMethod(
                new TestClass(),
                TestBaseClass::class,
                'inaccessibleParentMethod',
            ),
            "Should return 'valueParent' when invoking the inaccessible parent method 'inaccessibleParentMethod' on "
            . "'TestClass' via reflection.",
        );
    }

    /**
     * @throws ReflectionException
     */
    public function testSetInaccessibleParentProperty(): void
    {
        $object = new TestClass();

        self::setInaccessibleParentProperty($object, TestBaseClass::class, 'propertyParent', 'foo');

        self::assertSame(
            'foo',
            self::inaccessibleParentProperty($object, TestBaseClass::class, 'propertyParent'),
            "Should return 'value' when invoking the inaccessible method 'inaccessibleMethod' on 'TestClass' "
            . "'setInaccessibleParentProperty' method.",
        );
    }

    /**
     * @throws ReflectionException
     */
    public function testSetInaccessiblePropertySetsValueCorrectly(): void
    {
        $object = new TestClass();

        self::setInaccessibleProperty($object, 'property', 'foo');

        self::assertSame(
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
