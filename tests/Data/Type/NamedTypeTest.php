<?php

declare(strict_types=1);

namespace Sabroan\DI\Reflection\Tests\Data\Type;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sabroan\DI\Reflection\Data\Type\NamedType;
use Sabroan\DI\Reflection\Tests\Fixtures\DummyClassChild;
use Sabroan\DI\Reflection\Tests\Fixtures\DummyClassParent;
use Sabroan\DI\Reflection\Tests\Fixtures\DummyDependency;
use Sabroan\DI\Reflection\Tests\Fixtures\DummyTargetClass;

#[CoversClass(NamedType::class)]
final class NamedTypeTest extends TestCase
{
    private function createNamedType(
        \ReflectionNamedType $type,
        ?\ReflectionParameter $parameter = null,
    ): NamedType {
        return new NamedType($type, $parameter);
    }

    public function testGetClassNamesReturnsEmptyForBuiltinType(): void
    {
        $parameter = new \ReflectionParameter(
            [DummyTargetClass::class, 'defaultStringMethod'],
            'name',
        );

        $reflection = $parameter->getType();

        self::assertInstanceOf(\ReflectionNamedType::class, $reflection);

        $namedType = $this->createNamedType($reflection, $parameter);

        $classNames = $namedType->getClassNames();

        self::assertEmpty($classNames);
    }

    public function testGetClassNamesWithoutParameterYieldsRawNameForNonBuiltin(): void
    {
        $parameter = new \ReflectionParameter(
            [DummyTargetClass::class, 'methodWithTypedParam'],
            'dependency',
        );

        $type = $parameter->getType();

        self::assertInstanceOf(\ReflectionNamedType::class, $type);

        $namedType = $this->createNamedType($type);

        $classNames = $namedType->getClassNames();

        self::assertSame([DummyDependency::class], $classNames);
    }

    /**
     * @param list<class-string> $expectedClasses
     */
    #[DataProvider('specialKeywordsProvider')]
    public function testGetClassNamesResolvesSpecialKeywords(
        string $methodName,
        array $expectedClasses,
    ): void {
        $parameter = new \ReflectionParameter(
            [DummyClassChild::class, $methodName],
            'param',
        );

        $type = $parameter->getType();

        self::assertInstanceOf(\ReflectionNamedType::class, $type);

        $namedType = $this->createNamedType($type, $parameter);

        $classNames = $namedType->getClassNames();

        self::assertSame($expectedClasses, $classNames);
    }

    /**
     * @return array<string, array{string, list<class-string>}>
     */
    public static function specialKeywordsProvider(): array
    {
        return [
            'self' => [
                'selfMethod',
                [DummyClassChild::class],
            ],
            'parent' => [
                'parentKeywordMethod',
                [DummyClassParent::class],
            ],
        ];
    }

    public function testMatchReturnsTrueForMatchingInstance(): void
    {
        $parameter = new \ReflectionParameter(
            [DummyTargetClass::class, 'methodWithTypedParam'],
            'dependency',
        );

        $type = $parameter->getType();
        self::assertInstanceOf(\ReflectionNamedType::class, $type);

        $namedType = $this->createNamedType($type, $parameter);
        self::assertTrue($namedType->match(new DummyDependency()));
    }

    public function testMatchReturnsFalseForBuiltinType(): void
    {
        $parameter = new \ReflectionParameter(
            [DummyTargetClass::class, 'defaultStringMethod'],
            'name',
        );

        $type = $parameter->getType();
        self::assertInstanceOf(\ReflectionNamedType::class, $type);

        $namedType = $this->createNamedType($type, $parameter);
        self::assertFalse($namedType->match('hello'));
    }

    public function testMatchHandlesNullableClassType(): void
    {
        $parameter = new \ReflectionParameter(
            [DummyTargetClass::class, 'nullableMethod'],
            'dependency',
        );

        $type = $parameter->getType();
        self::assertInstanceOf(\ReflectionNamedType::class, $type);

        $namedType = $this->createNamedType($type, $parameter);
        self::assertTrue($namedType->match(null));
    }
}
