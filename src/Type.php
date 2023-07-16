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
    public function allows(mixed $value): bool;
    public function accepts(self $type): bool;

    /**
     * @return non-empty-string
     */
    public function toString(): string;
}
