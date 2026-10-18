<?php

declare(strict_types=1);

namespace Sabroan\DI\Reflection\Tests\Data\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sabroan\DI\Reflection\Data\Exception\TypeException;
use Sabroan\DI\Reflection\Tests\Fixtures\DummyTargetClass;

#[CoversClass(TypeException::class)]
final class TypeExceptionTest extends TestCase
{
    private function createTypeException(
        ?\ReflectionType $type,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ): TypeException {
        return new TypeException($type, $message, $code, $previous);
    }

    public function testConstructorSetsPropertiesCorrectly(): void
    {
        $reflectionParameter = new \ReflectionParameter(
            [DummyTargetClass::class, 'methodWithTypedParam'],
            'dependency',
        );
        $type = $reflectionParameter->getType();
        $previousException = new \RuntimeException('Previous error');

        $exception = $this->createTypeException(
            $type,
            'Invalid type provided',
            400,
            $previousException,
        );

        self::assertInstanceOf(\ReflectionException::class, $exception);
        self::assertSame($type, $exception->type);
        self::assertSame('Invalid type provided', $exception->getMessage());
        self::assertSame(400, $exception->getCode());
        self::assertSame($previousException, $exception->getPrevious());
    }

    public function testConstructorDefaults(): void
    {
        $exception = $this->createTypeException(null);

        self::assertNull($exception->type);
        self::assertSame('', $exception->getMessage());
        self::assertSame(0, $exception->getCode());
        self::assertNull($exception->getPrevious());
    }
}
