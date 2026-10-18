<?php

declare(strict_types=1);

namespace Sabroan\DI\Reflection;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Sabroan\DI\Reflection\Data\Type\TypeInterface;
use Sabroan\DI\Reflection\Data\TypeFactory;
use Sabroan\DI\Reflection\Exception\ParameterException;

/**
 * @api
 * @psalm-suppress ImmutableDependency
 */
final class ReflectionParameter extends \ReflectionParameter
{
    #[\Override]
    public function getDefaultValue(
        ?ContainerInterface $container = null,
    ): mixed {
        $type = parent::getType();
        $hasDefault = parent::isDefaultValueAvailable();

        if (null === $type) {
            if ($hasDefault) {
                return parent::getDefaultValue();
            }
            throw new ParameterException(
                $this,
                "Argument {$this->getDeclaringFunction()}({$this}) has neither type nor default value",
            );
        }

        if (null !== $container) {
            try {
                return $this->getNamedValueFromContainer($container);
            } catch (ParameterException|NotFoundExceptionInterface $exception) {
            }
        }

        try {
            return $this->createNamedValue($container);
        } catch (ParameterException $exception) {
        }

        if ($hasDefault) {
            return parent::getDefaultValue();
        }

        if ($type->allowsNull()) {
            return null;
        }

        throw new ParameterException(
            parameter: $this,
            message: "Argument {$this->getDeclaringFunction()}({$this}) not resolved",
            previous: $exception,
        );
    }

    #[\Override]
    public function getDeclaringFunction(): \ReflectionFunctionAbstract
    {
        $reflection = parent::getDeclaringFunction();

        if ($reflection instanceof \ReflectionMethod) {
            return $reflection->isClosure()
                ? new ReflectionFunction($reflection->getClosure())
                : new ReflectionMethod(
                    $reflection->class,
                    $reflection->name,
                );
        }

        if ($reflection instanceof \ReflectionFunction) {
            /** @psalm-suppress ArgumentTypeCoercion */
            return new ReflectionFunction(
                $reflection->isClosure()
                    ? $reflection->getClosure()
                    : $reflection->getName(),
            );
        }

        return $reflection;
    }

    /**
     * @psalm-capabilities read-props
     */
    #[\Override]
    public function getDeclaringClass(): ?ReflectionClass
    {
        $function = parent::getDeclaringFunction();

        if ($function->isClosure()) {
            return null;
        }

        $class = parent::getDeclaringClass();

        return null !== $class
            ? new ReflectionClass($class->name)
            : null;
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function __toString(): string
    {
        $return = '$' . $this->getName();

        if ($this->isVariadic()) {
            $return = '...' . $return;
        }

        if ($this->isPassedByReference()) {
            $return = '&' . $return;
        }

        if ($this->hasType()) {
            $return = (string) $this->getType() . ' ' . $return;
        }

        if (!$this->isDefaultValueAvailable()) {
            return $return;
        }

        /** @var mixed $default */
        $default = parent::getDefaultValue();

        if ($default instanceof \UnitEnum) {
            return $return . ' = ' . $default::class . '::' . $default->name;
        }

        if (\is_object($default)) {
            return $return . ' = new ' . $default::class . '()';
        }

        return $return . ' = ' . \var_export($default, true);
    }

    private function getNamedValueFromContainer(ContainerInterface $container): object
    {
        $types = $this->getTypes();
        $isMismatch = false;

        foreach ($types->getClassNames() as $name) {
            if (!$container->has($name)) {
                continue;
            }

            try {
                $result = $container->get($name);
            } catch (NotFoundExceptionInterface) {
                continue;
            }

            if (!\is_object($result)) {
                $isMismatch = true;
                continue;
            }

            if ($types->match($result)) {
                return $result;
            }

            $isMismatch = true;
        }

        if ($isMismatch) {
            throw new ParameterException(
                $this,
                "Argument {$this->getDeclaringFunction()}({$this}), retrieved from Container, does not match required type {$types}",
            );
        }

        throw new class (
            $this,
            "Argument {$this->getDeclaringFunction()}({$this}) was not found in the Container",
        ) extends ParameterException implements NotFoundExceptionInterface {};
    }

    private function createNamedValue(?ContainerInterface $container = null): object
    {
        $types = $this->getTypes();

        foreach ($types->getClassNames() as $name) {
            if (!\class_exists($name, true)) {
                continue;
            }

            try {
                $class = new ReflectionClass($name);

                if (!$class->isInstantiable()) {
                    continue;
                }

                $constructor = $class->getConstructor();

                if (null === $constructor) {
                    $instance = $class->newInstanceWithoutConstructor();
                    if ($types->match($instance)) {
                        return $instance;
                    }
                    continue;
                }

                $canUseLazyGhost = !$class->isInternal() && !$class->isReadOnly();

                $instance = (false === $canUseLazyGhost)
                    ? $class->newInstanceArgs(
                        $constructor->getParameters($container),
                    )
                    : $class->newLazyGhost(static function (
                        object $instance,
                    ) use ($container, $constructor): void {
                        $arguments = $constructor->getParameters($container);
                        $constructor->invokeArgs($instance, $arguments);
                    });

                if ($types->match($instance)) {
                    return $instance;
                }
            } catch (\ReflectionException) {
                continue;
            }
        }

        throw new class (
            $this,
            "Argument {$this->getDeclaringFunction()}({$this}) could not be created",
        ) extends ParameterException {};
    }

    private function getTypes(): TypeInterface
    {
        $type = parent::getType();

        if (null === $type) {
            throw new ParameterException(
                $this,
                "Unable to resolve argument {$this->getDeclaringFunction()}({$this}) without a type",
            );
        }

        return TypeFactory::create($type, $this);
    }
}
