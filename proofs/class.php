<?php
declare(strict_types = 1);

use Innmind\Type\{
    ClassName,
    Primitive,
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

    yield test(
        'ClassName::allows()',
        static function($assert) {
            $type = ClassName::of(\stdClass::class);

            $assert->true($type->allows(new \stdClass));
            $assert->false($type->allows(new class {
            }));
        },
    );
    yield test(
        'ClassName::accepts()',
        static function($assert) {
            $type = ClassName::of(\stdClass::class);

            $assert->true($type->accepts(ClassName::of(\stdClass::class)));
            $assert->false($type->accepts(ClassName::of(\Iterator::class)));

            $type = ClassName::of(\Traversable::class);

            $assert->true($type->accepts(ClassName::of(\Iterator::class)));
            $assert->true($type->accepts(ClassName::of(\IteratorAggregate::class)));
            $assert->true($type->accepts(Union::of(
                ClassName::of(\IteratorAggregate::class),
                ClassName::of(\Iterator::class),
            )));
            $assert->true($type->accepts(Intersection::of(
                ClassName::of(\IteratorAggregate::class),
                ClassName::of(\Iterator::class),
            )));
        },
    );
    yield proof(
        'ClassName::accepts() fail on primitives',
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
        )),
        static function($assert, $primitive) {
            $type = ClassName::of(\stdClass::class);

            $assert->false($type->accepts($primitive));
        },
    );
};
