<?php

declare(strict_types=1);

use ArtisanBuild\FatEnums\Exceptions\MissingDataAttributeException;
use ArtisanBuild\FatEnums\Exceptions\MissingDataKeyException;
use ArtisanBuild\FatEnums\Tests\Fixtures\KeyValueTestEnum;

describe('Case With Data', function (): void {
    it('can get a value by key', function (): void {
        expect(KeyValueTestEnum::CASE_WITH_DATA->data('key'))->toBe('value');
    });

    it('throws an exception when getting a value by a non-existant key', function (): void {
        expect(fn () => KeyValueTestEnum::CASE_WITH_DATA->data('foo'))->toThrow(MissingDataKeyException::class);
    });

    it('can use a fallback value when getting a value by a non-existant key', function (): void {
        expect(KeyValueTestEnum::CASE_WITH_DATA->data('foo', 'bar'))->toBe('bar');
        expect(KeyValueTestEnum::CASE_WITH_DATA->data('foo', null))->toBeNull();
    });

    it('can get all values on an enum case', function (): void {
        expect(KeyValueTestEnum::CASE_WITH_DATA->data())->toBe(['key' => 'value']);
    });
});

describe('Case Without Data', function (): void {
    it('throws an exception when getting a value by key', function (): void {
        expect(fn () => KeyValueTestEnum::CASE_WITHOUT_DATA->data('key'))->toThrow(MissingDataAttributeException::class);
    });

    it('can use a fallback value when getting a value by key', function (): void {
        expect(KeyValueTestEnum::CASE_WITHOUT_DATA->data('key', null))->toBeNull();
    });

    it('throws an exception when getting all values', function (): void {
        expect(fn () => KeyValueTestEnum::CASE_WITHOUT_DATA->data())->toThrow(MissingDataAttributeException::class);
    });

    it('can use a fallback value when getting all values', function (): void {
        expect(KeyValueTestEnum::CASE_WITHOUT_DATA->data(default: ['key' => 'value']))->toBe(['key' => 'value']);
    });
});
