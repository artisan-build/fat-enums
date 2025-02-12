<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Tests\Fixtures;

use ArtisanBuild\FatEnums\Traits\FatEnums;

enum FatTestEnum
{
    use FatEnums;

    case FIRST;
    case SECOND;
    case THIRD;
    case FOURTH;
    case FIFTH;
}
