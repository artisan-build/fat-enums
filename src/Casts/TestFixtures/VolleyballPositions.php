<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Casts\TestFixtures;

enum VolleyballPositions: int
{
    case SETTER = 0x1 << 0;
    case LIBERO = 0x1 << 1;
    case OUTSIDE_HITTER = 0x1 << 2;
    case MIDDLE_BLOCKER = 0x1 << 3;
}
