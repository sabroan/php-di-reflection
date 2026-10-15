<?php

declare(strict_types=1);

namespace Sabroan\DI\Data\Type;

final readonly class UnionType extends AbstractTypes
{
    #[\Override]
    public function match(mixed $var): bool
    {
        foreach ($this->types as $type) {
            if ($type->match($var)) {
                return true;
            }
        }
        return false;
    }
}
