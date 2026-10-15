<?php

declare(strict_types=1);

namespace Sabroan\DI\Data\Type;

use Sabroan\DI\Data\TypeFactory;

abstract readonly class AbstractTypes extends AbstractType
{
    public array $types;

    public function __construct(
        public \ReflectionUnionType|\ReflectionIntersectionType $type,
        public ?\ReflectionParameter $parameter = null,
    ) {
        parent::__construct($type);
        $this->types = \array_map(
            static fn(\ReflectionType $type): TypeInterface => TypeFactory::create($type, $parameter),
            $this->type->getTypes()
        );
    }

    public function getClassNames(): iterable
    {
        foreach ($this->types as $types) {
            foreach ($types->getClassNames() as $name) {
                yield $name;
            }
        }
    }
}
