<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Casts;

use BackedEnum;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;
use UnitEnum;

/**
 * A cast that resolves to different inner casts based on a discriminator field's raw value.
 */
class AsPolymorphicEnum implements Castable
{
    /**
     * Get the caster class to use when casting from / to this cast target.
     *
     * @param  array<int, string>  $arguments
     */
    public static function castUsing(array $arguments): CastsAttributes
    {
        $encoded = $arguments[0] ?? '';
        $decoded = json_decode(base64_decode($encoded), true);

        if (! is_array($decoded) || ! isset($decoded['field'], $decoded['map'])) {
            throw new InvalidArgumentException('Invalid polymorphic cast configuration');
        }

        $field = $decoded['field'];
        /** @var array<string, string> $map */
        $map = $decoded['map'];

        return new class($field, $map) implements CastsAttributes
        {
            /** @var array<string, CastsAttributes> */
            protected array $resolved_casters = [];

            /**
             * @param  array<string, string>  $map
             */
            public function __construct(
                protected string $field,
                protected array $map,
            ) {}

            /**
             * Cast the given value.
             *
             * @param  array<string, mixed>  $attributes
             */
            public function get($model, string $key, mixed $value, array $attributes): mixed
            {
                $caster = $this->resolveCaster($attributes);

                return $caster->get($model, $key, $value, $attributes);
            }

            /**
             * Prepare the given value for storage.
             *
             * @param  array<string, mixed>  $attributes
             * @return array<string, mixed>
             */
            public function set($model, string $key, mixed $value, array $attributes): array
            {
                $caster = $this->resolveCaster($attributes);

                return $caster->set($model, $key, $value, $attributes);
            }

            /**
             * Resolve the inner caster based on the discriminator field value.
             *
             * @param  array<string, mixed>  $attributes
             */
            protected function resolveCaster(array $attributes): CastsAttributes
            {
                $discriminator = (string) ($attributes[$this->field] ?? '');

                if (! isset($this->map[$discriminator])) {
                    throw new InvalidArgumentException(
                        "No cast mapping found for discriminator value '{$discriminator}' on field '{$this->field}'"
                    );
                }

                if (! isset($this->resolved_casters[$discriminator])) {
                    $this->resolved_casters[$discriminator] = CastResolver::resolve($this->map[$discriminator]);
                }

                return $this->resolved_casters[$discriminator];
            }
        };
    }

    /**
     * Build the cast string for a polymorphic enum cast.
     *
     * @param  array<string|int, string>  $map
     */
    public static function of(string $field, array $map): string
    {
        $normalized_map = [];
        foreach ($map as $key => $cast_string) {
            $normalized_map[(string) $key] = $cast_string;
        }

        $encoded = base64_encode((string) json_encode([
            'field' => $field,
            'map' => $normalized_map,
        ]));

        return self::class.':'.$encoded;
    }

    /**
     * Build a single-element map entry from an enum case, for spreading into the map array.
     *
     * @return array<string, string>
     */
    public static function case(UnitEnum $enum, string $cast_string): array
    {
        $key = $enum instanceof BackedEnum ? (string) $enum->value : $enum->name;

        return [$key => $cast_string];
    }
}
