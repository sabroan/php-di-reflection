<?php

declare(strict_types=1);

namespace Sabroan\DI\Reflection\Data;

use Sabroan\DI\Reflection\Data\Exception\TypeException;
use Sabroan\DI\Reflection\Data\Type\IntersectionType;
use Sabroan\DI\Reflection\Data\Type\NamedType;
use Sabroan\DI\Reflection\Data\Type\TypeInterface;
use Sabroan\DI\Reflection\Data\Type\UnionType;
use Sabroan\DI\Reflection\Exception\ParameterException;

/**
 * @api
 * @psalm-immutable
 */
final readonly class TypeFactory
{
    /**
     * @phpstan-pure
     * @psalm-pure
     */
    public static function create(
        \ReflectionType $type,
        ?\ReflectionParameter $parameter = null,
    ): TypeInterface {
        return match (true) {
            $type instanceof \ReflectionIntersectionType    => new IntersectionType($type, $parameter),
            $type instanceof \ReflectionUnionType           => new UnionType($type, $parameter),
            $type instanceof \ReflectionNamedType           => new NamedType($type, $parameter),
            default                                         => throw new TypeException(
                type: $type,
                message: 'Unknown type',
                previous: (null !== $parameter)
                    ? new ParameterException(
                        parameter: $parameter,
                        message: "Parameter has an unknown or unsupported type",
                    )
                    : null,
            ),
        };
    }
}
