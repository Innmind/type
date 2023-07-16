# type

[![Build Status](https://github.com/innmind/type/workflows/CI/badge.svg?branch=main)](https://github.com/innmind/type/actions?query=workflow%3ACI)
[![codecov](https://codecov.io/gh/innmind/type/branch/develop/graph/badge.svg)](https://codecov.io/gh/innmind/type)
[![Type Coverage](https://shepherd.dev/github/innmind/type/coverage.svg)](https://shepherd.dev/github/innmind/type)

This package allows to describe types as objects to check if a given type can accept a value or if it is compatible with another type.

## Installation

```sh
composer require innmind/type
```

## Usage

```php
use Innmind\Type\{
    Build,
    Primitive,
};

final class Example
{
    private int $id;
}

$type = Build::fromReflection((new \ReflectionProperty(Example::class, 'id'))->getType());
$type->allows(42); // true
$type->allows('some-uuid'); // false

$type->accepts(Primitive::int()); // true
$type->accepts(Primitive::string()); // false
```
