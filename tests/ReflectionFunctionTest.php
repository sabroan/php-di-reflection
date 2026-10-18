<?php

declare(strict_types=1);

namespace Sabroan\DI\Reflection\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sabroan\DI\Reflection\ReflectionFunction;

use function Sabroan\DI\Reflection\Tests\Fixtures\dummyFunction;

#[CoversClass(ReflectionFunction::class)]
final class ReflectionFunctionTest extends TestCase
{
    /**
     * @param \Closure(): mixed|string $function
     */
    private function createReflectionFunction(\Closure|string $function): ReflectionFunction
    {
        return new ReflectionFunction($function);
    }

    public function testToStringReturnsFileNameAndLineForClosure(): void
    {
        $closure = static function (): void {};
        self::assertSame(
            __FILE__ . ':' . __LINE__ - 2,
            (string) $this->createReflectionFunction($closure),
        );
    }

    /**
     * @param \Closure(): mixed|string $function
     */
    #[DataProvider('namedFunctionProvider')]
    public function testToStringReturnsNameForNamedFunction(
        \Closure|string $function,
        string $expectedName,
    ): void {
        self::assertSame(
            $expectedName,
            (string) $this->createReflectionFunction($function),
        );
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function namedFunctionProvider(): array
    {
        return [
            'internal' => ['strlen', 'strlen'],
            'custom' => [
                'Sabroan\DI\Reflection\Tests\Fixtures\dummyFunction',
                'Sabroan\DI\Reflection\Tests\Fixtures\dummyFunction',
            ],
        ];
    }
}
