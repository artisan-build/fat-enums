<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Collections;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use Override;
use UnitEnum;

/**
 * A type-safe collection that holds cases from a single enum class.
 *
 * @template T of UnitEnum
 *
 * @extends Collection<array-key, T>
 */
class EnumCollection extends Collection
{
    use CollectionNewInstancePolyfill;

    private readonly string $enumClass;

    /**
     * Create a new EnumCollection from an enum class string, an iterable of cases, or an empty array with a backed_by class.
     *
     * @param  enum-string<T>|iterable<T>  $items
     * @param  enum-string<T>|null  $backed_by
     */
    public function __construct(
        string|iterable $items = [],
        ?string $backed_by = null,
    ) {
        if (is_string($items)) {
            if (! enum_exists($items)) {
                throw new InvalidArgumentException("Invalid enum class: {$items}");
            }

            if ($backed_by !== null && $backed_by !== $items) {
                throw new InvalidArgumentException(sprintf(
                    'Enum class %s does not match backed_by class %s',
                    $items,
                    $backed_by,
                ));
            }

            $this->enumClass = $items;
            parent::__construct($items::cases());

            return;
        }

        if (! is_array($items)) {
            $items = iterator_to_array($items);
        }

        if ($backed_by !== null) {
            if (! enum_exists($backed_by)) {
                throw new InvalidArgumentException("Invalid enum class: {$backed_by}");
            }

            $this->enumClass = $backed_by;
        } elseif ($items === []) {
            throw new InvalidArgumentException(
                'Cannot create an EnumCollection from an empty array without specifying the backed_by parameter'
            );
        } else {
            $this->enumClass = $items[0]::class;
        }

        $this->validateItems($items);
        parent::__construct($items);
    }

    /**
     * Get the fully qualified enum class name for this collection.
     */
    public function getEnumClass(): string
    {
        return $this->enumClass;
    }

    /**
     * Create a new instance of this collection type, preserving the enum class constraint.
     *
     * TODO: Add #[Override] attribute when dropping Laravel 12 support.
     * TODO: When specifying Laravel 13 support, require ^13.3 minimum (newInstance() was added in 13.3).
     *
     * @param  array<array-key, mixed>  $items
     */
    public function newInstance($items = []): static
    {
        /** @phpstan-ignore new.static */
        return new static($items, backed_by: $this->enumClass);
    }

    /**
     * Push one or more items onto the end of the collection.
     */
    #[Override]
    public function push(...$values): static
    {
        $this->validateItems($values);

        return parent::push(...$values);
    }

    /**
     * Push an item onto the beginning of the collection.
     */
    #[Override]
    public function prepend($value, $key = null): static
    {
        $this->validateItem($value);

        return parent::prepend($value, $key);
    }

    /**
     * Put an item in the collection by key.
     */
    #[Override]
    public function put($key, $value): static
    {
        $this->validateItem($value);

        return parent::put($key, $value);
    }

    /**
     * Add an item to the collection.
     */
    #[Override]
    public function add($item): static
    {
        $this->validateItem($item);

        return parent::add($item);
    }

    /**
     * Set the item at a given offset.
     */
    #[Override]
    public function offsetSet($key, $value): void
    {
        $this->validateItem($value);

        parent::offsetSet($key, $value);
    }

    /**
     * Splice a portion of the underlying collection array, validating any replacement items.
     */
    #[Override]
    public function splice($offset, $length = null, $replacement = []): static
    {
        $this->validateItems($replacement);

        return parent::splice($offset, $length, $replacement);
    }

    /**
     * Merge the collection with the given items, validating all incoming items.
     */
    #[Override]
    public function merge($items): static
    {
        $this->validateItems($items);

        return parent::merge($items);
    }

    /**
     * Recursively merge the collection with the given items, validating all incoming items.
     */
    #[Override]
    public function mergeRecursive($items): static
    {
        $this->validateItems($items);

        return parent::mergeRecursive($items);
    }

    /**
     * Replace the collection items with the given items, validating all incoming items.
     */
    #[Override]
    public function replace($items): static
    {
        $this->validateItems($items);

        return parent::replace($items);
    }

    /**
     * Recursively replace the collection items with the given items, validating all incoming items.
     */
    #[Override]
    public function replaceRecursive($items): static
    {
        $this->validateItems($items);

        return parent::replaceRecursive($items);
    }

    /**
     * Union the collection with the given items, validating all incoming items.
     */
    #[Override]
    public function union($items): static
    {
        $this->validateItems($items);

        return parent::union($items);
    }

    /**
     * Pad collection to the specified length with a value, validating the pad value.
     */
    #[Override]
    public function pad($size, $value): static
    {
        $this->validateItem($value);

        return parent::pad($size, $value);
    }

    /**
     * Validate that a value is an instance of this collection's enum class.
     */
    protected function validateItem(mixed $item): void
    {
        if (! $item instanceof UnitEnum) {
            throw new InvalidArgumentException(sprintf(
                'All items must be an enum instance, %s given',
                get_debug_type($item),
            ));
        }

        if ($item::class !== $this->enumClass) {
            throw new InvalidArgumentException(sprintf(
                'Expected instance of %s, got %s',
                $this->enumClass,
                $item::class,
            ));
        }
    }

    /**
     * Validate that all values in an iterable are instances of this collection's enum class.
     *
     * @param  iterable<mixed>  $items
     */
    protected function validateItems(iterable $items): void
    {
        foreach ($items as $item) {
            $this->validateItem($item);
        }
    }
}
