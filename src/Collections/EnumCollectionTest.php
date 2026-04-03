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

    it('can check if collection contains a case', function (): void {
        $collection = new EnumCollection(CollectibleStringEnum::class);

        expect($collection->contains(CollectibleStringEnum::Active))->toBeTrue();
        expect($collection->contains(CollectibleIntEnum::One))->toBeFalse();
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
                'Expected instance of ArtisanBuild\FatEnums\Tests\Fixtures\CollectibleStringEnum, got ArtisanBuild\FatEnums\Tests\Fixtures\CollectibleIntEnum',
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

describe('EnumCollection constructor with backed_by parameter', function (): void {
    it('creates an empty collection when backed_by is provided', function (): void {
        $collection = new EnumCollection([], backed_by: CollectibleStringEnum::class);

        expect($collection)->toBeEmpty()
            ->and($collection->getEnumClass())->toBe(CollectibleStringEnum::class);
    });

    it('creates a collection with items when backed_by is provided', function (): void {
        $collection = new EnumCollection(
            [CollectibleStringEnum::Active, CollectibleStringEnum::Pending],
            backed_by: CollectibleStringEnum::class,
        );

        expect($collection->count())->toBe(2)
            ->and($collection->getEnumClass())->toBe(CollectibleStringEnum::class);
    });

    it('throws when backed_by does not match item types', function (): void {
        expect(fn () => new EnumCollection(
            [CollectibleStringEnum::Active],
            backed_by: CollectibleIntEnum::class,
        ))->toThrow(InvalidArgumentException::class);
    });

    it('throws when backed_by is not a valid enum class', function (): void {
        expect(fn () => new EnumCollection([], backed_by: stdClass::class))
            ->toThrow(InvalidArgumentException::class);
    });

    it('throws when constructing with empty array and no backed_by', function (): void {
        expect(fn () => new EnumCollection([]))
            ->toThrow(InvalidArgumentException::class);
    });

    it('accepts matching enum class string and backed_by', function (): void {
        $collection = new EnumCollection(CollectibleStringEnum::class, backed_by: CollectibleStringEnum::class);

        expect($collection->count())->toBe(3)
            ->and($collection->getEnumClass())->toBe(CollectibleStringEnum::class);
    });

    it('throws when enum class string and backed_by do not match', function (): void {
        expect(fn () => new EnumCollection(CollectibleStringEnum::class, backed_by: CollectibleIntEnum::class))
            ->toThrow(InvalidArgumentException::class);
    });
});

describe('EnumCollection mutation validation', function (): void {
    it('push accepts valid enum cases', function (): void {
        $collection = new EnumCollection([CollectibleStringEnum::Active]);

        $collection->push(CollectibleStringEnum::Pending);

        expect($collection->count())->toBe(2);
    });

    it('push rejects wrong enum type', function (): void {
        $collection = new EnumCollection([CollectibleStringEnum::Active]);

        expect(fn () => $collection->push(CollectibleIntEnum::One))
            ->toThrow(InvalidArgumentException::class);
    });

    it('push rejects non-enum values', function (): void {
        $collection = new EnumCollection([CollectibleStringEnum::Active]);

        expect(fn () => $collection->push('not an enum'))
            ->toThrow(InvalidArgumentException::class);
    });

    it('add accepts valid enum cases', function (): void {
        $collection = new EnumCollection([CollectibleStringEnum::Active]);

        $collection->add(CollectibleStringEnum::Pending);

        expect($collection->count())->toBe(2);
    });

    it('add rejects wrong enum type', function (): void {
        $collection = new EnumCollection([CollectibleStringEnum::Active]);

        expect(fn () => $collection->add(CollectibleIntEnum::One))
            ->toThrow(InvalidArgumentException::class);
    });

    it('prepend accepts valid enum cases', function (): void {
        $collection = new EnumCollection([CollectibleStringEnum::Active]);

        $collection->prepend(CollectibleStringEnum::Pending);

        expect($collection->first())->toBe(CollectibleStringEnum::Pending);
    });

    it('prepend rejects wrong enum type', function (): void {
        $collection = new EnumCollection([CollectibleStringEnum::Active]);

        expect(fn () => $collection->prepend(CollectibleIntEnum::One))
            ->toThrow(InvalidArgumentException::class);
    });

    it('put accepts valid enum cases', function (): void {
        $collection = new EnumCollection([CollectibleStringEnum::Active]);

        $collection->put(1, CollectibleStringEnum::Pending);

        expect($collection->count())->toBe(2);
    });

    it('put rejects wrong enum type', function (): void {
        $collection = new EnumCollection([CollectibleStringEnum::Active]);

        expect(fn () => $collection->put(1, CollectibleIntEnum::One))
            ->toThrow(InvalidArgumentException::class);
    });

    it('merge accepts valid enum cases', function (): void {
        $collection = new EnumCollection([CollectibleStringEnum::Active]);

        $merged = $collection->merge([CollectibleStringEnum::Pending]);

        expect($merged->count())->toBe(2);
    });

    it('merge rejects wrong enum type', function (): void {
        $collection = new EnumCollection([CollectibleStringEnum::Active]);

        expect(fn () => $collection->merge([CollectibleIntEnum::One]))
            ->toThrow(InvalidArgumentException::class);
    });

    it('pad accepts valid enum case', function (): void {
        $collection = new EnumCollection([CollectibleStringEnum::Active]);

        $padded = $collection->pad(3, CollectibleStringEnum::Pending);

        expect($padded->count())->toBe(3);
    });

    it('pad rejects wrong enum type', function (): void {
        $collection = new EnumCollection([CollectibleStringEnum::Active]);

        expect(fn () => $collection->pad(3, CollectibleIntEnum::One))
            ->toThrow(InvalidArgumentException::class);
    });

    it('offsetSet accepts valid enum case', function (): void {
        $collection = new EnumCollection([CollectibleStringEnum::Active]);

        $collection[1] = CollectibleStringEnum::Pending;

        expect($collection->count())->toBe(2);
    });

    it('offsetSet rejects wrong enum type', function (): void {
        $collection = new EnumCollection([CollectibleStringEnum::Active]);

        expect(fn () => $collection[1] = CollectibleIntEnum::One)
            ->toThrow(InvalidArgumentException::class);
    });

    it('splice with replacement accepts valid enum cases', function (): void {
        $collection = new EnumCollection(CollectibleStringEnum::class);

        $collection->splice(0, 1, [CollectibleStringEnum::Active]);

        expect($collection->count())->toBe(3);
    });

    it('splice with replacement rejects wrong enum type', function (): void {
        $collection = new EnumCollection(CollectibleStringEnum::class);

        expect(fn () => $collection->splice(0, 1, [CollectibleIntEnum::One]))
            ->toThrow(InvalidArgumentException::class);
    });

    it('splice without replacement works normally', function (): void {
        $collection = new EnumCollection(CollectibleStringEnum::class);

        $spliced = $collection->splice(0, 1);

        expect($spliced->count())->toBe(1)
            ->and($spliced->getEnumClass())->toBe(CollectibleStringEnum::class);
    });
});

describe('EnumCollection static factory methods', function (): void {
    it('make forwards backed_by to constructor', function (): void {
        $collection = EnumCollection::make(
            [CollectibleStringEnum::Active],
            backed_by: CollectibleStringEnum::class,
        );

        expect($collection->count())->toBe(1)
            ->and($collection->getEnumClass())->toBe(CollectibleStringEnum::class);
    });

    it('make creates empty collection with backed_by', function (): void {
        $collection = EnumCollection::make([], backed_by: CollectibleStringEnum::class);

        expect($collection)->toBeEmpty()
            ->and($collection->getEnumClass())->toBe(CollectibleStringEnum::class);
    });

    it('empty creates empty collection with backed_by', function (): void {
        $collection = EnumCollection::empty(backed_by: CollectibleStringEnum::class);

        expect($collection)->toBeEmpty()
            ->and($collection->getEnumClass())->toBe(CollectibleStringEnum::class);
    });

    it('wrap forwards backed_by to constructor', function (): void {
        $collection = EnumCollection::wrap(
            [CollectibleStringEnum::Active, CollectibleStringEnum::Pending],
            backed_by: CollectibleStringEnum::class,
        );

        expect($collection->count())->toBe(2)
            ->and($collection->getEnumClass())->toBe(CollectibleStringEnum::class);
    });
});
