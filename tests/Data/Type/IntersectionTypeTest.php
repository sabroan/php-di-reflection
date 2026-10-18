<?php

declare(strict_types=1);

namespace Sabroan\DI\Reflection\Tests\Data\Type;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sabroan\DI\Reflection\Data\Type\IntersectionType;
use Sabroan\DI\Reflection\Tests\Fixtures\DummyTargetClass;

#[CoversClass(IntersectionType::class)]
final class IntersectionTypeTest extends TestCase
{
    private function createIntersectionType(
        \ReflectionIntersectionType $type,
        ?\ReflectionParameter $parameter = null,
    ): IntersectionType {
        return new IntersectionType($type, $parameter);
    }

    public function testMatchReturnsTrueWhenAllTypesMatch(): void
    {
        $parameter = new \ReflectionParameter(
            [DummyTargetClass::class, 'intersectionMethod'],
            'param',
        );

        $type = $parameter->getType();
        self::assertInstanceOf(\ReflectionIntersectionType::class, $type);

        $intersectionType = $this->createIntersectionType($type, $parameter);
        $matchingObject = new \ArrayObject([1, 2, 3]);
        self::assertTrue($intersectionType->match($matchingObject));
    }

    #[DataProvider('nonMatchingValueProvider')]
    public function testMatchReturnsFalseWhenAnyTypeFailsToMatch(mixed $value): void
    {
        $parameter = new \ReflectionParameter(
            [DummyTargetClass::class, 'intersectionMethod'],
            'param',
        );

        $type = $parameter->getType();
        self::assertInstanceOf(\ReflectionIntersectionType::class, $type);

        $intersectionType = $this->createIntersectionType($type, $parameter);
        self::assertFalse($intersectionType->match($value));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function nonMatchingValueProvider(): array
    {
        return [
            'object matching only one interface' => [
                new class implements \Countable {
                    public function count(): int
                    {
                        return 0;
                    }
                },
            ],
            'unrelated object' => [new \stdClass()],
            'string' => ['not_an_object'],
            'null' => [null],
        ];
    }
}
