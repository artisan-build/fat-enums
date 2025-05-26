<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Collections;

interface CollectibleEnum extends EnumCollection
{
    public static function collect(): EnumCollection;
}
