<?php
declare(strict_types = 1);

namespace Innmind\Type;

/**
 * @psalm-immutable
 * @template A
 * @implements Type<A>
 */
final class Primitive implements Type
{
    /** @var pure-callable(mixed): bool */
    private $allows;
    /** @var non-empty-string */
    private string $kind;

    /**
     * @param pure-callable(mixed): bool $allows
     * @param non-empty-string $kind
     */
    private function __construct(callable $allows, string $kind)
    {
        $this->allows = $allows;
        $this->kind = $kind;
    }

    /**
     * @psalm-pure
     *
     * @return self<string>
     */
    public static function string(): self
    {
        /** @var self<string> */
        return new self(\is_string(...), 'string');
    }

    /**
     * @psalm-pure
     *
     * @return self<int>
     */
    public static function int(): self
    {
        /** @var self<int> */
        return new self(\is_int(...), 'int');
    }

    /**
     * @psalm-pure
     *
     * @return self<float>
     */
    public static function float(): self
    {
        /** @var self<float> */
        return new self(\is_float(...), 'float');
    }

    /**
     * @psalm-pure
     *
     * @return self<bool>
     */
    public static function bool(): self
    {
        /** @var self<bool> */
        return new self(\is_bool(...), 'bool');
    }

    /**
     * @psalm-pure
     *
     * @return self<array>
     */
    public static function array(): self
    {
        /** @var self<array> */
        return new self(\is_array(...), 'array');
    }

    /**
     * @psalm-pure
     *
     * @return self<object>
     */
    public static function object(): self
    {
        /** @var self<object> */
        return new self(\is_object(...), 'object');
    }

    /**
     * @psalm-pure
     *
     * @return self<resource>
     */
    public static function resource(): self
    {
        /** @var self<resource> */
        return new self(\is_resource(...), 'resource');
    }

    /**
     * @psalm-pure
     *
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
