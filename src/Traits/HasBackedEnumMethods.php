<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Traits;

use Exception;
use ReflectionEnum;

trait HasBackedEnumMethods
{
    public static function tryFrom($caseName): ?self
    {
        $rc = new ReflectionEnum(self::class);

        return $rc->hasCase($caseName) ? $rc->getConstant($caseName) : null;
    }

    public static function from($caseName): self
    {
        return self::tryFrom($caseName) ?? throw new Exception('Enum '.$caseName.' not found in '.self::class);
    }
}
