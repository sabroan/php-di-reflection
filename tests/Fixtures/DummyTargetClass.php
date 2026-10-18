<?php

declare(strict_types=1);

namespace Sabroan\DI\Reflection\Tests\Fixtures;

final class DummyTargetClass
{
    public function __construct() {}

    /**
     * @return \Closure(): null
     */
    public function getScopedClosure(): \Closure
    {
        return \Closure::fromCallable(fn() => null);
    }

    public function defaultStringMethod(string $name = 'default'): void {}

    public function defaultEnumMethod(DummyEnum $enum = DummyEnum::First): void {}

    public function unionMethod(string|DummyDependency $param): void {}

    /**
     * @param \Countable&\Traversable<mixed> $param
     */
    public function intersectionMethod(\Countable&\Traversable $param): void {}

    public function methodWithTypedParam(DummyDependency $dependency): void {}

    public function methodWithDefaultValue(string $value = 'fallback'): void {}

    public function nullableMethod(?\Stringable $dependency): void {}

    // @phpstan-ignore missingType.parameter
    public function referenceMethod(&$value): void {}

    // @phpstan-ignore missingType.parameter
    public function untypedMethod($param): void {}

    // @phpstan-ignore missingType.parameter
    public function untypedWithDefault($param = 'default_value'): void {}

    // @phpstan-ignore missingType.parameter
    public function variadicMethod(...$items): void {}
}
