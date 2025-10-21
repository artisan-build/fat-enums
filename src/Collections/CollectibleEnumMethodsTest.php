<?php

declare(strict_types=1);

use ArtisanBuild\FatEnums\Collections\EnumCollection;
use ArtisanBuild\FatEnums\Tests\Fixtures\CollectibleIntEnum;
use ArtisanBuild\FatEnums\Tests\Fixtures\CollectibleStringEnum;
use ArtisanBuild\FatEnums\Tests\Fixtures\CollectibleUnitEnum;

describe('CollectibleEnumMethods trait', function (): void {
    it('returns an EnumCollection containing all cases', function (): void {
        $collection = CollectibleStringEnum::collect();

        expect($collection)->toBeInstanceOf(EnumCollection::class);
        expect($collection->count())->toBe(3);
        expect($collection->all())->toBe(CollectibleStringEnum::cases());
        expect($collection->getEnumClass())->toBe(CollectibleStringEnum::class);
    });

    it('works with different enum backing types', function (): void {
        $stringCollection = CollectibleStringEnum::collect();
        $intCollection = CollectibleIntEnum::collect();
        $unitCollection = CollectibleUnitEnum::collect();

        expect($stringCollection->getEnumClass())->toBe(CollectibleStringEnum::class);
        expect($intCollection->getEnumClass())->toBe(CollectibleIntEnum::class);
        expect($unitCollection->getEnumClass())->toBe(CollectibleUnitEnum::class);
    });

    it('creates a new collection instance on each call', function (): void {
        $collection1 = CollectibleStringEnum::collect();
        $collection2 = CollectibleStringEnum::collect();

        expect($collection1)->not->toBe($collection2);
        expect($collection1)->toEqual($collection2);
    });
});
