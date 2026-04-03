<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Collections;

use Illuminate\Support\Arr;
use Illuminate\Support\Enumerable;
use Override;

/**
 * Polyfills the `newInstance()` factory method for Laravel Collection versions that call `new static(...)` directly.
 * Laravel 13.3+ has native `newInstance()` support, making these overrides redundant. Remove this trait entirely
 * when dropping Laravel 12 support.
 */
trait CollectionNewInstancePolyfill
{
    /**
     * Create a new collection instance, forwarding extra arguments to the constructor.
     */
    #[Override]
    public static function make($items = [], ...$args): static
    {
        /** @phpstan-ignore new.static, return.type */
        return new static($items, ...$args);
    }

    /**
     * Wrap the given value in a collection, forwarding extra arguments to the constructor.
     */
    #[Override]
    public static function wrap($value, ...$args): static
    {
        /** @phpstan-ignore return.type */
        return $value instanceof Enumerable
            ? new static($value, ...$args) /** @phpstan-ignore new.static */
            : new static(Arr::wrap($value), ...$args); /** @phpstan-ignore new.static */
    }

    /**
     * Create a new empty collection, forwarding extra arguments to the constructor.
     */
    #[Override]
    public static function empty(...$args): static
    {
        /** @phpstan-ignore new.static */
        return new static([], ...$args);
    }

    /**
     * Create a collection with the given range, forwarding extra arguments to the constructor.
     */
    #[Override]
    public static function range($from, $to, $step = 1, ...$args): static
    {
        /** @phpstan-ignore new.static, return.type */
        return new static(range($from, $to, $step), ...$args);
    }

    /**
     * Create a new collection by invoking the callback a given number of times.
     */
    #[Override]
    public static function times($number, ?callable $callback = null, ...$args): static
    {
        if ($number < 1) {
            /** @phpstan-ignore new.static */
            return new static([], ...$args);
        }

        return static::range(1, $number, 1, ...$args)
            ->unless($callback == null)
            ->map($callback);
    }

    /**
     * Create a new collection instance. Override this in subclasses to pass additional constructor arguments.
     *
     * @param  array<array-key, mixed>  $items
     */
    public function newInstance($items = []): static
    {
        return new static($items);
    }

    /**
     * Run a filter over each of the items.
     */
    #[Override]
    public function filter(?callable $callback = null): static
    {
        return $this->newInstance($this->toBase()->filter($callback)->all());
    }

    /**
     * Get all items except for those with the specified keys.
     */
    #[Override]
    public function except($keys): static
    {
        return $this->newInstance($this->toBase()->except($keys)->all());
    }

    /**
     * Get the items with the specified keys.
     */
    #[Override]
    public function only($keys): static
    {
        return $this->newInstance($this->toBase()->only($keys)->all());
    }

    /**
     * Select specific values from the items.
     */
    #[Override]
    public function select($keys): static
    {
        return $this->newInstance($this->toBase()->select($keys)->all());
    }

    /**
     * Get the items in the collection that are not present in the given items.
     */
    #[Override]
    public function diff($items): static
    {
        return $this->newInstance($this->toBase()->diff($items)->all());
    }

    /**
     * Get the items in the collection that are not present in the given items, using the callback.
     */
    #[Override]
    public function diffUsing($items, callable $callback): static
    {
        return $this->newInstance($this->toBase()->diffUsing($items, $callback)->all());
    }

    /**
     * Get the items in the collection whose keys and values are not present in the given items.
     */
    #[Override]
    public function diffAssoc($items): static
    {
        return $this->newInstance($this->toBase()->diffAssoc($items)->all());
    }

    /**
     * Get the items in the collection whose keys and values are not present in the given items, using the callback.
     */
    #[Override]
    public function diffAssocUsing($items, callable $callback): static
    {
        return $this->newInstance($this->toBase()->diffAssocUsing($items, $callback)->all());
    }

    /**
     * Get the items in the collection whose keys are not present in the given items.
     */
    #[Override]
    public function diffKeys($items): static
    {
        return $this->newInstance($this->toBase()->diffKeys($items)->all());
    }

    /**
     * Get the items in the collection whose keys are not present in the given items, using the callback.
     */
    #[Override]
    public function diffKeysUsing($items, callable $callback): static
    {
        return $this->newInstance($this->toBase()->diffKeysUsing($items, $callback)->all());
    }

    /**
     * Intersect the collection with the given items.
     */
    #[Override]
    public function intersect($items): static
    {
        return $this->newInstance($this->toBase()->intersect($items)->all());
    }

    /**
     * Intersect the collection with the given items, using the callback.
     */
    #[Override]
    public function intersectUsing($items, callable $callback): static
    {
        return $this->newInstance($this->toBase()->intersectUsing($items, $callback)->all());
    }

