<?php
declare(strict_types = 1);

use Innmind\Type\{
    Intersection,
    ClassName,
};

return static function() {
    yield test(
        'Intersection::allows()',
        static function($assert) {
            $intersection = Intersection::of(
                ClassName::of(Countable::class),
                ClassName::of(Iterator::class),
            );

            $assert->true($intersection->allows(new SplObjectStorage));
            $assert->false($intersection->allows(new ArrayObject));
            $assert->false($intersection->allows(new class implements Countable {
                public function count(): int
                {
                    return 0;
                }
            }));
        },
    );
    yield test(
        'Intersection::accepts()',
        static function($assert) {
            $intersection = Intersection::of(
                ClassName::of(Countable::class),
                ClassName::of(Iterator::class),
            );

            $assert->true($intersection->accepts($intersection));
            $assert->true($intersection->accepts(Intersection::of(
                ClassName::of(Iterator::class),
                ClassName::of(Countable::class),
            )));
            $assert->true($intersection->accepts(ClassName::of(SplObjectStorage::class)));
            $assert->false($intersection->accepts(ClassName::of(Countable::class)));
            $assert->false($intersection->accepts(ClassName::of(Iterator::class)));
        },
    );
};
