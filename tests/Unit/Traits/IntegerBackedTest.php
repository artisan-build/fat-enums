<?php

declare(strict_types=1);

use ArtisanBuild\FatEnums\Tests\Fixtures\IntegerBackedEnum;
use Illuminate\Support\Collection;

describe('Integer backed enum trait methods', function (): void {
    it('finds correct values for eq', function (): void {
        expect(IntegerBackedEnum::Eight->eq())
            ->toBeInstanceOf(Collection::class)
            ->toContain(IntegerBackedEnum::Eight)
            ->toHaveLength(1);
    });

    it('finds the correct values for gt', function (): void {
        expect(IntegerBackedEnum::Eight->gt())
            ->toBeInstanceOf(Collection::class)
            ->toContain(IntegerBackedEnum::Nine)
            ->toContain(IntegerBackedEnum::Ten)
            ->not->ToContain(IntegerBackedEnum::Seven);
    });
});
