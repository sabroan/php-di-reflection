<?php

declare(strict_types=1);

namespace Sabroan\DI\Reflection\Data\Type;

/**
 * @psalm-immutable
 */
abstract readonly class AbstractType implements TypeInterface
{
    /**
     * @phpstan-pure
     *
     * @param list<class-string> $classes
     */
    public function __construct(
        private \ReflectionType $type,
        public array $classes = [],
    ) {}

    /**
     * @phpstan-pure
     * @psalm-mutation-free
     *
     * @return list<class-string>
     */
    #[\Override]
    public function getClassNames(): array
    {
        /** @psalm-suppress ImpureVariable */
        return $this->classes;
    }

    #[\Override]
    public function __toString(): string
    {
        return (string) $this->type;
    }
}
