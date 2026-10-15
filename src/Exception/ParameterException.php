<?php

declare(strict_types=1);

namespace Sabroan\DI\Exception;

class ParameterException extends \ReflectionException
{
    public readonly \ReflectionParameter $parameter;

    public function __construct(
        \ReflectionParameter $parameter,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        $this->parameter = $parameter;
        parent::__construct($message, $code, $previous);
    }
}
