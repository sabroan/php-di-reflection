<?php

declare(strict_types=1);

namespace Sabroan\DI\Reflection\Tests\Data\Type;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sabroan\DI\Reflection\Data\Type\AbstractType;
use Sabroan\DI\Reflection\Data\Type\TypeInterface;
use Sabroan\DI\Reflection\Tests\Fixtures\DummyTargetClass;

#[CoversClass(AbstractType::class)]
final class AbstractTypeTest extends TestCase
{
    private function createAbstractType(\ReflectionType $type): AbstractType
    {
        return new readonly class ($type) extends AbstractType {
            /**
             * @return list<class-string>
             */
            public function getClassNames(): array
            {
                return [];
            }

            public function match(mixed $var): bool
            {
                return false;
            }
        };
    }

    public function testToStringReturnsStringRepresentationOfReflectionType(): void
    {
        $parameter = new \ReflectionParameter(
            [DummyTargetClass::class, 'defaultStringMethod'],
            'name',
        );

        $type = $parameter->getType();
        self::assertInstanceOf(\ReflectionNamedType::class, $type);

        $abstractType = $this->createAbstractType($type);
        self::assertInstanceOf(TypeInterface::class, $abstractType);
        self::assertSame('string', (string) $abstractType);
    }
}
