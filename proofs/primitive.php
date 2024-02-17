<?php
declare(strict_types = 1);

use Innmind\Type\{
    Primitive,
    Union,
    Intersection,
    Nullable,
    ClassName,
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

    yield proof(
        'Primitive::string()->allows()',
        given(
            Set\Strings::madeOf(Set\Unicode::any()),
            Set\Either::any(
                Set\Integers::any(),
                Set\RealNumbers::any(),
                Set\Elements::of(true, false, null, new stdClass),
            ),
        ),
        static function($assert, $string, $nonString) {
            $type = Primitive::string();

            $assert->true($type->allows($string));
            $assert->false($type->allows($nonString));
        },
    );
    yield proof(
        'Primitive::string()->accepts()',
        given(Set\Elements::of(
            Primitive::int(),
            Primitive::float(),
            Primitive::bool(),
            Primitive::array(),
            Primitive::object(),
            Primitive::resource(),
            Primitive::mixed(),
        )),
        static function($assert, $type) {
            $string = Primitive::string();

            $assert->true($string->accepts($string));
            $assert->true($string->accepts(Primitive::string()));
            $assert->false($string->accepts($type));
        },
    );
    yield proof(
        'Primitive::int()->accepts()',
        given(Set\Elements::of(
            Primitive::string(),
            Primitive::float(),
            Primitive::bool(),
            Primitive::array(),
            Primitive::object(),
            Primitive::resource(),
            Primitive::mixed(),
        )),
        static function($assert, $type) {
            $int = Primitive::int();

            $assert->true($int->accepts($int));
            $assert->true($int->accepts(Primitive::int()));
            $assert->false($int->accepts($type));
        },
    );
    yield proof(
        'Primitive::float()->accepts()',
        given(Set\Elements::of(
            Primitive::string(),
            Primitive::int(),
            Primitive::bool(),
            Primitive::array(),
            Primitive::object(),
            Primitive::resource(),
            Primitive::mixed(),
        )),
        static function($assert, $type) {
            $float = Primitive::float();

            $assert->true($float->accepts($float));
            $assert->true($float->accepts(Primitive::float()));
            $assert->false($float->accepts($type));
        },
    );
    yield proof(
        'Primitive::bool()->accepts()',
        given(Set\Elements::of(
            Primitive::string(),
            Primitive::int(),
            Primitive::float(),
            Primitive::array(),
            Primitive::object(),
            Primitive::resource(),
            Primitive::mixed(),
        )),
        static function($assert, $type) {
            $bool = Primitive::bool();

            $assert->true($bool->accepts($bool));
            $assert->true($bool->accepts(Primitive::bool()));
            $assert->false($bool->accepts($type));
        },
    );
    yield proof(
        'Primitive::array()->accepts()',
        given(Set\Elements::of(
            Primitive::string(),
            Primitive::int(),
            Primitive::float(),
            Primitive::bool(),
            Primitive::object(),
            Primitive::resource(),
            Primitive::mixed(),
        )),
        static function($assert, $type) {
            $array = Primitive::array();

            $assert->true($array->accepts($array));
            $assert->true($array->accepts(Primitive::array()));
            $assert->false($array->accepts($type));
        },
    );
    yield proof(
        'Primitive::object()->accepts()',
        given(Set\Elements::of(
            Primitive::string(),
            Primitive::int(),
            Primitive::float(),
            Primitive::bool(),
            Primitive::array(),
            Primitive::resource(),
            Primitive::mixed(),
        )),
        static function($assert, $type) {
            $object = Primitive::object();

            $assert->true($object->accepts($object));
            $assert->true($object->accepts(Primitive::object()));
            $assert->false($object->accepts($type));
        },
    );
    yield test(
        'Primitive::object()->accepts() any class',
        static function($assert) {
            $object = Primitive::object();

            $assert->true($object->accepts(ClassName::of(stdClass::class)));
        },
    );
    yield proof(
        'Primitive::object()->accepts() unions',
        given(Set\Elements::of(
            Primitive::string(),
            Primitive::int(),
            Primitive::float(),
            Primitive::bool(),
            Primitive::array(),
            Primitive::resource(),
            Primitive::mixed(),
        )),
        static function($assert, $type) {
            $object = Primitive::object();

            $assert->true($object->accepts(Union::of(
                ClassName::of(stdClass::class),
                ClassName::of(stdClass::class),
            )));
            $assert->false($object->accepts(Union::of(
                ClassName::of(stdClass::class),
                $type,
            )));
        },
    );
    yield proof(
        'Primitive::object()->accepts() intersections',
        given(Set\Elements::of(
            Primitive::string(),
            Primitive::int(),
            Primitive::float(),
            Primitive::bool(),
            Primitive::array(),
            Primitive::resource(),
            Primitive::mixed(),
        )),
        static function($assert, $type) {
            $object = Primitive::object();

            $assert->true($object->accepts(Intersection::of(
                ClassName::of(stdClass::class),
                ClassName::of(stdClass::class),
            )));
            $assert->false($object->accepts(Intersection::of(
                ClassName::of(stdClass::class),
                $type,
            )));
        },
    );
    yield proof(
        'Primitive::resource()->accepts()',
        given(Set\Elements::of(
            Primitive::string(),
            Primitive::int(),
            Primitive::float(),
            Primitive::bool(),
            Primitive::array(),
            Primitive::object(),
            Primitive::mixed(),
        )),
        static function($assert, $type) {
            $resource = Primitive::resource();

            $assert->true($resource->accepts($resource));
            $assert->true($resource->accepts(Primitive::resource()));
            $assert->false($resource->accepts($type));
        },
    );
    yield proof(
        'Primitive::mixed()->accepts()',
        given(Set\Either::any(
            $primitives,
            Set\Composite::immutable(
                Union::of(...),
                $primitives,
                $primitives,
            ),
            Set\Composite::immutable(
                Intersection::of(...),
                $primitives,
                $primitives,
            ),
            $primitives->map(Nullable::of(...)),
        )),
        static function($assert, $type) {
            $mixed = Primitive::mixed();

            $assert->true($mixed->accepts($mixed));
            $assert->true($mixed->accepts(Primitive::mixed()));
            $assert->true($mixed->accepts($type));
        },
    );
};
