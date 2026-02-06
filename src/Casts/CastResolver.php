<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Casts;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

/**
 * Resolves a cast string like 'AsEnumCollectionBitmask:PermissionEnum' into a CastsAttributes instance.
 */
class CastResolver
{
    /**
     * Parse a cast string and return the resolved CastsAttributes instance.
     */
    public static function resolve(string $cast_string): CastsAttributes
    {
        $segments = explode(':', $cast_string, 2);
        $cast_class = $segments[0];
        $arguments = isset($segments[1]) ? explode(',', $segments[1]) : [];

        if (! class_exists($cast_class)) {
            throw new InvalidArgumentException("Cast class {$cast_class} does not exist");
        }

        if (! is_subclass_of($cast_class, Castable::class)) {
            throw new InvalidArgumentException("Cast class {$cast_class} must implement Castable");
        }

        return $cast_class::castUsing($arguments);
    }
}
