<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Casts\TestFixtures;

use ArtisanBuild\FatEnums\Casts\AsEnumCollectionBitmask;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Test fixture model for collection-based bitmask casting.
 *
 * @property Collection<int, PermissionEnum> $permissions
 */
class PermissionsModel extends Model
{
    /**
     * Get the casts array.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'permissions' => AsEnumCollectionBitmask::of(PermissionEnum::class),
        ];
    }
}
