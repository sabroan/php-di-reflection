<?php

declare(strict_types=1);

namespace Sabroan\DI\Reflection\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sabroan\DI\Reflection\ReflectionClass;
use Sabroan\DI\Reflection\ReflectionMethod;
use Sabroan\DI\Reflection\Tests\Fixtures\DummyClassChild;
use Sabroan\DI\Reflection\Tests\Fixtures\DummyClassParent;

#[CoversClass(ReflectionMethod::class)]
final class ReflectionMethodTest extends TestCase
{
    private function createReflectionMethod(object|string $objectOrClass, string $method): ReflectionMethod
    {
        return new ReflectionMethod($objectOrClass, $method);
    }

    public function testCreateFromMethodName(): void
    {
        $method = ReflectionMethod::createFromMethodName(
            DummyClassChild::class . '::staticMethod',
        );

        self::assertInstanceOf(ReflectionMethod::class, $method);
        self::assertSame(DummyClassChild::class, $method->class);
        self::assertSame('staticMethod', $method->name);
    }

    public function testGetDeclaringClassReturnsReflectionClass(): void
    {
        $reflection = $this->createReflectionMethod(
            DummyClassChild::class,
            'childPublicMethod',
        );

        $declaringClass = $reflection->getDeclaringClass();

        self::assertInstanceOf(ReflectionClass::class, $declaringClass);
        self::assertSame(DummyClassChild::class, $declaringClass->getName());
    }

    public function testGetPrototypeReturnsReflectionMethod(): void
    {
        $reflection = $this->createReflectionMethod(
            DummyClassChild::class,
            'instanceMethod',
        );

        $prototype = $reflection->getPrototype();

        self::assertInstanceOf(ReflectionMethod::class, $prototype);
        self::assertSame(DummyClassParent::class, $prototype->class);
        self::assertSame('instanceMethod', $prototype->name);
    }

    #[DataProvider('toStringProvider')]
    public function testToStringReturnsFormattedMethodName(
        string $methodName,
        string $expectedOutput,
    ): void {
        self::assertSame(
            $expectedOutput,
            (string) $this->createReflectionMethod(
                DummyClassChild::class,
                $methodName,
            ),
        );
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function toStringProvider(): array
    {
        return [
            'instance' => [
                'childPublicMethod',
                DummyClassChild::class . '->childPublicMethod',
            ],
            'static' => [
                'staticMethod',
                DummyClassChild::class . '::staticMethod',
            ],
        ];
    }
}
