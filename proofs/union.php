<?php
declare(strict_types = 1);

use Innmind\Type\{
    Union,
    Primitive,
};
use Innmind\BlackBox\Set;

return static function() {
    yield proof(
        'Union::allows()',
        given(Set\Either::any(
            Set\Integers::any(),
            Set\RealNumbers::any(),
        )),
        static function($assert, $value) {
            $type = Union::of(
                Primitive::int(),
                Primitive::float(),
            );

            $assert->true($type->allows($value));
        },
    );
    yield proof(
        'Union::allows() failure',
        given(Set\Either::any(
            Set\Strings::any(),
            Set\Elements::of(true, false, null, new stdClass),
        )),
        static function($assert, $value) {
            $type = Union::of(
                Primitive::int(),
                Primitive::float(),
            );

            $assert->false($type->allows($value));
        },
    );
    yield proof(
        'Union::accepts()',
        given(Set\Elements::of(
            Primitive::string(),
            Primitive::bool(),
            Primitive::array(),
            Primitive::object(),
            Primitive::resource(),
        )),
        static function($assert, $failure) {
            $type = Union::of(
                Primitive::int(),
                Primitive::float(),
            );

            $assert->true($type->accepts($type));
            $assert->true($type->accepts(Union::of(
                Primitive::float(),
                Primitive::int(),
            )));
            $assert->true($type->accepts(Union::of(
                Primitive::float(),
                Union::of(
                    Primitive::int(),
                    Primitive::float(),
                ),
            )));
            $assert->true($type->accepts(Primitive::int()));
            $assert->true($type->accepts(Primitive::float()));
            $assert->false($type->accepts($failure));
            $assert->false($type->accepts(Union::of(
                Primitive::float(),
                Union::of(
                    Primitive::int(),
                    Primitive::string(),
                ),
            )));
        },
    );
};
