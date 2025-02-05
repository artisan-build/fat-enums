<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Traits;

trait FatEnums
{
    /**
     * @param  positive-int  $num
     * @return static|array<static>
     */
    public static function random(int $num = 1): static|array
    {
        throw_if(
            condition: $num < 1,
            exception: new \InvalidArgumentException('Number of random cases requested must be positive.'),
        );

        throw_if(
            condition: $num > count(static::cases()),
            exception: new \InvalidArgumentException('Number of random cases requested exceeds the number of cases available.'),
        );

        return $num === 1
            ? collect(static::cases())->shuffle()->first()
            : collect(static::cases())->shuffle()->take($num)->toArray();
    }
}
