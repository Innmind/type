<?php
declare(strict_types = 1);

namespace Innmind\Type;

/**
 * @template A
 * @template B
 * @implements Type<A|B>
 */
final class Union implements Type
{
    /** @var Type<A> */
    private Type $left;
    /** @var Type<B> */
    private Type $right;

    /**
     * @param Type<A> $left
     * @param Type<B> $right
     */
    private function __construct(Type $left, Type $right)
    {
        $this->left = $left;
        $this->right = $right;
    }

    /**
     * @template C
     * @template D
     *
     * @param Type<C> $left
     * @param Type<D> $right
     *
     * @return self<C, D>
     */
    public static function of(Type $left, Type $right): self
    {
        return new self($left, $right);
    }

    /**
     * @return Type<A>
     */
    public function left(): Type
    {
        return $this->left;
    }

    /**
     * @return Type<B>
     */
    public function right(): Type
    {
        return $this->right;
    }

    public function allows(mixed $value): bool
    {
        return $this->left->allows($value) || $this->right->allows($value);
    }

    public function accepts(Type $type): bool
    {
        if ($type instanceof self) {
            // (int|float) E (int|float)
            if ($this->left->accepts($type->left())) {
                return $this->right->accepts($type->right());
            }

            // (int|float) E (float|int)
            if ($this->left->accepts($type->right())) {
                return $this->right->accepts($type->left());
            }

            // verify $type can't be _larger_ than $this
            return $this->accepts($type->left()) && $this->accepts($type->right());
        }

        return $this->left->accepts($type) || $this->right->accepts($type);
    }

    public function toString(): string
    {
        $left = $this->left->toString();
        $right = $this->right->toString();

        if ($this->left instanceof Intersection) {
            $left = "($left)";
        }

        if ($this->right instanceof Intersection) {
            $right = "($right)";
        }

        return $left.'|'.$right;
    }
}
