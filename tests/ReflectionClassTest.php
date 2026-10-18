<?php

declare(strict_types=1);

namespace Sabroan\DI\Reflection\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sabroan\DI\Reflection\ReflectionClass;
use Sabroan\DI\Reflection\ReflectionMethod;
use Sabroan\DI\Reflection\Tests\Fixtures\DummyClassChild;
use Sabroan\DI\Reflection\Tests\Fixtures\DummyClassParent;
use Sabroan\DI\Reflection\Tests\Fixtures\DummyTargetClass;

#[CoversClass(ReflectionClass::class)]
final class ReflectionClassTest extends TestCase
{
    /**
     * @param class-string<object>|object $objectOrClass
     */
    private function createReflectionClass(string|object $objectOrClass): ReflectionClass
    {
        return new ReflectionClass($objectOrClass);
    }

    public function testGetConstructorReturnsReflectionMethodWhenConstructorExists(): void
    {
        $reflection = $this->createReflectionClass(DummyTargetClass::class);
        $constructor = $reflection->getConstructor();

        self::assertInstanceOf(ReflectionMethod::class, $constructor);
        self::assertSame('__construct', $constructor->getName());
    }

    public function testGetConstructorReturnsNullWhenNoConstructor(): void
    {
        $reflection = $this->createReflectionClass(DummyClassParent::class);

        self::assertNull($reflection->getConstructor());
    }

    public function testGetParentClassReturnsReflectionClassWhenParentExists(): void
    {
        $reflection = $this->createReflectionClass(DummyClassChild::class);
        $parent = $reflection->getParentClass();

        self::assertInstanceOf(ReflectionClass::class, $parent);
        self::assertSame(DummyClassParent::class, $parent->getName());
    }

    public function testGetParentClassReturnsFalseWhenNoParent(): void
    {
        $reflection = $this->createReflectionClass(DummyClassParent::class);

        self::assertFalse($reflection->getParentClass());
    }

    public function testCanInstantiateFromObjectInstance(): void
    {
        $instance = new DummyTargetClass();
        $reflection = $this->createReflectionClass($instance);

        self::assertSame(DummyTargetClass::class, $reflection->getName());
    }
}
