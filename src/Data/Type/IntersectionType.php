<?php

declare(strict_types=1);

namespace Sabroan\DI\Reflection\Data\Type;

/**
 * @psalm-immutable
 */
final readonly class IntersectionType extends AbstractTypes
{
    #[\Override]
    public function match(mixed $var): bool
    {
        foreach ($this->types as $type) {
            if (!$type->match($var)) {
                return false;
            }
        }
        return true;
    }
}
