<?php

declare(strict_types=1);

namespace Sabroan\DI\Reflection\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sabroan\DI\Reflection\ReflectionClass;
use Sabroan\DI\Reflection\ReflectionFunction;
use Sabroan\DI\Reflection\ReflectionFunctionAbstractTrait;
use Sabroan\DI\Reflection\ReflectionParameter;
use Sabroan\DI\Reflection\Tests\Fixtures\DummyTargetClass;

#[CoversClass(ReflectionFunctionAbstractTrait::class)]
final class ReflectionFunctionAbstractTraitTest extends TestCase
{
    /**
     * @param \Closure(): mixed|string $function
     */
    private function createReflectionFunction(\Closure|string $function): ReflectionFunction
    {
        return new ReflectionFunction($function);
    }

    public function testGetParametersResolvesDefaultValues(): void
    {
        $closure = static function (string $name = 'default', int $age = 30): void {};

        $reflection = $this->createReflectionFunction($closure);

        self::assertSame(
            ['default', 30],
            $reflection->getParameters(),
        );
    }

    public function testGetClosureScopeClassReturnsReflectionClassWhenScoped(): void
    {
        $target = new DummyTargetClass();
        $closure = $target->getScopedClosure();

        $reflection = $this->createReflectionFunction($closure);

        $scope = $reflection->getClosureScopeClass();

        self::assertInstanceOf(ReflectionClass::class, $scope);
        self::assertSame(DummyTargetClass::class, $scope->getName());
    }

    public function testGetClosureScopeClassReturnsNullForUnboundClosure(): void
    {
        $closure = static function (): void {};

        $reflection = $this->createReflectionFunction($closure);

        self::assertNull($reflection->getClosureScopeClass());
    }
}
