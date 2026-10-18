<?php

declare(strict_types=1);

namespace Sabroan\DI\Reflection\Data\Type;

use Sabroan\DI\Reflection\Data\TypeFactory;

/**
 * @psalm-immutable
 */
abstract readonly class AbstractTypes extends AbstractType
{
    /** @var list<TypeInterface> */
    public array $types;

    /**
     * @phpstan-pure
     */
    public function __construct(
        public \ReflectionUnionType|\ReflectionIntersectionType $type,
        ?\ReflectionParameter $parameter = null,
    ) {
        $types = [];
        foreach ($this->type->getTypes() as $current) {
            $types[] = TypeFactory::create($current, $parameter);
        }
        $this->types = $types;

        $classes = [];
        foreach ($this->types as $types) {
            foreach ($types->getClassNames() as $name) {
                $classes[$name] = null;
            }
        }

        parent::__construct($type, \array_keys($classes));
    }
}
