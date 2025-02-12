<?php

declare(strict_types=1);

use ArtisanBuild\FatEnums\Tests\Fixtures\UnbackedEnum;

it('can get a case by name', function (): void {
    expect(UnbackedEnum::tryFrom('Yes'))->toBe(UnbackedEnum::Yes);
    expect(UnbackedEnum::from('No'))->toBe(UnbackedEnum::No);
});

it('returns null when getting a case by name that does not exist', function (): void {
    expect(UnbackedEnum::tryFrom('Missing'))->toBeNull();
});

it('throws an exception when getting a case by name that does not exist', function (): void {
    expect(fn () => UnbackedEnum::from('Missing'))->toThrow(\Exception::class);
});
