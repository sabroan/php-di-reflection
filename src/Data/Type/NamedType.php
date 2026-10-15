<?php

declare(strict_types=1);

namespace Sabroan\DI\Data\Type;

final readonly class NamedType extends AbstractType
{
    public function __construct(
        public \ReflectionNamedType $type,
        public ?\ReflectionParameter $parameter = null,
    ) {
        parent::__construct($type);
    }

    #[\Override]
    public function getClassNames(): iterable
    {
        if ($this->type->isBuiltin()) {
            return;
        }

        if (!$this->parameter) {
            yield $this->type->getName();
            return;
        }

        $class = match (\strtolower($this->type->getName())) {
            'parent'         => ($this->parameter?->getDeclaringClass()?->getParentClass() ?: null)?->name,
            'self', 'static' => $this->parameter?->getDeclaringClass()?->name,
            default          => $this->type->getName(),
        };

        if (null !== $class) {
            yield $class;
        }
    }

    #[\Override]
    public function match(mixed $var): bool
    {
        if ($this->type->isBuiltin()) {
            return false;
        }

        foreach ($this->getClassNames() as $class) {
            if ($var instanceof $class) {
                return true;
            }
        }

        if (null === $var) {
            return $this->type->allowsNull();
        }

        return false;
    }
}
