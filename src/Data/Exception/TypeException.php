<?php

declare(strict_types=1);

namespace Sabroan\DI\Data\Exception;

class TypeException extends \ReflectionException
{
    public readonly ?\ReflectionType $type;

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
