<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Casts;

use ArtisanBuild\FatEnums\Casts\TestFixtures\OtherPermissionEnum;
use ArtisanBuild\FatEnums\Casts\TestFixtures\PermissionEnum;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AsEnumArrayObjectBitmaskTest extends TestCase
{
    #[Test]
    public function it_casts_array_of_enums_to_bitmask()
    {
        /** @var Model&{roles: array<PermissionEnum>} */
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'roles' => AsEnumArrayObjectBitmask::of(PermissionEnum::class),
                ];
            }
        };

        $model->roles = [PermissionEnum::READ, PermissionEnum::WRITE];

        $this->assertEquals(3, $model->getAttributes()['roles']);
    }

    #[Test]
    public function it_casts_bitmask_to_array_of_enums()
    {
        /** @var Model&{roles: array<PermissionEnum>} */
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'roles' => AsEnumArrayObjectBitmask::of(PermissionEnum::class),
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
    public function it_throws_exception_when_null_provided_to_non_nullable_array_cast()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value cannot be null for non-nullable cast');

        /** @var Model&{roles: array<PermissionEnum>} */
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'roles' => AsEnumArrayObjectBitmask::of(PermissionEnum::class),
                ];
            }
        };

        $model->roles = null;
    }

    #[Test]
    public function it_throws_exception_for_invalid_enum_class_for_array_cast()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Class InvalidEnum must be an enum');

        /** @var Model&{roles: array<PermissionEnum>} */
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'roles' => AsEnumArrayObjectBitmask::of('InvalidEnum'),
                ];
            }
        };

        $model->roles = [];
    }

    #[Test]
    public function it_throws_exception_for_invalid_array_value()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be an array of BackedEnum cases');

        /** @var Model&{roles: array<PermissionEnum>} */
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'roles' => AsEnumArrayObjectBitmask::of(PermissionEnum::class),
                ];
            }
        };

        $model->roles = 'invalid';
    }

    #[Test]
    public function it_throws_exception_when_enum_case_is_not_instance_of_registered_enum_class_for_array_cast()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('All enum cases must be instances of ArtisanBuild\FatEnums\Casts\TestFixtures\PermissionEnum');

        /** @var Model&{roles: array<PermissionEnum>} */
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'roles' => AsEnumArrayObjectBitmask::of(PermissionEnum::class),
                ];
            }
        };

        $model->roles = [OtherPermissionEnum::READ];
    }
} 