<?php
declare(strict_types = 1);

namespace Innmind\Type;

/**
 * @template A
 * @implements Type<?A>
 */
final class Nullable implements Type
{
    /** @var Type<A> */
    private Type $type;

    /**
     * @param Type<A> $type
     */
    private function __construct(Type $type)
    {
        $this->type = $type;
    }

    /**
     * @template B
     *
     * @param Type<B> $type
     *
     * @return self<B>
     */
    public static function of(Type $type): self
    {
        return new self($type);
    }

    public function allows(mixed $value): bool
    {
        return match ($value) {
            null => true,
            default => $this->type->allows($value),
        };
    }

    public function accepts(Type $type): bool
    {
        return false; // todo
    }

    public function toString(): string
    {
        if ($this->type instanceof Union) {
            return 'null|'.$this->type->toString();
        }

        if ($this->type instanceof Intersection) {
            return \sprintf('null|(%s)', $this->type->toString());
        }

        return '?'.$this->type->toString();
    }
}