<?php

declare(strict_types=1);

namespace Sabroan\DI;

class ReflectionMethod extends \ReflectionMethod
{
    use ReflectionFunctionAbstractTrait;

    #[\Override]
    public static function createFromMethodName(string $method): static
    {
        $reflection = parent::createFromMethodName($method);

        return new static($reflection->class, $reflection->name);
    }

    #[\Override]
    public function getDeclaringClass(): ReflectionClass
    {
        return new ReflectionClass($this->class);
    }

    #[\Override]
    public function getPrototype(): static
    {
        $parent = parent::getPrototype();

        return new static($parent->class, $parent->name);
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->class . ($this->isStatic() ? '::' : '->') . $this->name;
    }
}