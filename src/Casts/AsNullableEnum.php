<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Casts;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * A generic wrapper that makes any Castable cast nullable.
 */
class AsNullableEnum implements Castable
{
    /**
     * Get the caster class to use when casting from / to this cast target.
     *
     * @param  array<int, string>  $arguments
     */
    public static function castUsing(array $arguments): CastsAttributes
    {
        $inner_cast_string = $arguments[0] ?? '';
        $inner_caster = CastResolver::resolve($inner_cast_string);

        return new class($inner_caster) implements CastsAttributes
        {
            /**
             * @param  CastsAttributes<mixed, mixed>  $inner_caster
             */
            public function __construct(protected CastsAttributes $inner_caster) {}

            /**
             * Cast the given value.
             *
             * @param  array<string, mixed>  $attributes
             */
            public function get($model, string $key, mixed $value, array $attributes): mixed
            {
                if ($value === null) {
                    return null;
                }

                return $this->inner_caster->get($model, $key, $value, $attributes);
            }

            /**
             * Prepare the given value for storage.
             *
             * @param  array<string, mixed>  $attributes
             * @return array<string, mixed>
             */
            public function set($model, string $key, mixed $value, array $attributes): array
            {
                if ($value === null) {
                    return [$key => null];
                }

                return $this->inner_caster->set($model, $key, $value, $attributes);
            }
        };
    }

    /**
     * Specify the inner cast for the nullable wrapper.
     */
    public static function of(string $inner_cast_string): string
    {
        return self::class.':'.$inner_cast_string;
    }
}
