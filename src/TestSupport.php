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
 * Test support utilities for PHPUnit suites.
 *
 * Provides reflection-based helpers to read and write non-public properties and invoke non-public methods, including
 * members declared on parent classes.
 *
 * Also provides helpers for normalizing line endings to `"\n"` and for removing files from directories while
 * preserving `.gitignore` and `.gitkeep`.
 *
 * @copyright Copyright (C) 2025 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
trait TestSupport
{
    /**
     * Retrieves a property value from a specified class context.
     *
     * Uses reflection to read `$propertyName` from `$object` using `$className` as the declaring class context.
     *
     * @param object $object Object instance from which to retrieve the property value.
     * @param object|string $className Name or instance of the class that declares the property.
     * @param string $propertyName Name of the property to access.
     *
     * @throws ReflectionException
     *
     * @return mixed Value of the specified property.
     *
     * @phpstan-param class-string|object $className
     */
    private static function inaccessibleParentProperty(
        object $object,
        string|object $className,
        string $propertyName,
    ): mixed {
        $class = new ReflectionClass($className);

        return $class->getProperty($propertyName)->getValue($object);
    }

    /**
     * Retrieves a property value from a class or object.
     *
     * Uses reflection to read `$propertyName` from the provided class name or object instance.
     *
     * @param object|string $object Name of the class or object instance from which to retrieve the property value.
     * @param string $propertyName Name of the property to access.
     *
     * @throws ReflectionException if the property does not exist.
     *
     * @return mixed Value of the specified property, or `null` when `$propertyName` is empty.
     *
     * @phpstan-param class-string|object $object
     */
    private static function inaccessibleProperty(string|object $object, string $propertyName): mixed
    {
        $class = new ReflectionClass($object);

        if ($propertyName !== '') {
            $property = $class->getProperty($propertyName);
            $result = is_string($object) ? $property->getValue() : $property->getValue($object);
        }

        return $result ?? null;
    }

    /**
     * Invokes a method via reflection.
     *
     * Uses reflection to invoke `$method` on `$object` with `$args`.
     *
     * @param object $object Object instance containing the method to invoke.
     * @param string $method Name of the method to invoke.
     * @param array $args Arguments to pass to the method invocation.
     *
     * @throws ReflectionException if the method does not exist.
     *
     * @return mixed Value of the invoked method, or `null` when `$method` is empty.
     *
     * @phpstan-param array<array-key, mixed> $args
     */
    private static function invokeMethod(object $object, string $method, array $args = []): mixed
    {
        $reflection = new ReflectionObject($object);

        if ($method !== '') {
            $method = $reflection->getMethod($method);
            $result = $method->invokeArgs($object, $args);
        }

        return $result ?? null;
    }

    /**
     * Invokes a parent class method via reflection.
     *
     * Uses `$parentClass` as the declaring class context to invoke `$method` on `$object` with `$args`.
     *
     * @param object $object Object instance containing the method to invoke.
     * @param string $parentClass Name of the parent class containing the method.
     * @param string $method Name of the method to invoke.
     * @param array $args Arguments to pass to the method invocation.
     *
     * @throws ReflectionException if the method does not exist.
     *
     * @return mixed Value of the invoked method, or `null` when `$method` is empty.
     *
     * @phpstan-param class-string $parentClass
     * @phpstan-param array<array-key, mixed> $args
     */
    private static function invokeParentMethod(
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
     * Normalizes line endings to `"\n"` for cross-platform string assertions.
     *
     * Converts `"\r\n"` and `"\r"` to `"\n"` to keep string comparisons deterministic across operating systems.
     *
     * @param string $line Input string potentially containing Windows style line endings.
     *
     * @return string String with normalized line endings.
     */
    private static function normalizeLineEndings(string $line): string
    {
        return str_replace(["\r\n", "\r"], "\n", $line);
    }

    /**
     * Removes directory contents recursively while preserving `.gitignore` and `.gitkeep`.
     *
     * Iterates through `$basePath` and removes all files and subdirectories except `.`, `..`, `.gitignore`, and
     * `.gitkeep`. Subdirectories are processed recursively before removal.
     *
     * File and directory removals are attempted with error suppression.
     *
     * @param string $basePath Absolute path to the directory whose contents will be removed.
     *
     * @throws RuntimeException if the directory cannot be opened for reading.
     */
    private static function removeFilesFromDirectory(string $basePath): void
    {
        $handle = @opendir($basePath);

        if ($handle === false) {
            $dirname = basename($basePath);
            throw new RuntimeException("Unable to open directory: $dirname");
        }

        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            if ($file === '.gitignore' || $file === '.gitkeep') {
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
     * Sets a parent class property value via reflection.
     *
     * Uses `$parentClass` as the declaring class context to assign `$value` to `$propertyName` on `$object`.
     *
     * This method is a no-op when `$propertyName` is empty.
     *
     * @param object $object Object instance whose parent property will be set.
     * @param string $parentClass Name of the parent class containing the property.
     * @param string $propertyName Name of the property to set.
     * @param mixed $value Value to assign to the property.
     *
     * @throws ReflectionException if the property does not exist.
     *
     * @phpstan-param class-string $parentClass
     */
    private static function setInaccessibleParentProperty(
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
     * Sets a property value via reflection.
     *
     * Assigns `$value` to `$propertyName` on `$object`.
     *
     * This method is a no-op when `$propertyName` is empty.
     *
     * @param object $object Object instance whose property will be set.
     * @param string $propertyName Name of the property to set.
     * @param mixed $value Value to assign to the property.
     *
     * @throws ReflectionException if the property does not exist.
     */
    private static function setInaccessibleProperty(
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
