<?php

declare(strict_types=1);

namespace Sabroan\DI\Reflection\Data\Type;

/**
 * @psalm-immutable
 */
interface TypeInterface extends \Stringable
{
    /**
     * @psalm-mutation-free
     */
    public function match(mixed $var): bool;

    /**
     * @phpstan-pure
     * @psalm-mutation-free
     *
     * @return list<class-string>
     */
    public function getClassNames(): array;
}
