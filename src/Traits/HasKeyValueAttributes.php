<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Traits;

use ArtisanBuild\FatEnums\Attributes\WithData;
use ArtisanBuild\FatEnums\Exceptions\MissingDataAttributeException;
use ArtisanBuild\FatEnums\Exceptions\MissingDataKeyException;
use ArtisanBuild\FatEnums\Support\ShouldThrow;
use ReflectionClassConstant;

trait HasKeyValueAttributes
{
    public function data(?string $key = null, mixed $default = new ShouldThrow): mixed
    {
        $data_attribute = (new ReflectionClassConstant($this::class, $this->name))
            ->getAttributes(WithData::class);

        if (empty($data_attribute)) {
            if ($default instanceof ShouldThrow) {
                throw new MissingDataAttributeException($this::class, $this->name);
            }

            return $default;
        }

        $data = $data_attribute[0]->newInstance()->data;

        if ($key === null) {
            return $data;
        }

        if (! array_key_exists($key, $data)) {
            if ($default instanceof ShouldThrow) {
                throw new MissingDataKeyException($this::class, $this->name, $key);
            }

            return $default;
        }

        return $data[$key];
    }
}
