<?php
declare(strict_types = 1);

namespace Innmind\Type;

/**
 * @template A
 * @template B
 * @implements Type<A&B>
 */
final class Intersection implements Type
{
    /** @var Type<A> */
    private Type $a;
    /** @var Type<B> */
    private Type $b;

    /**
     * @param Type<A> $a
     * @param Type<B> $b
     */
    private function __construct(Type $a, Type $b)
    {
        $this->a = $a;
        $this->b = $b;
    }

    /**
     * @template C
     * @template D
     *
     * @param Type<C> $a
     * @param Type<D> $b
     *
     * @return self<C, D>
     */
    public static function of(Type $a, Type $b): self
    {
        return new self($a, $b);
    }

    /**
     * @return Type<A>
     */
    public function left(): Type
    {
        return $this->a;
    }

    /**
     * @return Type<B>
     */
    public function right(): Type
    {
        return $this->b;
    }

    public function allows(mixed $value): bool
    {
        return $this->a->allows($value) && $this->b->allows($value);
    }

    public function accepts(Type $type): bool
    {
        return false; // todo
    }

    public function toString(): string
    {
        $a = $this->a->toString();
        $b = $this->b->toString();

        if ($this->a instanceof Union) {
            $a = "($a)";
        }

        if ($this->b instanceof Union) {
            $b = "($b)";
        }

        return $a.'&'.$b;
    }
}
