<?php

declare(strict_types=1);

namespace Sabroan\DI\Reflection\Tests\Data\Type;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sabroan\DI\Reflection\Data\Type\AbstractTypes;
use Sabroan\DI\Reflection\Data\Type\TypeInterface;
use Sabroan\DI\Reflection\Tests\Fixtures\DummyTargetClass;

#[CoversClass(AbstractTypes::class)]
final class AbstractTypesTest extends TestCase
{
    private function createAbstractTypes(
        \ReflectionUnionType|\ReflectionIntersectionType $type,
        ?\ReflectionParameter $parameter = null,
    ): AbstractTypes {
        return new readonly class ($type, $parameter) extends AbstractTypes {
            public function match(mixed $var): bool
            {
                return false;
            }
        };
    }

    public function testConstructorPopulatesTypesArray(): void
    {
        $parameter = new \ReflectionParameter(
            [DummyTargetClass::class, 'unionMethod'],
            'param',
        );

        $type = $parameter->getType();

        self::assertInstanceOf(\ReflectionUnionType::class, $type);

        $abstractTypes = $this->createAbstractTypes($type, $parameter);

        self::assertCount(2, $abstractTypes->types);

        foreach ($abstractTypes->types as $type) {
            self::assertInstanceOf(TypeInterface::class, $type);
        }
    }

    public function testGetClassNamesYieldsClassNamesFromAllTypes(): void
    {
        $parameter = new \ReflectionParameter(
            [DummyTargetClass::class, 'unionMethod'],
            'param',
        );

        $type = $parameter->getType();

        self::assertInstanceOf(\ReflectionUnionType::class, $type);

        $abstractTypes = $this->createAbstractTypes($type, $parameter);

        $classNames = $abstractTypes->getClassNames();

        self::assertSame(
            [\Sabroan\DI\Reflection\Tests\Fixtures\DummyDependency::class],
            $classNames,
        );
    }
}
