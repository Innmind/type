<?php
declare(strict_types = 1);

namespace Fixtures\Innmind\Type;

final class Example
{
    private $unknown;
    private string $string;
    private int $int;
    private float $float;
    private bool $bool;
    private array $array;
    private object $object;
    private mixed $mixed;
    private ?int $nullable;
    private \stdClass $class;
    private \stdClass|self $union;
    private \ArrayAccess&\Iterator $intersection;
    private (\ArrayAccess&\Iterator)|self $intersectionAndUnion;
    private null|(\ArrayAccess&\Iterator)|self $nullableIntersectionAndUnion;
}