    /**
     * Intersect the collection with the given items by key and value.
     */
    #[Override]
    public function intersectAssoc($items): static
    {
        return $this->newInstance($this->toBase()->intersectAssoc($items)->all());
    }

    /**
     * Intersect the collection with the given items by key and value, using the callback.
     */
    #[Override]
    public function intersectAssocUsing($items, callable $callback): static
    {
        return $this->newInstance($this->toBase()->intersectAssocUsing($items, $callback)->all());
    }

    /**
     * Intersect the collection with the given items by key.
     */
    #[Override]
    public function intersectByKeys($items): static
    {
        return $this->newInstance($this->toBase()->intersectByKeys($items)->all());
    }

    /**
     * Reverse items order.
     */
    #[Override]
    public function reverse(): static
    {
        return $this->newInstance($this->toBase()->reverse()->all());
    }

    /**
     * Shuffle the items in the collection.
     */
    #[Override]
    public function shuffle(): static
    {
        return $this->newInstance($this->toBase()->shuffle()->all());
    }

    /**
     * Slice the underlying collection array.
     */
    #[Override]
    public function slice($offset, $length = null): static
    {
        return $this->newInstance($this->toBase()->slice($offset, $length)->all());
    }

    /**
     * Sort through each item with a callback.
     */
    #[Override]
    public function sort($callback = null): static
    {
        return $this->newInstance($this->toBase()->sort($callback)->all());
    }

    /**
     * Sort items in descending order.
     */
    #[Override]
    public function sortDesc($options = SORT_REGULAR): static
    {
        return $this->newInstance($this->toBase()->sortDesc($options)->all());
    }

    /**
     * Sort the collection keys.
     */
    #[Override]
    public function sortKeys($options = SORT_REGULAR, $descending = false): static
    {
        return $this->newInstance($this->toBase()->sortKeys($options, $descending)->all());
    }

    /**
     * Sort the collection keys using a callback.
     */
    #[Override]
    public function sortKeysUsing(callable $callback): static
    {
        return $this->newInstance($this->toBase()->sortKeysUsing($callback)->all());
    }

    /**
     * Skip items until the given value is found.
     */
    #[Override]
    public function skipUntil($value): static
    {
        return $this->newInstance($this->toBase()->skipUntil($value)->all());
    }

    /**
     * Skip items while the given value is true.
     */
    #[Override]
    public function skipWhile($value): static
    {
        return $this->newInstance($this->toBase()->skipWhile($value)->all());
    }

    /**
     * Take items until the given value is found.
     */
    #[Override]
    public function takeUntil($value): static
    {
        return $this->newInstance($this->toBase()->takeUntil($value)->all());
    }

    /**
     * Take items while the given value is true.
     */
    #[Override]
    public function takeWhile($value): static
    {
        return $this->newInstance($this->toBase()->takeWhile($value)->all());
    }

    /**
     * Splice a portion of the underlying collection array.
     */
    #[Override]
    public function splice($offset, $length = null, $replacement = []): static
    {
        return $this->newInstance($this->toBase()->splice($offset, $length, $replacement)->all());
    }

    /**
     * Return only unique items from the collection.
     */
    #[Override]
    public function unique($key = null, $strict = false): static
    {
        return $this->newInstance($this->toBase()->unique($key, $strict)->all());
    }

    /**
     * Reset the keys on the underlying array.
     */
    #[Override]
    public function values(): static
    {
        /** @phpstan-ignore return.type */
        return $this->newInstance($this->toBase()->values()->all());
    }

    /**
     * Get one or a specified number of items randomly from the collection.
     */
    #[Override]
    public function random($number = null, $preserveKeys = false): static
    {
        return $this->newInstance($this->toBase()->random($number, $preserveKeys)->all());
    }

    /**
     * Merge the collection with the given items.
     */
    #[Override]
    public function merge($items): static
    {
        return $this->newInstance($this->toBase()->merge($items)->all());
    }

    /**
     * Recursively merge the collection with the given items.
     */
    #[Override]
    public function mergeRecursive($items): static
    {
        /** @phpstan-ignore return.type */
        return $this->newInstance($this->toBase()->mergeRecursive($items)->all());
    }

    /**
     * Replace the collection items with the given items.
     */
    #[Override]
    public function replace($items): static
    {
        return $this->newInstance($this->toBase()->replace($items)->all());
    }

    /**
     * Recursively replace the collection items with the given items.
     */
    #[Override]
    public function replaceRecursive($items): static
    {
        return $this->newInstance($this->toBase()->replaceRecursive($items)->all());
    }

    /**
     * Union the collection with the given items.
     */
    #[Override]
    public function union($items): static
    {
        return $this->newInstance($this->toBase()->union($items)->all());
    }

    /**
     * Pad collection to the specified length with a value.
     */
    #[Override]
    public function pad($size, $value): static
    {
        /** @phpstan-ignore return.type */
        return $this->newInstance($this->toBase()->pad($size, $value)->all());
    }
}
