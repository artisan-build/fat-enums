<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Collections;

trait CollectibleEnumMethods
{
    /**
     * Collect all cases of this enum into a type-safe EnumCollection.
     *
     * @return EnumCollection<static>
     */
    public static function collect(): EnumCollection
    {
        // We're going to use the enum class name to create the
        // collection instead of the enum cases, because the
        // EnumCollection's constructor does that for us.

        return new EnumCollection(static::class);
    }
}
