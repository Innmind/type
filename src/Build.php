<?php
declare(strict_types = 1);

namespace Innmind\Type;

final class Build
{
    private function __construct()
    {
    }

    /**
     * @psalm-pure
     */
    public static function fromReflection(?\ReflectionType $type): Type
    {
        return match ($type) {
            null => Primitive::mixed(),
            default => self::analyze($type),
        };
    }

    /**
     * @psalm-pure
     */
    private static function analyze(\ReflectionType $refl): Type
    {
        $type = match ($refl::class) {
            \ReflectionNamedType::class => self::ofNamed($refl),
            \ReflectionUnionType::class => self::ofUnion($refl),
            \ReflectionIntersectionType::class => self::ofIntersection($refl),
        };

        if ($type->toString() === 'mixed') {
            return $type;
        }

        return match ($refl->allowsNull()) {
            true => Nullable::of($type),
            false => $type,
        };
    }

    /**
     * @psalm-pure
     */
    private static function ofNamed(\ReflectionNamedType $refl): Type
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        return match ($refl->getName()) {
            'string' => Primitive::string(),
            'int' => Primitive::int(),
            'float' => Primitive::float(),
            'bool' => Primitive::bool(),
            'array' => Primitive::array(),
            'object' => Primitive::object(),
            'mixed' => Primitive::mixed(),
            'self', 'static' => ClassName::of($refl->getName()),
            default => match ((new \ReflectionClass($refl->getName()))->isEnum()) {
                true => ClassName::ofEnum($refl->getName()),
                false => ClassName::of($refl->getName()),
            },
        };
    }

    /**
     * @psalm-pure
     */
    private static function ofUnion(\ReflectionUnionType $refl): Type
    {
        $types = \array_filter(
            $refl->getTypes(),
            static fn($type) => match ($type::class) {
                \ReflectionNamedType::class => $type->getName() !== 'null',
                default => true,
            },
        );
        $types = \array_map(self::analyze(...), $types);
        $initial = \array_shift($types);

        /**
         * @psalm-suppress InvalidArgument
         * @var Type
         */
        return \array_reduce(
            $types,
            Union::of(...),
            $initial,
        );
    }

    /**
     * @psalm-pure
     */
    private static function ofIntersection(\ReflectionIntersectionType $refl): Type
    {
        $types = \array_map(self::analyze(...), $refl->getTypes());
        $initial = \array_shift($types);

        return \array_reduce(
            $types,
            Intersection::of(...),
            $initial,
        );
    }
}
