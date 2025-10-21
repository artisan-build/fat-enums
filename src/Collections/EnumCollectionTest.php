<?php

declare(strict_types=1);

use ArtisanBuild\FatEnums\Collections\EnumCollection;
use ArtisanBuild\FatEnums\Tests\Fixtures\CollectibleIntEnum;
use ArtisanBuild\FatEnums\Tests\Fixtures\CollectibleStringEnum;
use ArtisanBuild\FatEnums\Tests\Fixtures\CollectibleUnitEnum;
use ArtisanBuild\FatEnums\Tests\Fixtures\StringBackedEnum;
use Illuminate\Support\Collection;

describe('EnumCollection constructor with enum class string', function (): void {
    it('creates a collection from an enum class string', function (): void {
        $collection = new EnumCollection(CollectibleStringEnum::class);

        expect($collection)->toBeInstanceOf(EnumCollection::class);
        expect($collection->count())->toBe(3);
        expect($collection->all())->toBe(CollectibleStringEnum::cases());
    });

    it('creates a collection from a unit enum class string', function (): void {
        $collection = new EnumCollection(CollectibleUnitEnum::class);

        expect($collection)->toBeInstanceOf(EnumCollection::class);
        expect($collection->count())->toBe(3);
        expect($collection->all())->toBe(CollectibleUnitEnum::cases());
    });

    it('throws an exception when given an invalid enum class string', function (): void {
        expect(fn () => new EnumCollection('InvalidEnumClass'))
            ->toThrow(
                InvalidArgumentException::class,
                'Invalid enum class: InvalidEnumClass',
            );
    });

    it('throws an exception when given a non-enum class string', function (): void {
        expect(fn () => new EnumCollection(stdClass::class))
            ->toThrow(
                InvalidArgumentException::class,
                'Invalid enum class: stdClass',
            );
    });
});

describe('EnumCollection constructor with iterable', function (): void {
    it('creates a collection from an array of enum cases', function (): void {
        $cases = [
            CollectibleStringEnum::Active,
            CollectibleStringEnum::Pending,
        ];

        $collection = new EnumCollection($cases);

        expect($collection)->toBeInstanceOf(EnumCollection::class);
        expect($collection->count())->toBe(2);
        expect($collection->all())->toBe($cases);
    });

    it('creates a collection from an iterator of enum cases', function (): void {
        $iterator = new ArrayIterator([
            CollectibleIntEnum::One,
            CollectibleIntEnum::Two,
        ]);

        $collection = new EnumCollection($iterator);

        expect($collection)->toBeInstanceOf(EnumCollection::class);
        expect($collection->count())->toBe(2);
        expect($collection->first())->toBe(CollectibleIntEnum::One);
        expect($collection->last())->toBe(CollectibleIntEnum::Two);
    });

    it('creates a collection from a Laravel Collection of enum cases', function (): void {
        $laravelCollection = new Collection([
            CollectibleStringEnum::Active,
            CollectibleStringEnum::Pending,
        ]);

        $collection = new EnumCollection($laravelCollection);

        expect($collection)->toBeInstanceOf(EnumCollection::class);
        expect($collection->count())->toBe(2);
        expect($collection->first())->toBe(CollectibleStringEnum::Active);
        expect($collection->last())->toBe(CollectibleStringEnum::Pending);
    });

    it('creates a collection from an array of unit enum cases', function (): void {
        $cases = [
            CollectibleUnitEnum::Red,
            CollectibleUnitEnum::Blue,
        ];

        $collection = new EnumCollection($cases);

        expect($collection)->toBeInstanceOf(EnumCollection::class);
        expect($collection->count())->toBe(2);
        expect($collection->all())->toBe($cases);
        expect($collection->getEnumClass())->toBe(CollectibleUnitEnum::class);
    });

    it('throws an exception when given an array with non-enum items', function (): void {
        $items = [
            CollectibleStringEnum::Active,
            'not an enum',
        ];

        expect(fn () => new EnumCollection($items))
            ->toThrow(
                InvalidArgumentException::class,
                'All items must be an enum instance, string given',
            );
    });

    it('throws an exception when given an array with mixed enum types', function (): void {
        $items = [
            CollectibleStringEnum::Active,
            CollectibleIntEnum::One,
        ];

        expect(fn () => new EnumCollection($items))
            ->toThrow(
                InvalidArgumentException::class,
                'All items must be an instance of the same enum class: ArtisanBuild\FatEnums\Tests\Fixtures\CollectibleStringEnum',
            );
    });
});

describe('EnumCollection getEnumClass method', function (): void {
    it('returns the enum class name when constructed from enum class string', function (): void {
        $collection = new EnumCollection(CollectibleStringEnum::class);

        expect($collection->getEnumClass())->toBe(CollectibleStringEnum::class);
    });

    it('returns the enum class name when constructed from array of cases', function (): void {
        $collection = new EnumCollection([
            CollectibleIntEnum::One,
            CollectibleIntEnum::Two,
        ]);

        expect($collection->getEnumClass())->toBe(CollectibleIntEnum::class);
    });

    it('returns the enum class name for a single-case collection', function (): void {
        $collection = new EnumCollection([StringBackedEnum::Happy]);

        expect($collection->getEnumClass())->toBe(StringBackedEnum::class);
    });
});

describe('EnumCollection inherits Collection methods', function (): void {
    it('can filter enum cases', function (): void {
        $collection = new EnumCollection(CollectibleStringEnum::class);

        $filtered = $collection->filter(
            fn ($case) => $case === CollectibleStringEnum::Active
        );

        expect($filtered->count())->toBe(1);
        expect($filtered->first())->toBe(CollectibleStringEnum::Active);
    });

    it('can check if collection contains a case', function (): void {
        $collection = new EnumCollection(CollectibleStringEnum::class);

        expect($collection->contains(CollectibleStringEnum::Active))->toBeTrue();
        expect($collection->contains(CollectibleIntEnum::One))->toBeFalse();
    });
});
