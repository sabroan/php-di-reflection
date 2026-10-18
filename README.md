# DI Reflection

A reflection wrapper library for dependency injection (DI) containers and auto-wiring engines.

Built on PHP's native Reflection API, it extends standard reflection capabilities to resolve dependencies through PSR-11 containers, automatically instantiate missing dependencies, match complex type declarations, and support lazy initialization using PHP 8.4 Lazy Ghost objects.

## Features

- **PSR-11 Container Integration:** Resolves typed class dependencies through any PSR-11-compatible container.

- **Automatic Dependency Resolution:** Automatically instantiates eligible dependencies that are not available in the container, recursively resolving their dependencies where possible.

- **PHP 8.4 Lazy Ghost Support:** Leverages `ReflectionClass::newLazyGhost()` for deferred, memory-efficient initialization of eligible classes.

- **Comprehensive Type Matching:** Supports named, union, and intersection types, including relative type declarations such as `self`, `static`, and `parent`.

## Installation

Install the package via Composer:

`composer require sabroan/di-reflection`

## Quick Start
### Resolving Classes
The `ReflectionClass` allows you to instantiate classes by automatically resolving their constructor dependencies. If a dependency is not available in the container, the resolver can instantiate it automatically, recursively resolving its dependencies where possible.
```php
use Psr\Container\ContainerInterface;
use Sabroan\DI\Reflection\ReflectionClass;

/** @var ContainerInterface $container */
$class = new ReflectionClass(Users::class);

$constructor = $class->getConstructor();

$users = (null === $constructor)
    ? $class->newInstance()
    : $class->newLazyGhost(
        static fn (object $ghost) => $ghost->__construct(
            ...$constructor->getParameters($container)
        )
    );
```

### Resolving Function & Method Arguments

You can automatically extract resolved arguments for any function or method by passing a PSR-11 container instance:
```php
use Psr\Container\ContainerInterface;
use Sabroan\DI\Reflection\ReflectionMethod;

/** @var ContainerInterface $container */

$method = new ReflectionMethod(MyService::class, 'execute');

$arguments = $method->getParameters($container);

$service = new MyService();
$method->invokeArgs($service, $arguments);
```

### Auto-Resolving Individual Parameters

`ReflectionParameter` extends `\ReflectionParameter` and adds container lookup and dynamic instantiation to `getDefaultValue()`:
```php
use Psr\Container\ContainerInterface;
use Sabroan\DI\Reflection\ReflectionParameter;

/** @var ContainerInterface $container */

$parameter = new ReflectionParameter([MyService::class, 'execute'], 'logger');

$value = $parameter->getDefaultValue($container);
```

## License
This package is released under the MIT License. See the LICENSE file for details.