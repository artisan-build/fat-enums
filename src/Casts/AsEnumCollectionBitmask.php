<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Casts;

use BackedEnum;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Collection;
use InvalidArgumentException;

final class AsEnumCollectionBitmask implements Castable
{
    /**
     * Get the caster class to use when casting from / to this cast target.
     *
     * @template TEnum of \BackedEnum
     *
     * @param  array{class-string<TEnum>}  $arguments
     * @return \Illuminate\Contracts\Database\Eloquent\CastsAttributes<\Illuminate\Support\Collection<array-key, TEnum>, iterable<TEnum>>
     */
    public static function castUsing(array $arguments)
    {
        return new class($arguments) implements CastsAttributes
        {
            protected $arguments;

            public function __construct(array $arguments)
            {
                $this->arguments = $arguments;
                $enumClass = $arguments[0];

                if (! enum_exists($enumClass)) {
                    throw new InvalidArgumentException("Class {$enumClass} must be an enum");
                }

                if (! is_subclass_of($enumClass, BackedEnum::class)) {
                    throw new InvalidArgumentException("Class {$enumClass} must be a BackedEnum");
                }
            }

            /**
             * Cast the given value.
             *
             * @param  array<string, mixed>  $attributes
             * @return \Illuminate\Support\Collection<int, BackedEnum>
             */
            public function get($model, $key, $value, $attributes)
            {
                $enumClass = $this->arguments[0];

                $bitmask = (int) ($value ?? 0);

                return collect($enumClass::cases())
                    ->filter(fn (BackedEnum $case) => ($bitmask & $case->value) === $case->value);
            }

            /**
             * Prepare the given value for storage.
             *
             * @param  array<string, mixed>  $attributes
             * @return array<string, int>
             */
            public function set($model, $key, $value, $attributes)
            {
                if ($value === null) {
                    throw new InvalidArgumentException('Value cannot be null for non-nullable cast');
                }

                if (! $value instanceof Collection) {
                    throw new InvalidArgumentException('Value must be a Collection of BackedEnum cases');
                }

                $enumClass = $this->arguments[0];

                $bitmask = $value->reduce(
                    function (int $carry, BackedEnum $case) use ($enumClass) {
                        if (! $case instanceof $enumClass) {
                            throw new InvalidArgumentException("All enum cases must be instances of {$enumClass}");
                        }

                        return $carry | $case->value;
                    },
                    0
                );

                return [$key => $bitmask];
            }
        };
    }

    /**
     * Specify the Enum for the cast.
     *
     * @param  class-string<BackedEnum>  $class
     * @return string
     */
    public static function of($class)
    {
        return self::class.':'.$class;
    }
}
