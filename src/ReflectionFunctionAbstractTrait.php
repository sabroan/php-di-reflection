<?php

declare(strict_types=1);

namespace Sabroan\DI\Reflection;

use Psr\Container\ContainerInterface;

/**
 * @psalm-suppress ImmutableDependency
 */
trait ReflectionFunctionAbstractTrait
{
    /**
     * @return list<mixed>
     * @phpstan-ignore method.childReturnType
     */
    #[\Override]
    public function getParameters(?ContainerInterface $container = null): array
    {
        /** @var array{mixed, mixed}|\Closure|string $function */
        $function = match (true) {
            $this instanceof ReflectionMethod   => [$this->class, $this->name],
            $this->isClosure()                  => parent::getClosure(),
            default                             => $this->name,
        };

        $arguments = [];

        foreach (parent::getParameters() as $parameter) {
            $param = new ReflectionParameter($function, $parameter->getName());

            /** @psalm-suppress MixedAssignment */
            $value = $param->getDefaultValue($container);

            if ($param->isVariadic() && \is_iterable($value)) {
                /** @psalm-suppress MixedAssignment */
                foreach ($value as $item) {
                    $arguments[] = $item;
                }
            } else {
                /** @psalm-suppress MixedAssignment */
                $arguments[] = $value;
            }
        }

        return $arguments;
    }

    #[\Override]
    public function getClosureScopeClass(): ?ReflectionClass
    {
        if (null === parent::getClosureThis()) {
            return null;
        }

        $class = parent::getClosureScopeClass();

        return (null !== $class)
            ? new ReflectionClass($class->name)
            : null;
    }
}
