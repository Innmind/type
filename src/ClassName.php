<?php
declare(strict_types = 1);

namespace Innmind\Type;

/**
 * @psalm-immutable
 * @template A of object
 * @implements Type<A>
 */
final class ClassName implements Type
{
    /** @var class-string<A> */
    private string $class;
    private bool $enum;

    /**
     * @param class-string<A> $class
     */
    private function __construct(string $class, bool $enum)
    {
        $this->class = $class;
        $this->enum = $enum;
    }

    /**
     * @psalm-pure
     * @template C of object
     *
     * @param class-string<C> $class
     *
     * @return self<C>
     */
    public static function of(string $class): self
    {
        return new self($class, false);
    }

    /**
     * @psalm-pure
     * @template C of object
     *
     * @param class-string<C> $class
     *
     * @return self<C>
     */
    public static function ofEnum(string $class): self
    {
        return new self($class, true);
    }

    public function enum(): bool
    {
        return $this->enum;
    }

    public function allows(mixed $value): bool
    {
        return $value instanceof $this->class;
    }

    public function accepts(Type $type): bool
    {
        if ($type instanceof Union || $type instanceof Intersection) {
            return $this->accepts($type->left()) && $this->accepts($type->right());
        }

        if (!($type instanceof self)) {
            return false;
        }

        return \is_a($type->toString(), $this->class, true);
    }

    public function toString(): string
    {
        return $this->class;
    }
}
