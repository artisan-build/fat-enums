<?php

declare(strict_types=1);

use ArtisanBuild\FatEnums\Tests\Fixtures\FatTestEnum;

it('throws an exception when the number of random cases requested is not positive', function (): void {
    expect(fn () => FatTestEnum::random(0))->toThrow(
        InvalidArgumentException::class,
        'Number of random cases requested must be positive.',
    );
});

it('throws an exception when the number of random cases requested exceeds the number of cases available', function (): void {
    expect(fn () => FatTestEnum::random(count(FatTestEnum::cases()) + 1))->toThrow(
        InvalidArgumentException::class,
        'Number of random cases requested exceeds the number of cases available.',
    );
});

it('returns a random case', function (): void {
    $random = FatTestEnum::random();
    expect($random)->toBeInstanceOf(FatTestEnum::class);
    expect(FatTestEnum::cases())->toContain($random);
});

it('returns multiple random cases', function (): void {
    $random = FatTestEnum::random(3);
    expect($random)->toBeArray();
    foreach ($random as $case) {
        expect($case)->toBeInstanceOf(FatTestEnum::class);
        expect(FatTestEnum::cases())->toContain($case);
    }
});

it('returns a random case when the number of random cases requested is 1', function (): void {
    $random = FatTestEnum::random(1);
    expect($random)->toBeInstanceOf(FatTestEnum::class);
    expect(FatTestEnum::cases())->toContain($random);
});
