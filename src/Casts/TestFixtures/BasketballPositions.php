<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Casts\TestFixtures;

enum BasketballPositions: int
{
    case POINT_GUARD = 0x1 << 0;
    case SHOOTING_GUARD = 0x1 << 1;
    case SMALL_FORWARD = 0x1 << 2;
    case POWER_FORWARD = 0x1 << 3;
    case CENTER = 0x1 << 4;
}
