<?php

declare(strict_types=1);

namespace Sabroan\DI\Reflection;

/**
 * @api
 * @extends \ReflectionClass<object>
 * @psalm-suppress ImmutableDependency
 */
final class ReflectionClass extends \ReflectionClass
{
    #[\Override]
    public function getConstructor(): ?ReflectionMethod
    {
        $constructor = parent::getConstructor();
        if (null === $constructor) {
            return null;
        }

        return new ReflectionMethod($this->name, $constructor->name);
    }

    #[\Override]
    public function getMethod(string $name): ReflectionMethod
    {
        return new ReflectionMethod($this->name, parent::getMethod($name)->name);
    }

    #[\Override]
    public function getMethods(?int $filter = null): array
    {
        $methods = [];
        foreach (parent::getMethods($filter) as $method) {
            $methods[] = $this->getMethod($method->name);
        }
        return $methods;
    }

    /**
     * @psalm-capabilities read-props
     */
    #[\Override]
    public function getParentClass(): static|false
    {
        $parent = parent::getParentClass();
        if (false !== $parent) {
            return new static($parent->name);
        }

        return false;
    }
}
