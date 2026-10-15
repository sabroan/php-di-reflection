<?php

declare(strict_types=1);

namespace Sabroan\DI;

use Psr\Container\ContainerInterface;

trait ReflectionFunctionAbstractTrait
{
    #[\Override]
    public function getParameters(?ContainerInterface $container = null): array
    {
        $function = match (true) {
            $this instanceof ReflectionMethod => [$this->class, $this->name],
            $this->isClosure() => parent::getClosure(),
            default => $this->name,
        };
        $arguments = [];
        foreach (parent::getParameters() as $parameter) {
            $param = new ReflectionParameter($function, $parameter->getName());
            $value = $param->getDefaultValue($container);
            if ($param->isVariadic() && \is_iterable($value)) {
                $arguments = [...$arguments, ...$value];
            } else {
                $arguments[] = $value;
            }
        }
        return $arguments;
    }

    #[\Override]
    public function getClosureScopeClass(): ?ReflectionClass
    {
        $class = parent::getClosureScopeClass();
        return $class ? new ReflectionClass($class->name) : null;
    }
}