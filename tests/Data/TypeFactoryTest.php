<?php

declare(strict_types=1);

namespace Sabroan\DI\Reflection\Tests\Data;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sabroan\DI\Reflection\Data\Exception\TypeException;
use Sabroan\DI\Reflection\Data\Type\IntersectionType;
use Sabroan\DI\Reflection\Data\Type\NamedType;
use Sabroan\DI\Reflection\Data\Type\UnionType;
use Sabroan\DI\Reflection\Data\TypeFactory;
use Sabroan\DI\Reflection\Exception\ParameterException;
use Sabroan\DI\Reflection\Tests\Fixtures\DummyTargetClass;

#[CoversClass(TypeFactory::class)]
final class TypeFactoryTest extends TestCase
{
    public function testCreateReturnsNamedTypeForReflectionNamedType(): void
    {
        $parameter = new \ReflectionParameter(
            [DummyTargetClass::class, 'defaultStringMethod'],
            'name',
        );
        $type = $parameter->getType();
        self::assertInstanceOf(\ReflectionNamedType::class, $type);

        $result = TypeFactory::create($type, $parameter);
        self::assertInstanceOf(NamedType::class, $result);
    }

    public function testCreateReturnsUnionTypeForReflectionUnionType(): void
    {
        $parameter = new \ReflectionParameter(
            [DummyTargetClass::class, 'unionMethod'],
            'param',
        );

        $type = $parameter->getType();
        self::assertInstanceOf(\ReflectionUnionType::class, $type);

        $result = TypeFactory::create($type, $parameter);
        self::assertInstanceOf(UnionType::class, $result);
    }

    public function testCreateReturnsIntersectionTypeForReflectionIntersectionType(): void
    {
        $parameter = new \ReflectionParameter(
            [DummyTargetClass::class, 'intersectionMethod'],
            'param',
        );

        $type = $parameter->getType();
        self::assertInstanceOf(\ReflectionIntersectionType::class, $type);

        $result = TypeFactory::create($type, $parameter);
        self::assertInstanceOf(IntersectionType::class, $result);
    }

    public function testCreateThrowsTypeExceptionForUnknownReflectionTypeWithoutParameter(): void
    {
        $unknownType = self::createStub(\ReflectionType::class);

        try {
            $result = TypeFactory::create($unknownType);
            self::fail('TypeException was expected but not thrown.');
        } catch (TypeException $exception) {
            self::assertSame($unknownType, $exception->type);
            self::assertSame('Unknown type', $exception->getMessage());
            self::assertNull($exception->getPrevious());
        }
    }

    public function testCreateThrowsTypeExceptionWithPreviousParameterExceptionWhenParameterProvided(): void
    {
        $unknownType = self::createStub(\ReflectionType::class);
        $parameter = new \ReflectionParameter(
            [DummyTargetClass::class, 'untypedMethod'],
            'param',
        );

        try {
            $result = TypeFactory::create($unknownType, $parameter);
            self::fail('TypeException was expected but not thrown.');
        } catch (TypeException $exception) {
            self::assertSame($unknownType, $exception->type);
            self::assertSame('Unknown type', $exception->getMessage());

            $previous = $exception->getPrevious();
            self::assertInstanceOf(ParameterException::class, $previous);
            self::assertSame($parameter, $previous->parameter);
        }
    }
}
