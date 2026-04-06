<?php
declare(strict_types = 1);

namespace Innmind\Type;

/**
 * @template T
 * @psalm-immutable
 */
interface Type
{
    /**
     * @psalm-assert-if-true T $value
     */
    #[\NoDiscard]
    public function allows(mixed $value): bool;
    #[\NoDiscard]
    public function accepts(self $type): bool;

    /**
     * @return non-empty-string
     */
    #[\NoDiscard]
    public function toString(): string;
}
