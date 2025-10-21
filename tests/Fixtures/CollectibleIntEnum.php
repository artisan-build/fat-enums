<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Tests\Fixtures;

use ArtisanBuild\FatEnums\Collections\CollectibleEnum;
use ArtisanBuild\FatEnums\Collections\CollectibleEnumMethods;

enum CollectibleIntEnum: int implements CollectibleEnum
{
    use CollectibleEnumMethods;

    case One = 1;
    case Two = 2;
    case Three = 3;
}
