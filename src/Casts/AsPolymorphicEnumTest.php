<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Casts;

use ArtisanBuild\FatEnums\Casts\TestFixtures\ActivityEnum;
use ArtisanBuild\FatEnums\Casts\TestFixtures\BasketballPositions;
use ArtisanBuild\FatEnums\Casts\TestFixtures\SportEnum;
use ArtisanBuild\FatEnums\Casts\TestFixtures\VolleyballPositions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AsPolymorphicEnumTest extends TestCase
{
    #[Test]
    public function it_resolves_correct_cast_based_on_discriminator_for_get()
    {
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'positions' => AsPolymorphicEnum::of('sport', [
                        'volleyball' => AsEnumCollectionBitmask::of(VolleyballPositions::class),
                        'basketball' => AsEnumCollectionBitmask::of(BasketballPositions::class),
                    ]),
                ];
            }
        };

        $model->setRawAttributes(['sport' => 'volleyball', 'positions' => 3]);

        $this->assertInstanceOf(Collection::class, $model->positions);
        $this->assertCount(2, $model->positions);
        $this->assertTrue($model->positions->contains(VolleyballPositions::SETTER));
        $this->assertTrue($model->positions->contains(VolleyballPositions::LIBERO));
    }

    #[Test]
    public function it_resolves_different_cast_for_different_discriminator()
    {
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'positions' => AsPolymorphicEnum::of('sport', [
                        'volleyball' => AsEnumCollectionBitmask::of(VolleyballPositions::class),
                        'basketball' => AsEnumCollectionBitmask::of(BasketballPositions::class),
                    ]),
                ];
            }
        };

        $model->setRawAttributes(['sport' => 'basketball', 'positions' => 3]);

        $this->assertInstanceOf(Collection::class, $model->positions);
        $this->assertCount(2, $model->positions);
        $this->assertTrue($model->positions->contains(BasketballPositions::POINT_GUARD));
        $this->assertTrue($model->positions->contains(BasketballPositions::SHOOTING_GUARD));
    }

    #[Test]
    public function it_resolves_correct_cast_for_set()
    {
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'positions' => AsPolymorphicEnum::of('sport', [
                        'volleyball' => AsEnumCollectionBitmask::of(VolleyballPositions::class),
                        'basketball' => AsEnumCollectionBitmask::of(BasketballPositions::class),
                    ]),
                ];
            }
        };

        $model->setRawAttributes(['sport' => 'volleyball']);
        $model->positions = collect([VolleyballPositions::SETTER, VolleyballPositions::OUTSIDE_HITTER]);

        $this->assertEquals(5, $model->getAttributes()['positions']);
    }

    #[Test]
    public function it_throws_exception_when_discriminator_has_no_matching_entry()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("No cast mapping found for discriminator value 'soccer' on field 'sport'");

        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'positions' => AsPolymorphicEnum::of('sport', [
                        'volleyball' => AsEnumCollectionBitmask::of(VolleyballPositions::class),
                        'basketball' => AsEnumCollectionBitmask::of(BasketballPositions::class),
                    ]),
                ];
            }
        };

        $model->setRawAttributes(['sport' => 'soccer', 'positions' => 1]);
        $_ = $model->positions;
    }

    #[Test]
    public function it_supports_backed_enum_cases_via_case_helper()
    {
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'positions' => AsPolymorphicEnum::of('sport', [
                        ...AsPolymorphicEnum::case(SportEnum::Volleyball, AsEnumCollectionBitmask::of(VolleyballPositions::class)),
                        ...AsPolymorphicEnum::case(SportEnum::Basketball, AsEnumCollectionBitmask::of(BasketballPositions::class)),
                    ]),
                ];
            }
        };

        $model->setRawAttributes(['sport' => 'volleyball', 'positions' => 3]);

        $this->assertInstanceOf(Collection::class, $model->positions);
        $this->assertTrue($model->positions->contains(VolleyballPositions::SETTER));
        $this->assertTrue($model->positions->contains(VolleyballPositions::LIBERO));
    }

    #[Test]
    public function it_supports_non_backed_enum_cases_via_case_helper()
    {
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'positions' => AsPolymorphicEnum::of('activity', [
                        ...AsPolymorphicEnum::case(ActivityEnum::Volleyball, AsEnumCollectionBitmask::of(VolleyballPositions::class)),
                        ...AsPolymorphicEnum::case(ActivityEnum::Basketball, AsEnumCollectionBitmask::of(BasketballPositions::class)),
                    ]),
                ];
            }
        };

        $model->setRawAttributes(['activity' => 'Volleyball', 'positions' => 3]);

        $this->assertInstanceOf(Collection::class, $model->positions);
        $this->assertTrue($model->positions->contains(VolleyballPositions::SETTER));
        $this->assertTrue($model->positions->contains(VolleyballPositions::LIBERO));
    }

    #[Test]
    public function it_works_with_array_object_bitmask()
    {
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'positions' => AsPolymorphicEnum::of('sport', [
                        'volleyball' => AsEnumArrayObjectBitmask::of(VolleyballPositions::class),
                        'basketball' => AsEnumArrayObjectBitmask::of(BasketballPositions::class),
                    ]),
                ];
            }
        };

        $model->setRawAttributes(['sport' => 'basketball', 'positions' => 3]);

        $this->assertIsArray($model->positions);
        $this->assertCount(2, $model->positions);
        $this->assertEquals(BasketballPositions::POINT_GUARD, $model->positions[0]);
        $this->assertEquals(BasketballPositions::SHOOTING_GUARD, $model->positions[1]);
    }

    #[Test]
    public function it_composes_with_as_nullable_enum()
    {
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'positions' => AsNullableEnum::of(AsPolymorphicEnum::of('sport', [
                        'volleyball' => AsEnumCollectionBitmask::of(VolleyballPositions::class),
                        'basketball' => AsEnumCollectionBitmask::of(BasketballPositions::class),
                    ])),
                ];
            }
        };

        $model->setRawAttributes(['sport' => 'volleyball', 'positions' => null]);
        $this->assertNull($model->positions);

        $model->setRawAttributes(['sport' => 'volleyball', 'positions' => 3]);
        $this->assertInstanceOf(Collection::class, $model->positions);
        $this->assertTrue($model->positions->contains(VolleyballPositions::SETTER));
    }

    #[Test]
    public function it_composes_with_as_nullable_enum_for_set()
    {
        $model = new class extends Model
        {
            protected function casts(): array
            {
                return [
                    'positions' => AsNullableEnum::of(AsPolymorphicEnum::of('sport', [
                        'volleyball' => AsEnumCollectionBitmask::of(VolleyballPositions::class),
                        'basketball' => AsEnumCollectionBitmask::of(BasketballPositions::class),
                    ])),
                ];
            }
        };

        $model->setRawAttributes(['sport' => 'volleyball']);
        $model->positions = null;
        $this->assertNull($model->getAttributes()['positions']);

        $model->positions = collect([VolleyballPositions::SETTER]);
        $this->assertEquals(1, $model->getAttributes()['positions']);
    }
}
