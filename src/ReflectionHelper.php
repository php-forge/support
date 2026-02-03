<?php

declare(strict_types=1);

namespace PHPForge\Support;

use ReflectionClass;
use ReflectionException;
use ReflectionObject;

use function is_string;

/**
 * Reflection-based utilities for PHPUnit tests.
 *
 * Provides helper methods to read and write non-public properties and invoke non-public methods, including members
 * declared on parent classes.
 *
 * Key features.
 * - Invokes non-public methods via {@see ReflectionHelper::invokeMethod()} and
 *   {@see ReflectionHelper::invokeParentMethod()}.
 * - Reads non-public properties via {@see ReflectionHelper::inaccessibleProperty()} and
 *   {@see ReflectionHelper::inaccessibleParentProperty()}.
 * - Sets non-public properties via {@see ReflectionHelper::setInaccessibleProperty()} and
 *   {@see ReflectionHelper::setInaccessibleParentProperty()}.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class ReflectionHelper
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
    public static function inaccessibleParentProperty(
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
    }
}
