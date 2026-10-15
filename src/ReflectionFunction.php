<?php

declare(strict_types=1);

namespace Sabroan\DI;

class ReflectionFunction extends \ReflectionFunction
{
    use ReflectionFunctionAbstractTrait;

    #[\Override]
    public function __toString(): string
    {
        if ($this->isClosure()) {
            $file = $this->getFileName();
            $line = $this->getStartLine();
            if ($file && $line) {
                return "{$file}:{$line}";
            }
        }
        return $this->name;
    }
}
