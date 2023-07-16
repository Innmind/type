<?php
declare(strict_types = 1);

use Innmind\Type\{
    Nullable,
    Primitive,
    ClassName,
    Union,
    Intersection,
};
use Innmind\BlackBox\Set;

return static function() {
    $primitives = Set\Elements::of(
        Primitive::string(),
        Primitive::int(),
        Primitive::float(),
        Primitive::bool(),
        Primitive::array(),
        Primitive::object(),
        Primitive::resource(),
    );
    $classes = Set\Elements::of(
        \ArrayObject::class,
        \Iterator::class,
        \Countable::class,
    )->map(ClassName::of(...));

    yield proof(
        'Nullable::allows()',
        given(Set\Either::any(
            Set\Composite::immutable(
                static fn(...$args) => $args,
                Set\Nullable::of(Set\Strings::any()),
                Set\Elements::of(Primitive::string()),
            ),
            Set\Composite::immutable(
                static fn(...$args) => $args,
                Set\Nullable::of(Set\Integers::any()),
                Set\Elements::of(Primitive::int()),
            ),
            Set\Composite::immutable(
                static fn(...$args) => $args,
                Set\Nullable::of(Set\Elements::of(new \ArrayObject)),
                Set\Elements::of(ClassName::of(\ArrayObject::class)),
            ),
            Set\Composite::immutable(
                static fn(...$args) => $args,
                Set\Nullable::of(Set\Either::any(
                    Set\Integers::any(),
                    Set\RealNumbers::any(),
                )),
                Set\Elements::of(Union::of(
                    Primitive::int(),
                    Primitive::float(),
                )),
            ),
            Set\Composite::immutable(
                static fn(...$args) => $args,
                Set\Nullable::of(Set\Elements::of(new \ArrayObject)),
                Set\Elements::of(Intersection::of(
                    ClassName::of(\Countable::class),
                    ClassName::of(\IteratorAggregate::class),
                )),
            ),
        )),
        static function($assert, $pair) {
            [$value, $type] = $pair;

            $assert->true(Nullable::of($type)->allows($value));
        },
    );
    yield proof(
        'Nullable::allows() failure',
        given(Set\Either::any(
            Set\Composite::immutable(
                static fn(...$args) => $args,
                Set\Integers::any(),
                Set\Elements::of(Primitive::string()),
            ),
            Set\Composite::immutable(
                static fn(...$args) => $args,
                Set\Strings::any(),
                Set\Elements::of(Primitive::int()),
            ),
            Set\Composite::immutable(
                static fn(...$args) => $args,
                Set\Strings::any(),
                Set\Elements::of(ClassName::of(\ArrayObject::class)),
            ),
            Set\Composite::immutable(
                static fn(...$args) => $args,
                Set\Strings::any(),
                Set\Elements::of(Union::of(
                    Primitive::int(),
                    Primitive::float(),
                )),
            ),
            Set\Composite::immutable(
                static fn(...$args) => $args,
                Set\Elements::of(new \SplObjectStorage),
                Set\Elements::of(Intersection::of(
                    ClassName::of(\Countable::class),
                    ClassName::of(\IteratorAggregate::class),
                )),
            ),
        )),
        static function($assert, $pair) {
            [$invalid, $type] = $pair;

            $assert->false(Nullable::of($type)->allows($invalid));
        },
    );
    yield proof(
        'Nullable::accepts()',
        given(Set\Either::any(
            $primitives,
            $classes,
            Set\Composite::immutable(
                Union::of(...),
                Set\Either::any($primitives, $classes),
                Set\Either::any($primitives, $classes),
            ),
            Set\Composite::immutable(
                Intersection::of(...),
                $classes,
                $classes,
            ),
        )),
        static function($assert, $type) {
            $nullable = Nullable::of($type);

            $assert->true($nullable->accepts($nullable));
            $assert->true($nullable->accepts(Nullable::of($type)));
            $assert->true($nullable->accepts($type));
        },
    );
    yield proof(
        'Nullable::accepts() failure',
        given(Set\Either::any(
            Set\Composite::immutable(
                static fn(...$args) => $args,
                Set\Elements::of(Primitive::int()),
                Set\Elements::of(Primitive::string()),
            ),
            Set\Composite::immutable(
                static fn(...$args) => $args,
                Set\Elements::of(Primitive::string()),
                Set\Elements::of(Primitive::int()),
            ),
            Set\Composite::immutable(
                static fn(...$args) => $args,
                Set\Elements::of(Primitive::string()),
                Set\Elements::of(ClassName::of(\Countable::class)),
            ),
        )),
        static function($assert, $pair) {
            [$type, $incompatible] = $pair;
            $nullable = Nullable::of($type);

            $assert->false($nullable->accepts($incompatible));
            $assert->false($nullable->accepts(Nullable::of($incompatible)));
        },
    );
};
