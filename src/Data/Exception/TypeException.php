<?php

declare(strict_types=1);

namespace Sabroan\DI\Reflection\Data\Exception;

use ReflectionType;
use Throwable;

final class TypeException extends \ReflectionException
{
    /** @psalm-suppress PossiblyUnusedProperty */
    public readonly ?\ReflectionType $type;

    /**
     * @psalm-pure
     */
    public function __construct(
        ?\ReflectionType $type,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        $this->type = $type;
        parent::__construct($message, $code, $previous);
    }
}
