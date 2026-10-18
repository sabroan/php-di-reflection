<?php

declare(strict_types=1);

namespace Sabroan\DI\Reflection\Data\Type;

/**
 * @psalm-immutable
 */
final readonly class NamedType extends AbstractType
{
    /**
     * @phpstan-pure
     */
    public function __construct(
        public \ReflectionNamedType $type,
        ?\ReflectionParameter $parameter = null,
    ) {
        $class = static::getClassName($type, $parameter);
        $classes = null === $class ? [] : [$class];
        parent::__construct($type, $classes);
    }

    /**
     * @psalm-mutation-free
     */
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

    /**
     * @psalm-pure
     * @return class-string|null
     */
    private static function getClassName(
        \ReflectionNamedType $type,
        ?\ReflectionParameter $parameter,
    ): ?string {
        if ($type->isBuiltin()) {
            return null;
        }

        $name = $type->getName();

        if (null === $parameter) {
            /** @var class-string $name */
            return $name;
        }

        $parent = $parameter->getDeclaringClass()?->getParentClass();
        $class = match (\strtolower($name)) {
            'parent'         => (false !== $parent) ? $parent?->getName() : null,
            'self', 'static' => $parameter->getDeclaringClass()?->getName(),
            default          => $name,
        };

        /** @var class-string|null $class */
        return $class;
    }
}
