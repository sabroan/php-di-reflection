<?php

declare(strict_types=1);

namespace Sabroan\DI\Reflection\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Sabroan\DI\Reflection\Exception\ParameterException;
use Sabroan\DI\Reflection\ReflectionClass;
use Sabroan\DI\Reflection\ReflectionFunction;
use Sabroan\DI\Reflection\ReflectionMethod;
use Sabroan\DI\Reflection\ReflectionParameter;
use Sabroan\DI\Reflection\Tests\Fixtures\DummyDependency;
use Sabroan\DI\Reflection\Tests\Fixtures\DummyEnum;
use Sabroan\DI\Reflection\Tests\Fixtures\DummyTargetClass;

#[CoversClass(ReflectionParameter::class)]
final class ReflectionParameterTest extends TestCase
{
    /**
     * @param object|string|array{object|string, string} $function
     */
    private function createReflectionParameter(
        object|string|array $function,
        string|int $param,
    ): ReflectionParameter {
        return new ReflectionParameter($function, $param);
    }

    public function testGetDeclaringFunctionReturnsReflectionMethod(): void
    {
        $parameter = $this->createReflectionParameter(
            [DummyTargetClass::class, 'methodWithTypedParam'],
            'dependency',
        );

        $function = $parameter->getDeclaringFunction();

        self::assertInstanceOf(
            ReflectionMethod::class,
            $function,
        );
        self::assertSame(
            'methodWithTypedParam',
            $function->getName(),
        );
    }

    public function testGetDeclaringFunctionReturnsReflectionFunction(): void
    {
        $closure = static function (string $foo): void {};
        $parameter = $this->createReflectionParameter(
            $closure,
            'foo',
        );
        self::assertInstanceOf(
            ReflectionFunction::class,
            $parameter->getDeclaringFunction(),
        );
    }

    public function testGetDeclaringClassReturnsReflectionClassWhenInClass(): void
    {
        $parameter = $this->createReflectionParameter(
            [DummyTargetClass::class, 'methodWithTypedParam'],
            'dependency',
        );

        $class = $parameter->getDeclaringClass();

        self::assertInstanceOf(ReflectionClass::class, $class);
        self::assertSame(DummyTargetClass::class, $class->getName());
    }

    public function testGetDeclaringClassReturnsNullForClosure(): void
    {
        $closure = static function (string $foo): void {};
        $parameter = $this->createReflectionParameter($closure, 'foo');

        self::assertNull($parameter->getDeclaringClass());
    }

    #[DataProvider('toStringProvider')]
    public function testToStringFormatting(
        string $method,
        string $name,
        string $expected,
    ): void {
        $parameter = $this->createReflectionParameter(
            [DummyTargetClass::class, $method],
            $name,
        );

        self::assertSame($expected, (string) $parameter);
    }

    /**
     * @return array<string, array{string, string, string}>
     */
    public static function toStringProvider(): array
    {
        return [
            'untyped' => [
                'untypedMethod',
                'param',
                '$param',
            ],
            'typed' => [
                'methodWithTypedParam',
                'dependency',
                DummyDependency::class . ' $dependency',
            ],
            'variadic' => [
                'variadicMethod',
                'items',
                '...$items',
            ],
            'reference' => [
                'referenceMethod',
                'value',
                '&$value',
            ],
            'default string' => [
                'defaultStringMethod',
                'name',
                "string \$name = 'default'",
            ],
            'default enum' => [
                'defaultEnumMethod',
                'enum',
                DummyEnum::class . ' $enum = ' . DummyEnum::class . '::First',
            ],
        ];
    }

    public function testGetDefaultValueWithoutTypeReturnsDefaultValue(): void
    {
        $parameter = $this->createReflectionParameter(
            [DummyTargetClass::class, 'untypedWithDefault'],
            'param',
        );

        self::assertSame('default_value', $parameter->getDefaultValue());
    }

    public function testGetDefaultValueWithoutTypeThrowsExceptionWhenNoDefault(): void
    {
        $parameter = $this->createReflectionParameter(
            [DummyTargetClass::class, 'untypedMethod'],
            'param',
        );

        $this->expectException(ParameterException::class);
        $parameter->getDefaultValue();
    }

    public function testGetDefaultValueResolvesFromContainer(): void
    {
        $dependency = new DummyDependency();

        $container = self::createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('has')
            ->with(DummyDependency::class)
            ->willReturn(true);

        $container->expects(self::once())
            ->method('get')
            ->with(DummyDependency::class)
            ->willReturn($dependency);

        $parameter = $this->createReflectionParameter(
            [DummyTargetClass::class, 'methodWithTypedParam'],
            'dependency',
        );

        self::assertSame($dependency, $parameter->getDefaultValue($container));
    }

    public function testGetDefaultValueCreatesInstanceWhenNotInContainer(): void
    {
        $container = self::createStub(ContainerInterface::class);
        $container->method('has')->willReturn(false);

        $parameter = $this->createReflectionParameter(
            [DummyTargetClass::class, 'methodWithTypedParam'],
            'dependency',
        );

        $result = $parameter->getDefaultValue($container);

        self::assertInstanceOf(DummyDependency::class, $result);
    }

    public function testGetDefaultValueReturnsDefaultValueWhenContainerAndCreationFail(): void
    {
        $parameter = $this->createReflectionParameter(
            [DummyTargetClass::class, 'methodWithDefaultValue'],
            'value',
        );

        self::assertSame('fallback', $parameter->getDefaultValue());
    }

    public function testGetDefaultValueReturnsNullWhenNullableAndUnresolvable(): void
    {
        $parameter = $this->createReflectionParameter(
            [DummyTargetClass::class, 'nullableMethod'],
            'dependency',
        );

        self::assertNull($parameter->getDefaultValue());
    }
}
