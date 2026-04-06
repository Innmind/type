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
    /**
     * @param pure-Closure(mixed): bool $allows
     * @param non-empty-string $kind
     */
    private function __construct(
        private \Closure $allows,
        private string $kind,
    ) {
    }

    /**
     * @psalm-pure
     *
     * @return self<string>
     */
    #[\NoDiscard]
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
    #[\NoDiscard]
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
    #[\NoDiscard]
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
    #[\NoDiscard]
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
    #[\NoDiscard]
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
    #[\NoDiscard]
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
    #[\NoDiscard]
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
    #[\NoDiscard]
    public static function mixed(): self
    {
        /** @var self<mixed> */
        return new self(static fn() => true, 'mixed');
    }

    #[\Override]
    public function allows(mixed $value): bool
    {
        return ($this->allows)($value);
    }

    #[\Override]
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

    #[\Override]
    public function toString(): string
    {
        return $this->kind;
    }
}
