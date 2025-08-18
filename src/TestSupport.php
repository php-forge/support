<?php

declare(strict_types=1);

namespace PHPForge\Support;

use ReflectionClass;
use ReflectionException;
use ReflectionObject;
use RuntimeException;

use function basename;
use function closedir;
use function is_dir;
use function is_string;
use function opendir;
use function readdir;
use function rmdir;
use function str_replace;
use function unlink;

/**
 * Trait providing utilities for testing inaccessible properties, methods, and filesystem cleanup.
 *
 * Supplies static helper methods for normalizing line endings, accessing or modifying private/protected properties and
 * methods (including those inherited from parent classes), invoking inaccessible methods, and recursively removing
 * files from directories.
 *
 * These utilities are designed to facilitate comprehensive unit testing by enabling assertions and manipulations that
 * would otherwise be restricted by visibility constraints or platform differences.
 *
 * @copyright Copyright (C) 2025 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
trait TestSupport
{
    /**
     * Retrieves the value of an inaccessible property from a parent class instance.
     *
     * Uses reflection to access the specified property of the given parent class, allowing tests to inspect or assert
     * the value of private or protected properties inherited from parent classes.
     *
     * This method is useful for verifying the internal state of objects in inheritance scenarios where direct access
     * to parent properties is not possible.
     *
     * @param object $object Object instance from which to retrieve the property value.
     * @param object|string $className Name or instance of the parent class containing the property.
     * @param string $propertyName Name of the property to access.
     *
     * @throws ReflectionException
     *
     * @return mixed Value of the specified parent property.
     *
     * @phpstan-param class-string|object $className
     */
    public static function inaccessibleParentProperty(
        object $object,
        string|object $className,
        string $propertyName,
    ): mixed {
        $class = new ReflectionClass($className);

        return $class->getProperty($propertyName)->getValue($object);
    }

    /**
     * Retrieves the value of an inaccessible property from an object or class instance.
     *
     * Uses reflection to access the specified property of the given object or class, allowing tests to inspect or
     * assert the value of private or protected properties that are otherwise inaccessible.
     *
     * This method is useful for verifying the internal state of objects during testing, especially when direct access
     * to the property is not possible due to visibility constraints.
     *
     * @param object|string $object Name of the class or object instance from which to retrieve the property value.
     * @param string $propertyName Name of the property to access.
     *
     * @throws ReflectionException if the property does not exist or is inaccessible.
     *
     * @return mixed Value of the specified property, or `null` if the property name is empty.
     *
     * @phpstan-param class-string|object $object
     */
    public static function inaccessibleProperty(string|object $object, string $propertyName): mixed
    {
        $class = new ReflectionClass($object);

        if ($propertyName !== '') {
            $property = $class->getProperty($propertyName);
            $result = is_string($object) ? $property->getValue() : $property->getValue($object);
        }

        return $result ?? null;
    }

    /**
     * Invokes an inaccessible method on the given object instance with the specified arguments.
     *
     * Uses reflection to access and invoke a private or protected method of the provided object, allowing tests to
     * execute logic that is not publicly accessible.
     *
     * This is useful for verifying internal behavior or side effects during unit testing.
     *
     * @param object $object Object instance containing the method to invoke.
     * @param string $method Name of the method to invoke.
     * @param array $args Arguments to pass to the method invocation.
     *
     * @throws ReflectionException if the method does not exist or is inaccessible.
     *
     * @return mixed Value of the invoked method, or `null` if the method name is empty.
     *
     * @phpstan-param array<array-key, mixed> $args
     */
    public static function invokeMethod(object $object, string $method, array $args = []): mixed
    {
        $reflection = new ReflectionObject($object);

        if ($method !== '') {
            $method = $reflection->getMethod($method);
            $result = $method->invokeArgs($object, $args);
        }

        return $result ?? null;
    }

    /**
     * Invokes an inaccessible method from a parent class on the given object instance with the specified arguments.
     *
     * Uses reflection to access and invoke a private or protected method defined in the specified parent class,
     * allowing tests to execute logic that is not publicly accessible from the child class.
     *
     * This is useful for verifying inherited behavior or side effects during unit testing of subclasses.
     *
     * @param object $object Object instance containing the method to invoke.
     * @param string $parentClass Name of the parent class containing the method.
     * @param string $method Name of the method to invoke.
     * @param array $args Arguments to pass to the method invocation.
     *
     * @throws ReflectionException if the method does not exist or is inaccessible in the parent class.
     *
     * @return mixed Value of the invoked method, or `null` if the method name is empty.
     *
     * @phpstan-param class-string $parentClass
     * @phpstan-param array<array-key, mixed> $args
     */
    public static function invokeParentMethod(
        object $object,
        string $parentClass,
        string $method,
        array $args = [],
    ): mixed {
        $reflection = new ReflectionClass($parentClass);

        if ($method !== '') {
            $method = $reflection->getMethod($method);
            $result = $method->invokeArgs($object, $args);
        }

        return $result ?? null;
    }

    /**
     * Normalizes line endings to unix style ('\n') for cross-platform string assertions.
     *
     * Converts windows style ('\r\n') line endings to unix style ('\n') to ensure consistent string comparisons across
     * different operating systems during testing.
     *
     * This method is useful for eliminating false negatives in assertions caused by platform-specific line endings.
     *
     * @param string $line Input string potentially containing windows style line endings.
     *
     * @return string String with normalized unix style line endings.
     */
    public static function normalizeLineEndings(string $line): string
    {
        return str_replace("\r\n", "\n", $line);
    }

    /**
     * Removes all files and directories recursively from the specified base path, excluding '.gitignore' and
     * '.gitkeep'.
     *
     * Opens the given directory, iterates through its contents, and removes all files and subdirectories except for
     * special entries ('.', '..', '.gitignore', '.gitkeep').
     *
     * Subdirectories are processed recursively before removal.
     *
     * @param string $basePath Absolute path to the directory whose contents will be removed.
     *
     * @throws RuntimeException if the directory cannot be opened for reading.
     */
    public static function removeFilesFromDirectory(string $basePath): void
    {
        $handle = @opendir($basePath);

        if ($handle === false) {
            $dirname = basename($basePath);
            throw new RuntimeException("Unable to open directory: $dirname");
        }

        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..' || $file === '.gitignore' || $file === '.gitkeep') {
                continue;
            }

            $path = $basePath . DIRECTORY_SEPARATOR . $file;

            if (is_dir($path)) {
                self::removeFilesFromDirectory($path);
                @rmdir($path);
            } else {
                @unlink($path);
            }
        }

        closedir($handle);
    }

    /**
     * Sets the value of an inaccessible property on a parent class instance.
     *
     * Uses reflection to assign the specified value to a private or protected property defined in the given parent
     * class, enabling tests to modify internal state that is otherwise inaccessible due to visibility constraints in
     * inheritance scenarios.
     *
     * This method is useful for testing scenarios that require direct manipulation of parent class internals.
     *
     * @param object $object Object instance whose parent property will be set.
     * @param string $parentClass Name of the parent class containing the property.
     * @param string $propertyName Name of the property to set.
     * @param mixed $value Value to assign to the property.
     *
     * @throws ReflectionException if the property does not exist or is inaccessible in the parent class.
     *
     * @phpstan-param class-string $parentClass
     */
    public static function setInaccessibleParentProperty(
        object $object,
        string $parentClass,
        string $propertyName,
        mixed $value,
    ): void {
        $class = new ReflectionClass($parentClass);

        if ($propertyName !== '') {
            $property = $class->getProperty($propertyName);
            $property->setValue($object, $value);
        }

        unset($class, $property);
    }

    /**
     * Sets the value of an inaccessible property on the given object instance.
     *
     * Uses reflection to assign the specified value to a private or protected property of the provided object enabling
     * tests to modify internal state that is otherwise inaccessible due to visibility constraints.
     *
     * This method is useful for testing scenarios that require direct manipulation of object internals.
     *
     * @param object $object Object instance whose property will be set.
     * @param string $propertyName Name of the property to set.
     * @param mixed $value Value to assign to the property.
     *
     * @throws ReflectionException if the property does not exist or is inaccessible.
     */
    public static function setInaccessibleProperty(
        object $object,
        string $propertyName,
        mixed $value,
    ): void {
        $class = new ReflectionClass($object);

        if ($propertyName !== '') {
            $property = $class->getProperty($propertyName);
            $property->setValue($object, $value);
        }

        unset($class, $property);
    }
}
