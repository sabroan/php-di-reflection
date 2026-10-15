<?php

declare(strict_types=1);

namespace Sabroan\DI\Data\Type;

abstract readonly class AbstractType implements TypeInterface
{
    public function __construct(
        private \ReflectionType $type,
    ) {}

    #[\Override]
    public function __toString(): string
    {
        return (string) $this->type;
    }
}
