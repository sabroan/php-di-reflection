# DI Reflection

A reflection wrapper library for dependency injection (DI) containers and auto-wiring engines.

Built on PHP's native Reflection API, it extends standard reflection capabilities to resolve dependencies through PSR-11 containers, automatically instantiate missing dependencies, match complex type declarations, and support lazy initialization using Lazy Ghost objects.

## Features

- **PSR-11 Container Integration:** Resolves typed class dependencies through any PSR-11-compatible container.

- **Automatic Dependency Resolution:** Automatically instantiates eligible dependencies that are not available in the container, recursively resolving their dependencies where possible.

- **Lazy Ghost Support:** Leverages `ReflectionClass::newLazyGhost()` for deferred, memory-efficient initialization of eligible classes.

- **Comprehensive Type Matching:** Supports named, union, and intersection types, including relative type declarations such as `self`, `static`, and `parent`.

## Installation

Install the package via Composer:

`composer require sabroan/di-reflection`

## Quick Start
### Resolving Classes
```php
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Sabroan\DI\Reflection\ReflectionClass;

readonly class LoggerService {
    public function __construct(
        public LoggerInterface $logger
    ) {
        $logger->log('Hello, world!');
    }
}

$class = new ReflectionClass(LoggerService::class);

$constructor = $class->getConstructor();

$service = (null === $constructor)
    ? $class->newInstance()
    : $class->newLazyGhost(
        static fn (object $ghost) => $ghost->__construct(
            /** @var ContainerInterface $container */
            ...$constructor->getParameters($container)
        )
    );

$service->logger->log('WoW');
```

### Resolving Methods
```php
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Sabroan\DI\Reflection\ReflectionClass;

readonly class Greeter {
    public function greet(LoggerInterface $logger) {
        $logger->log('Hello, world!');
    }
}

$method = new ReflectionMethod(Greeter::class, 'greet');

/** @var ContainerInterface $container */
$arguments = $method->getParameters($container);

$method->invokeArgs(new Greeter(), $arguments);
```

### Resolving Functions
```php
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Sabroan\DI\Reflection\ReflectionFunction;

$function = new ReflectionFunction(
    static function (LoggerInterface $logger): void {
        $logger->log('Hello, world!');
    }
);

/** @var ContainerInterface $container */
$arguments = $method->getParameters($container);

$function->invokeArgs($arguments);
```

### Resolving Parameters
```php
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Sabroan\DI\Reflection\ReflectionParameter;

$closure = static function (LoggerInterface $logger): void {
    $logger->log('Hello, world!');
};

$parameter = new ReflectionParameter($closure, 'logger');

/** @var ContainerInterface $container */
$logger = $parameter->getDefaultValue($container);

assert($logger instanceof LoggerInterface);

$closure($logger);
```

## License
This package is released under the MIT License. See the LICENSE file for details.