<?php

declare(strict_types=1);

namespace Sabroan\DI\Data;

use Sabroan\DI\Data\Exception\TypeException;
use Sabroan\DI\Data\Type\IntersectionType;
use Sabroan\DI\Data\Type\NamedType;
use Sabroan\DI\Data\Type\TypeInterface;
use Sabroan\DI\Data\Type\UnionType;

class TypeFactory
{
    public static function create(
        \ReflectionType $type,
        ?\ReflectionParameter $parameter = null,
    ): TypeInterface {
        return match (true) {
            $type instanceof \ReflectionIntersectionType    => new IntersectionType($type, $parameter),
            $type instanceof \ReflectionUnionType           => new UnionType($type, $parameter),
            $type instanceof \ReflectionNamedType           => new NamedType($type, $parameter),
            default                                         => throw new TypeException($type, 'Unknown type'),
        };
    }
}
