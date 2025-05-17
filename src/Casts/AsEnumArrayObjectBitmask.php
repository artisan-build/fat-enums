<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Casts;

use BackedEnum;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

final class AsEnumArrayObjectBitmask implements Castable
{
    /**
     * Get the caster class to use when casting from / to this cast target.
     *
     * @template TEnum of \BackedEnum
     *
     * @param  array{class-string<TEnum>}  $arguments
     * @return \Illuminate\Contracts\Database\Eloquent\CastsAttributes<array<TEnum>, int>
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
             * @return array<BackedEnum>
             */
            public function get($model, $key, $value, $attributes)
            {
                $enumClass = $this->arguments[0];

                if ($value === null) {
                    return [];
                }

                $bitmask = (int) $value;
                $result = [];

                foreach ($enumClass::cases() as $case) {
                    if (($bitmask & $case->value) === $case->value) {
                        $result[] = $case;
                    }
                }

                return $result;
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

                if (! is_array($value)) {
                    throw new InvalidArgumentException('Value must be an array of BackedEnum cases');
                }

                $enumClass = $this->arguments[0];
                $bitmask = 0;

                foreach ($value as $enum) {
                    if (! $enum instanceof BackedEnum) {
                        throw new InvalidArgumentException('All values must be BackedEnum cases');
                    }
                    if (! $enum instanceof $enumClass) {
                        throw new InvalidArgumentException("All enum cases must be instances of {$enumClass}");
                    }
                    $bitmask |= $enum->value;
                }

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
        return self::class . ':' . $class;
    }
} 