<?php

declare(strict_types=1);

namespace Sabroan\DI\Reflection;

/**
 * @api
 */
final class ReflectionFunction extends \ReflectionFunction
{
    use ReflectionFunctionAbstractTrait;

    /**
     * @psalm-capabilities read-props
     * @psalm-suppress ImmutableDependency
     */
    #[\Override]
    public function __toString(): string
    {
        if ($this->isClosure()) {
            $file = $this->getFileName();
            $line = $this->getStartLine();
            if (false !== $file && false !== $line) {
                return "{$file}:{$line}";
            }
        }

        /** @var non-empty-string */
        return $this->name;
    }
}
