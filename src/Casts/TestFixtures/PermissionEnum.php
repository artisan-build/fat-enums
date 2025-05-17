<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Casts\TestFixtures;

enum PermissionEnum: int
{
    case READ = 0x1 << 0;
    case WRITE = 0x1 << 1;
    case DELETE = 0x1 << 2;
    case ADMIN = 0x1 << 3;
}
