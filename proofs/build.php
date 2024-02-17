<?php
declare(strict_types = 1);

use Innmind\Type\Build;
use Fixtures\Innmind\Type\Example;
use Innmind\BlackBox\Set;

return static function() {
    yield test(
        'Build no type is coalesced to mixed',
        static function($assert) {
            $refl = new ReflectionProperty(Example::class, 'unknown');
            $type = Build::fromReflection($refl->getType());

            $assert
                ->expected('mixed')
                ->same($type->toString());
        },
    );
    yield proof(
        'Build primitive',
        given(Set\Elements::of(
            'string',
            'int',
            'float',
            'bool',
            'array',
            'object',
            'mixed',
        )),
        static function($assert, $primitive) {
            $refl = new ReflectionProperty(Example::class, $primitive);
            $type = Build::fromReflection($refl->getType());

            $assert
                ->expected($primitive)
                ->same($type->toString());
        },
    );
    yield test(
        'Build nullable primitive',
        static function($assert) {
            $refl = new ReflectionProperty(Example::class, 'nullable');
            $type = Build::fromReflection($refl->getType());

            $assert
                ->expected('?int')
                ->same($type->toString());
        },
    );
    yield test(
        'Build class',
        static function($assert) {
            $refl = new ReflectionProperty(Example::class, 'class');
            $type = Build::fromReflection($refl->getType());

            $assert
                ->expected('stdClass')
                ->same($type->toString());
        },
    );
    yield test(
        'Build union',
        static function($assert) {
            $refl = new ReflectionProperty(Example::class, 'union');
            $type = Build::fromReflection($refl->getType());

            $assert
                ->expected('stdClass|self')
                ->same($type->toString());
        },
    );
    yield test(
        'Build intersection',
        static function($assert) {
            $refl = new ReflectionProperty(Example::class, 'intersection');
            $type = Build::fromReflection($refl->getType());

            $assert
                ->expected('ArrayAccess&Iterator')
                ->same($type->toString());
        },
    );
    yield test(
        'Build intersection and union',
        static function($assert) {
            $refl = new ReflectionProperty(Example::class, 'intersectionAndUnion');
            $type = Build::fromReflection($refl->getType());

            $assert
                ->expected('(ArrayAccess&Iterator)|self')
                ->same($type->toString());
        },
    );
    yield test(
        'Build nullable intersection and union',
        static function($assert) {
            $refl = new ReflectionProperty(Example::class, 'nullableIntersectionAndUnion');
            $type = Build::fromReflection($refl->getType());

            $assert
                ->expected('null|(ArrayAccess&Iterator)|self')
                ->same($type->toString());
        },
    );
};
