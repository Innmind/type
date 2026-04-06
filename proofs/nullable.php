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
    $primitives = Set::of(
        Primitive::string(),
        Primitive::int(),
        Primitive::float(),
        Primitive::bool(),
        Primitive::array(),
        Primitive::object(),
        Primitive::resource(),
    );
    $classes = Set::of(
        ArrayObject::class,
        Iterator::class,
        Countable::class,
    )->map(ClassName::of(...));

    yield proof(
        'Nullable::allows()',
        given(Set::either(
            Set::compose(
                static fn(...$args) => $args,
                Set::strings()->nullable(),
                Set::of(Primitive::string()),
            ),
            Set::compose(
                static fn(...$args) => $args,
                Set::integers()->nullable(),
                Set::of(Primitive::int()),
            ),
            Set::compose(
                static fn(...$args) => $args,
                Set::of(new ArrayObject)->nullable(),
                Set::of(ClassName::of(ArrayObject::class)),
            ),
            Set::compose(
                static fn(...$args) => $args,
                Set::either(
                    Set::integers(),
                    Set::realNumbers(),
                )->nullable(),
                Set::of(Union::of(
                    Primitive::int(),
                    Primitive::float(),
                )),
            ),
            Set::compose(
                static fn(...$args) => $args,
                Set::of(new ArrayObject)->nullable(),
                Set::of(Intersection::of(
                    ClassName::of(Countable::class),
                    ClassName::of(IteratorAggregate::class),
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
        given(Set::either(
            Set::compose(
                static fn(...$args) => $args,
                Set::integers(),
                Set::of(Primitive::string()),
            ),
            Set::compose(
                static fn(...$args) => $args,
                Set::strings(),
                Set::of(Primitive::int()),
            ),
            Set::compose(
                static fn(...$args) => $args,
                Set::strings(),
                Set::of(ClassName::of(ArrayObject::class)),
            ),
            Set::compose(
                static fn(...$args) => $args,
                Set::strings(),
                Set::of(Union::of(
                    Primitive::int(),
                    Primitive::float(),
                )),
            ),
            Set::compose(
                static fn(...$args) => $args,
                Set::of(new SplObjectStorage),
                Set::of(Intersection::of(
                    ClassName::of(Countable::class),
                    ClassName::of(IteratorAggregate::class),
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
        given(Set::either(
            $primitives,
            $classes,
            Set::compose(
                Union::of(...),
                Set::either($primitives, $classes),
                Set::either($primitives, $classes),
            ),
            Set::compose(
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
        given(Set::either(
            Set::compose(
                static fn(...$args) => $args,
                Set::of(Primitive::int()),
                Set::of(Primitive::string()),
            ),
            Set::compose(
                static fn(...$args) => $args,
                Set::of(Primitive::string()),
                Set::of(Primitive::int()),
            ),
            Set::compose(
                static fn(...$args) => $args,
                Set::of(Primitive::string()),
                Set::of(ClassName::of(Countable::class)),
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
