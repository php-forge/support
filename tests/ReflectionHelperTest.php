<?php

declare(strict_types=1);

namespace PHPForge\Support\Tests;

use PHPForge\Support\ReflectionHelper;
use PHPForge\Support\Tests\Stub\{TestBaseClass, TestClass};
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * Unit tests for {@see ReflectionHelper} reflection access behavior.
 *
 * Verifies reflection-based access and invocation of non-public members.
 *
 * Test coverage.
 * - Invokes non-public methods on instances and parent classes.
 * - Reads non-public properties from instances and parent classes.
 * - Sets non-public properties on instances and parent classes.
 *
 * {@see ReflectionHelper} for implementation details.
 * {@see TestBaseClass} for stub inheritance behavior.
 * {@see TestClass} for stub member visibility behavior.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class ReflectionHelperTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testInaccessibleParentPropertyReturnsExpectedValue(): void
    {
        self::assertSame(
            'valueParent',
            ReflectionHelper::inaccessibleParentProperty(
                new TestClass(),
                TestBaseClass::class,
                'propertyParent',
            ),
            "Should return the value of the parent property 'propertyParent' when accessed via reflection.",
        );
    }

    public function testInaccessiblePropertyReturnsNullForEmptyPropertyName(): void
    {
        self::assertNull(
            ReflectionHelper::inaccessibleProperty(new TestClass(), ''),
            'Should return null when property name is empty.',
        );
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnInaccessiblePropertyValueWhenPropertyIsPrivate(): void
    {
        self::assertSame(
            'value',
            ReflectionHelper::inaccessibleProperty(new TestClass(), 'property'),
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
            ReflectionHelper::invokeMethod(new TestClass(), 'inaccessibleMethod'),
            "Should return 'value' when invoking the inaccessible method 'inaccessibleMethod' on 'TestClass' "
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
            ReflectionHelper::invokeParentMethod(
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

        ReflectionHelper::setInaccessibleParentProperty($object, TestBaseClass::class, 'propertyParent', 'foo');

        self::assertSame(
            'foo',
            ReflectionHelper::inaccessibleParentProperty($object, TestBaseClass::class, 'propertyParent'),
            "Should return 'foo' after setting parent property 'propertyParent' "
            . "via 'setInaccessibleParentProperty' method.",
        );
    }

    /**
     * @throws ReflectionException
     */
    public function testSetInaccessiblePropertySetsValueCorrectly(): void
    {
        $object = new TestClass();

        ReflectionHelper::setInaccessibleProperty($object, 'property', 'foo');

        self::assertSame(
            'foo',
            ReflectionHelper::inaccessibleProperty($object, 'property'),
            "Should return 'foo' after setting the private property 'property' via 'setInaccessibleProperty' method.",
        );
    }

    public function testThrowReflectionExceptionForNonExistentProperty(): void
    {
        $this->expectException(ReflectionException::class);
        $this->expectExceptionMessage(
            'Property PHPForge\Support\Tests\Stub\TestClass::$nonExistent does not exist',
        );

        ReflectionHelper::inaccessibleProperty(new TestClass(), 'nonExistent');
    }
}
