<?php

declare(strict_types=1);

namespace Sabroan\DI\Reflection;

/**
 * @api
 * @psalm-suppress ImmutableDependency
 */
final class ReflectionMethod extends \ReflectionMethod
{
    use ReflectionFunctionAbstractTrait;

    #[\Override]
    public static function createFromMethodName(string $method): static
    {
        $reflection = parent::createFromMethodName($method);

        return new static($reflection->class, $reflection->name);
    }

    /**
     * @psalm-capabilities read-props
     */
    #[\Override]
    public function getDeclaringClass(): ReflectionClass
    {
        return new ReflectionClass($this->class);
    }

    #[\Override]
    public function getPrototype(): self
    {
        $parent = parent::getPrototype();

        return new self($parent->class, $parent->name);
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->class . ($this->isStatic() ? '::' : '->') . $this->name;
    }
}
