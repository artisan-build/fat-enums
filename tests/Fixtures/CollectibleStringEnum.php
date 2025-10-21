<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Tests\Fixtures;

use ArtisanBuild\FatEnums\Collections\CollectibleEnum;
use ArtisanBuild\FatEnums\Collections\CollectibleEnumMethods;

enum CollectibleStringEnum: string implements CollectibleEnum
{
    use CollectibleEnumMethods;

    case Active = 'active';
    case Inactive = 'inactive';
    case Pending = 'pending';
}
