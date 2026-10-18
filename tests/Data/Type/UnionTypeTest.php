<?php

declare(strict_types=1);

namespace Sabroan\DI\Reflection\Tests\Data\Type;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sabroan\DI\Reflection\Data\Type\UnionType;
use Sabroan\DI\Reflection\Tests\Fixtures\DummyDependency;
use Sabroan\DI\Reflection\Tests\Fixtures\DummyTargetClass;

#[CoversClass(UnionType::class)]
final class UnionTypeTest extends TestCase
{
    private function createUnionType(
        \ReflectionUnionType $type,
        ?\ReflectionParameter $parameter = null,
    ): UnionType {
        return new UnionType($type, $parameter);
    }

    public function testMatchReturnsTrueWhenAnyTypeMatches(): void
    {
        $parameter = new \ReflectionParameter(
            [DummyTargetClass::class, 'unionMethod'],
            'param',
        );

        $reflection = $parameter->getType();
        self::assertInstanceOf(\ReflectionUnionType::class, $reflection);

        $unionType = $this->createUnionType($reflection, $parameter);
        self::assertTrue($unionType->match(new DummyDependency()));
    }

    #[DataProvider('nonMatchingValueProvider')]
    public function testMatchReturnsFalseWhenNoTypeMatches(mixed $value): void
    {
        $parameter = new \ReflectionParameter(
            [DummyTargetClass::class, 'unionMethod'],
            'param',
        );

        $reflection = $parameter->getType();
        self::assertInstanceOf(\ReflectionUnionType::class, $reflection);

        $unionType = $this->createUnionType($reflection, $parameter);
        self::assertFalse($unionType->match($value));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function nonMatchingValueProvider(): array
    {
        return [
            'unrelated' => [new \stdClass()],
            'integer' => [123],
            'null' => [null],
        ];
    }
}
