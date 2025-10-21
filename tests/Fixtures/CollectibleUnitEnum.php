<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Tests\Fixtures;

use ArtisanBuild\FatEnums\Collections\CollectibleEnum;
use ArtisanBuild\FatEnums\Collections\CollectibleEnumMethods;

enum CollectibleUnitEnum implements CollectibleEnum
{
    use CollectibleEnumMethods;

    case Red;
    case Green;
    case Blue;
}
