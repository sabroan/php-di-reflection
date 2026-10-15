<?php

declare(strict_types=1);

namespace Sabroan\DI;

class ReflectionClass extends \ReflectionClass
{
    #[\Override]
    public function getConstructor(): ?ReflectionMethod
    {
        $constructor = parent::getConstructor();
        if (null === $constructor) {
            return null;
        }
        return $this->getMethod($constructor->name);
    }

    #[\Override]
    public function getMethod(string $name): ReflectionMethod
    {
        return new ReflectionMethod($this->name, $name);
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

    #[\Override]
    public function getParentClass(): static|false
    {
        $parent = parent::getParentClass();
        return $parent
            ? new static($parent->name)
            : false;
    }
}
