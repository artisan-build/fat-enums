<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Casts;

use ArtisanBuild\FatEnums\Casts\TestFixtures\OtherPermissionEnum;
use ArtisanBuild\FatEnums\Casts\TestFixtures\PermissionEnum;
use ArtisanBuild\FatEnums\Casts\TestFixtures\RolesModel;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AsEnumArrayObjectBitmaskTest extends TestCase
{
    #[Test]
    public function it_casts_array_of_enums_to_bitmask(): void
    {
        $model = new RolesModel;

        $model->roles = [PermissionEnum::READ, PermissionEnum::WRITE];

        $this->assertEquals(3, $model->getAttributes()['roles']);
    }

    #[Test]
    public function it_casts_bitmask_to_array_of_enums(): void
    {
        $model = new RolesModel;

        $model->setRawAttributes(['roles' => 3]);

        $this->assertIsArray($model->roles);
        $this->assertCount(2, $model->roles);
        $this->assertEquals(PermissionEnum::READ, $model->roles[0]);
        $this->assertEquals(PermissionEnum::WRITE, $model->roles[1]);
    }

    #[Test]
    public function it_throws_exception_when_null_provided_to_non_nullable_array_cast(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value cannot be null for non-nullable cast');

        $model = new RolesModel;

        $model->roles = null;
    }

    #[Test]
    public function it_throws_exception_for_invalid_enum_class_for_array_cast(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Class InvalidEnum must be an enum');

        $model = new class extends Model
        {
            /**
             * Get the casts array.
             *
             * @return array<string, string>
             */
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
    public function it_throws_exception_for_invalid_array_value(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be an array of BackedEnum cases');

        $model = new RolesModel;

        $model->roles = 'invalid';
    }

    #[Test]
    public function it_throws_exception_when_enum_case_is_not_instance_of_registered_enum_class_for_array_cast(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('All enum cases must be instances of ArtisanBuild\FatEnums\Casts\TestFixtures\PermissionEnum');

        $model = new RolesModel;

        $model->roles = [OtherPermissionEnum::READ];
    }

    #[Test]
    public function it_returns_empty_array_when_null_bitmask_is_stored(): void
    {
        $model = new RolesModel;

        $model->setRawAttributes(['roles' => null]);

        $this->assertIsArray($model->roles);
        $this->assertCount(0, $model->roles);
    }

    #[Test]
    public function it_throws_exception_when_non_backed_enum_value_in_array(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('All values must be BackedEnum cases');

        $model = new RolesModel;

        $model->roles = ['not_an_enum'];
    }
}
