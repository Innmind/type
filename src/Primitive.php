<?php
declare(strict_types = 1);

namespace Innmind\Type;

/**
 * @template A
 * @implements Type<A>
 */
final class Primitive implements Type
{
    /** @var callable(mixed): bool */
    private $allows;
    /** @var non-empty-string */
    private string $kind;

    /**
     * @param callable(mixed): bool $allows
     * @param non-empty-string $kind
     */
    private function __construct(callable $allows, string $kind)
    {
        $this->allows = $allows;
        $this->kind = $kind;
    }

    /**
     * @return self<string>
     */
    public static function string(): self
    {
        /** @var self<string> */
        return new self(\is_string(...), 'string');
    }

    /**
     * @return self<int>
     */
    public static function int(): self
    {
        /** @var self<int> */
        return new self(\is_int(...), 'int');
    }

    /**
     * @return self<float>
     */
    public static function float(): self
    {
        /** @var self<float> */
        return new self(\is_float(...), 'float');
    }

    /**
     * @return self<bool>
     */
    public static function bool(): self
    {
        /** @var self<bool> */
        return new self(\is_bool(...), 'bool');
    }

    /**
     * @return self<array>
     */
    public static function array(): self
    {
        /** @var self<array> */
        return new self(\is_array(...), 'array');
    }

    /**
     * @return self<object>
     */
    public static function object(): self
    {
        /** @var self<object> */
        return new self(\is_object(...), 'object');
    }

    /**
     * @return self<resource>
     */
    public static function resource(): self
    {
        /** @var self<resource> */
        return new self(\is_resource(...), 'resource');
    }

    /**
     * @return self<mixed>
     */
    public static function mixed(): self
    {
        /** @var self<mixed> */
        return new self(static fn() => true, 'mixed');
    }

    public function allows(mixed $value): bool
    {
        return ($this->allows)($value);
    }

    public function accepts(Type $type): bool
    {
        if ($this->kind === 'mixed') {
            return true;
        }

        if ($this->kind === 'object' && $type instanceof ClassName) {
            return true;
        }

        if ($this->kind === 'object' && $type instanceof Union) {
            return $this->accepts($type->left()) && $this->accepts($type->right());
        }

        if ($this->kind === 'object' && $type instanceof Intersection) {
            return $this->accepts($type->left()) && $this->accepts($type->right());
        }

        return $type instanceof self && $type->kind === $this->kind;
    }

    public function toString(): string
    {
        return $this->kind;
    }
}
