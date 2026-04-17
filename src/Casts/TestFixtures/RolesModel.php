<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Casts\TestFixtures;

use ArtisanBuild\FatEnums\Casts\AsEnumArrayObjectBitmask;
use Illuminate\Database\Eloquent\Model;
use Override;

/**
 * Test fixture model for array-based bitmask casting.
 *
 * @property array<PermissionEnum> $roles
 */
class RolesModel extends Model
{
    /**
     * Get the casts array.
     *
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'roles' => AsEnumArrayObjectBitmask::of(PermissionEnum::class),
        ];
    }
}
