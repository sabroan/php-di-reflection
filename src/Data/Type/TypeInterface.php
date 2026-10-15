<?php

declare(strict_types=1);

namespace Sabroan\DI\Data\Type;

interface TypeInterface extends \Stringable
{
    public function match(mixed $var): bool;
    public function getClassNames(): iterable;
}
