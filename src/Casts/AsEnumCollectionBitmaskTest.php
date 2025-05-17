<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Casts;

use ArtisanBuild\FatEnums\Casts\TestFixtures\OtherPermissionEnum;
use ArtisanBuild\FatEnums\Casts\TestFixtures\PermissionEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AsEnumCollectionBitmaskTest extends TestCase
{
    #[Test]
    public function it_casts_collection_of_enums_to_bitmask()
    {
        /** @var Model&{permissions: Collection<PermissionEnum>} */
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'permissions' => AsEnumCollectionBitmask::of(PermissionEnum::class),
                ];
            }
        };

        $permissions = collect([PermissionEnum::READ, PermissionEnum::WRITE]);
        $model->permissions = $permissions;

        $this->assertEquals(3, $model->getAttributes()['permissions']);
    }

    #[Test]
    public function it_casts_bitmask_to_collection_of_enums()
    {
        /** @var Model&{permissions: Collection<PermissionEnum>} */
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'permissions' => AsEnumCollectionBitmask::of(PermissionEnum::class),
                ];
            }
        };

        $model->setRawAttributes(['permissions' => 3]);

        $this->assertInstanceOf(Collection::class, $model->permissions);
        $this->assertCount(2, $model->permissions);
        $this->assertTrue($model->permissions->contains(PermissionEnum::READ));
        $this->assertTrue($model->permissions->contains(PermissionEnum::WRITE));
    }

    #[Test]
    public function it_throws_exception_when_null_provided_to_non_nullable_collection_cast()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value cannot be null for non-nullable cast');

        /** @var Model&{permissions: Collection<PermissionEnum>} */
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'permissions' => AsEnumCollectionBitmask::of(PermissionEnum::class),
                ];
            }
        };

        $model->permissions = null;
    }

    #[Test]
    public function it_throws_exception_for_invalid_enum_class()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Class InvalidEnum must be an enum');

        /** @var Model&{permissions: Collection<PermissionEnum>} */
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'permissions' => AsEnumCollectionBitmask::of('InvalidEnum'),
                ];
            }
        };

        $model->permissions = collect([]);
    }

    #[Test]
    public function it_throws_exception_for_invalid_collection_value()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be a Collection of BackedEnum cases');

        /** @var Model&{permissions: Collection<PermissionEnum>} */
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'permissions' => AsEnumCollectionBitmask::of(PermissionEnum::class),
                ];
            }
        };

        $model->permissions = 'invalid';
    }

    #[Test]
    public function it_throws_exception_when_enum_case_is_not_instance_of_registered_enum_class()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('All enum cases must be instances of ArtisanBuild\FatEnums\Casts\TestFixtures\PermissionEnum');

        /** @var Model&{permissions: Collection<PermissionEnum>} */
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'permissions' => AsEnumCollectionBitmask::of(PermissionEnum::class),
                ];
            }
        };

        $model->permissions = collect([OtherPermissionEnum::READ]);
    }
}
