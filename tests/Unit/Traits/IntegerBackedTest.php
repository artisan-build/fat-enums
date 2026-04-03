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

    it('finds the correct values for neq', function (): void {
        expect(IntegerBackedEnum::Eight->neq())
            ->toBeInstanceOf(Collection::class)
            ->not->toContain(IntegerBackedEnum::Eight)
            ->toHaveLength(count(IntegerBackedEnum::cases()) - 1);
    });

    it('finds the correct values for gte', function (): void {
        expect(IntegerBackedEnum::Eight->gte())
            ->toBeInstanceOf(Collection::class)
            ->toContain(IntegerBackedEnum::Eight)
            ->toContain(IntegerBackedEnum::Nine)
            ->toContain(IntegerBackedEnum::Ten)
            ->not->toContain(IntegerBackedEnum::Seven);
    });

    it('finds the correct values for lt', function (): void {
        expect(IntegerBackedEnum::Eight->lt())
            ->toBeInstanceOf(Collection::class)
            ->toContain(IntegerBackedEnum::Seven)
            ->not->toContain(IntegerBackedEnum::Eight)
            ->not->toContain(IntegerBackedEnum::Nine);
    });

    it('finds the correct values for lte', function (): void {
        expect(IntegerBackedEnum::Eight->lte())
            ->toBeInstanceOf(Collection::class)
            ->toContain(IntegerBackedEnum::Seven)
            ->toContain(IntegerBackedEnum::Eight)
            ->not->toContain(IntegerBackedEnum::Nine);
    });

    it('returns the ordinal string', function (): void {
        expect(IntegerBackedEnum::One->ordinal())->toBe('1st')
            ->and(IntegerBackedEnum::Two->ordinal())->toBe('2nd')
            ->and(IntegerBackedEnum::Three->ordinal())->toBe('3rd');
    });
});
