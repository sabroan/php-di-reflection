<?php

declare(strict_types=1);

namespace Sabroan\DI\Reflection\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sabroan\DI\Reflection\Exception\ParameterException;
use Sabroan\DI\Reflection\Tests\Fixtures\DummyTargetClass;

#[CoversClass(ParameterException::class)]
final class ParameterExceptionTest extends TestCase
{
    private function createParameterException(
        \ReflectionParameter $parameter,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ): ParameterException {
        return new ParameterException($parameter, $message, $code, $previous);
    }

    public function testConstructorSetsPropertiesCorrectly(): void
    {
        $parameter = new \ReflectionParameter(
            [DummyTargetClass::class, 'untypedMethod'],
            'param',
        );
        $previousException = new \RuntimeException('Previous error');

        $exception = $this->createParameterException(
            $parameter,
            'Parameter resolution failed',
            500,
            $previousException,
        );

        self::assertInstanceOf(\ReflectionException::class, $exception);
        self::assertSame($parameter, $exception->parameter);
        self::assertSame('Parameter resolution failed', $exception->getMessage());
        self::assertSame(500, $exception->getCode());
        self::assertSame($previousException, $exception->getPrevious());
    }

    public function testConstructorDefaults(): void
    {
        $parameter = new \ReflectionParameter(
            [DummyTargetClass::class, 'untypedMethod'],
            'param',
        );

        $exception = $this->createParameterException($parameter);

        self::assertSame($parameter, $exception->parameter);
        self::assertSame('', $exception->getMessage());
        self::assertSame(0, $exception->getCode());
        self::assertNull($exception->getPrevious());
    }
}
