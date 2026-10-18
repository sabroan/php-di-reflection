<?php

declare(strict_types=1);

namespace Sabroan\DI\Reflection\Tests\Fixtures;

class DummyClassChild extends DummyClassParent
{
    public function __construct() {}

    public function childPublicMethod(): void {}

    #[\Override]
    public function instanceMethod(): void {}

    public function parentKeywordMethod(parent $param): void {}

    public function selfMethod(self $param): void {}

    public static function staticMethod(): void {}
}
