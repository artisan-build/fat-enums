<?php

declare(strict_types=1);

use ArtisanBuild\FatEnums\Collections\CollectionNewInstancePolyfill;
use Illuminate\Support\Collection;

/**
 * A plain Collection subclass that uses the polyfill with no constructor changes.
 */
class PlainPolyfillCollection extends Collection
{
    use CollectionNewInstancePolyfill;
}

/**
 * A Collection subclass with extra constructor state, proving the polyfill's newInstance() override works.
 */
class TaggedCollection extends Collection
{
    use CollectionNewInstancePolyfill;

    public function __construct($items = [], public readonly string $tag = 'default')
    {
        parent::__construct($items);
    }

    public function newInstance($items = []): static
    {
        return new static($items, tag: $this->tag);
    }
}

describe('CollectionNewInstancePolyfill with plain subclass', function (): void {
    it('filter returns the subclass type', function (): void {
        $collection = new PlainPolyfillCollection([1, 2, 3, 4, 5]);

        $filtered = $collection->filter(fn ($v) => $v > 3);

        expect($filtered)->toBeInstanceOf(PlainPolyfillCollection::class)
            ->and($filtered->all())->toBe([3 => 4, 4 => 5]);
    });

    it('filter to empty returns the subclass type', function (): void {
        $collection = new PlainPolyfillCollection([1, 2, 3]);

        $filtered = $collection->filter(fn ($v) => $v > 10);

        expect($filtered)->toBeInstanceOf(PlainPolyfillCollection::class)
            ->and($filtered)->toBeEmpty();
    });

    it('values returns the subclass type', function (): void {
        $collection = new PlainPolyfillCollection([1, 2, 3]);

        $values = $collection->values();

        expect($values)->toBeInstanceOf(PlainPolyfillCollection::class);
    });

    it('make creates the subclass type', function (): void {
        $collection = PlainPolyfillCollection::make([1, 2, 3]);

        expect($collection)->toBeInstanceOf(PlainPolyfillCollection::class)
            ->and($collection->count())->toBe(3);
    });
});

describe('CollectionNewInstancePolyfill with extended constructor', function (): void {
    it('filter preserves extra constructor state', function (): void {
        $collection = new TaggedCollection([1, 2, 3, 4, 5], tag: 'important');

        $filtered = $collection->filter(fn ($v) => $v > 3);

        expect($filtered)->toBeInstanceOf(TaggedCollection::class)
            ->and($filtered->tag)->toBe('important')
            ->and($filtered->count())->toBe(2);
    });

    it('filter to empty preserves extra constructor state', function (): void {
        $collection = new TaggedCollection([1, 2, 3], tag: 'important');

        $filtered = $collection->filter(fn ($v) => $v > 10);

        expect($filtered)->toBeInstanceOf(TaggedCollection::class)
            ->and($filtered->tag)->toBe('important')
            ->and($filtered)->toBeEmpty();
    });

    it('values preserves extra constructor state', function (): void {
        $collection = new TaggedCollection([1, 2, 3], tag: 'special');

        $values = $collection->values();

        expect($values)->toBeInstanceOf(TaggedCollection::class)
            ->and($values->tag)->toBe('special');
    });

    it('sort preserves extra constructor state', function (): void {
        $collection = new TaggedCollection([3, 1, 2], tag: 'sorted');

        $sorted = $collection->sort();

        expect($sorted)->toBeInstanceOf(TaggedCollection::class)
            ->and($sorted->tag)->toBe('sorted')
            ->and($sorted->values()->all())->toBe([1, 2, 3]);
    });

    it('make forwards extra constructor arguments', function (): void {
        $collection = TaggedCollection::make([1, 2], tag: 'made');

        expect($collection)->toBeInstanceOf(TaggedCollection::class)
            ->and($collection->tag)->toBe('made');
    });

    it('empty forwards extra constructor arguments', function (): void {
        $collection = TaggedCollection::empty(tag: 'empty-tag');

        expect($collection)->toBeInstanceOf(TaggedCollection::class)
            ->and($collection->tag)->toBe('empty-tag')
            ->and($collection)->toBeEmpty();
    });
});
