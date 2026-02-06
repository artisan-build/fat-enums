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

final class AsNullableEnumTest extends TestCase
{
    #[Test]
    public function it_returns_null_when_getting_null_value_with_collection_cast()
    {
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'permissions' => AsNullableEnum::of(AsEnumCollectionBitmask::of(PermissionEnum::class)),
                ];
            }
        };

        $model->setRawAttributes(['permissions' => null]);

        $this->assertNull($model->permissions);
    }

    #[Test]
    public function it_sets_null_value_with_collection_cast()
    {
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'permissions' => AsNullableEnum::of(AsEnumCollectionBitmask::of(PermissionEnum::class)),
                ];
            }
        };

        $model->permissions = null;

        $this->assertNull($model->getAttributes()['permissions']);
    }

    #[Test]
    public function it_delegates_non_null_get_to_inner_collection_cast()
    {
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'permissions' => AsNullableEnum::of(AsEnumCollectionBitmask::of(PermissionEnum::class)),
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
    public function it_delegates_non_null_set_to_inner_collection_cast()
    {
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'permissions' => AsNullableEnum::of(AsEnumCollectionBitmask::of(PermissionEnum::class)),
                ];
            }
        };

        $model->permissions = collect([PermissionEnum::READ, PermissionEnum::WRITE]);

        $this->assertEquals(3, $model->getAttributes()['permissions']);
    }

    #[Test]
    public function it_returns_null_when_getting_null_value_with_array_cast()
    {
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'roles' => AsNullableEnum::of(AsEnumArrayObjectBitmask::of(PermissionEnum::class)),
                ];
            }
        };

        $model->setRawAttributes(['roles' => null]);

        $this->assertNull($model->roles);
    }

    #[Test]
    public function it_sets_null_value_with_array_cast()
    {
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'roles' => AsNullableEnum::of(AsEnumArrayObjectBitmask::of(PermissionEnum::class)),
                ];
            }
        };

        $model->roles = null;

        $this->assertNull($model->getAttributes()['roles']);
    }

    #[Test]
    public function it_delegates_non_null_get_to_inner_array_cast()
    {
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'roles' => AsNullableEnum::of(AsEnumArrayObjectBitmask::of(PermissionEnum::class)),
                ];
            }
        };

        $model->setRawAttributes(['roles' => 3]);

        $this->assertIsArray($model->roles);
        $this->assertCount(2, $model->roles);
        $this->assertEquals(PermissionEnum::READ, $model->roles[0]);
        $this->assertEquals(PermissionEnum::WRITE, $model->roles[1]);
    }

    #[Test]
    public function it_delegates_non_null_set_to_inner_array_cast()
    {
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'roles' => AsNullableEnum::of(AsEnumArrayObjectBitmask::of(PermissionEnum::class)),
                ];
            }
        };

        $model->roles = [PermissionEnum::READ, PermissionEnum::WRITE];

        $this->assertEquals(3, $model->getAttributes()['roles']);
    }

    #[Test]
    public function it_works_with_casts_property_syntax()
    {
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'permissions' => AsNullableEnum::class.':'.AsEnumCollectionBitmask::class.':'.PermissionEnum::class,
                ];
            }
        };

        $model->setRawAttributes(['permissions' => null]);
        $this->assertNull($model->permissions);

        $model->setRawAttributes(['permissions' => 3]);
        $this->assertInstanceOf(Collection::class, $model->permissions);
        $this->assertCount(2, $model->permissions);
    }

    #[Test]
    public function it_throws_exception_for_invalid_inner_cast()
    {
        $this->expectException(InvalidArgumentException::class);

        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'permissions' => AsNullableEnum::of('NonExistentCast:SomeEnum'),
                ];
            }
        };

        $model->permissions = collect([]);
    }

    #[Test]
    public function it_throws_exception_for_wrong_enum_type_in_collection()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('All enum cases must be instances of ArtisanBuild\FatEnums\Casts\TestFixtures\PermissionEnum');

        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'permissions' => AsNullableEnum::of(AsEnumCollectionBitmask::of(PermissionEnum::class)),
                ];
            }
        };

        $model->permissions = collect([OtherPermissionEnum::READ]);
    }

    #[Test]
    public function it_throws_exception_for_wrong_enum_type_in_array()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('All enum cases must be instances of ArtisanBuild\FatEnums\Casts\TestFixtures\PermissionEnum');

        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'roles' => AsNullableEnum::of(AsEnumArrayObjectBitmask::of(PermissionEnum::class)),
                ];
            }
        };

        $model->roles = [OtherPermissionEnum::READ];
    }
}
