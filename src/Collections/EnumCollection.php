<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Collections;

use InvalidArgumentException;
use BackedEnum;
use UnitEnum;

class EnumCollection extends \Illuminate\Support\Collection
{
    private readonly string $enumClass;

    /**
     * @param enum-class<T>|iterable<T> $items
     */
    public function __construct(
        string|iterable $items = [],
    ) {
        if (is_string($items)) {
            if (!enum_exists($items)) {
                throw new InvalidArgumentException('Invalid enum class: ' . $items);
            }

            $this->enumClass = $items;
            $items = $items::cases();
            goto construct;
        }

        // check that every item is an enum
        foreach ($items as $item) {
            if (!$item instanceof BackedEnum || !$item instanceof UnitEnum) {
                throw new InvalidArgumentException('All items must be an enum instance, ' . get_debug_type($item) . ' given');
            }
        }

        // chec that every item is an instance of the **same** enum class
        $enumClass = $items[0]::class;
        foreach ($items as $item) {
            if ($item::class !== $enumClass) {
                throw new InvalidArgumentException('All items must be an instance of the same enum class: ' . $enumClass);
            }
        }

        $this->enumClass = $enumClass;

        construct:
        parent::__construct($items);
    }

    public function getEnumClass(): string
    {
        return $this->enumClass;
    }
}
