<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Casts;

use ArtisanBuild\FatEnums\Casts\TestFixtures\PermissionEnum;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CastResolverTest extends TestCase
{
    #[Test]
    public function it_resolves_a_cast_string_to_a_casts_attributes_instance()
    {
        $cast_string = AsEnumCollectionBitmask::class.':'.PermissionEnum::class;

        $result = CastResolver::resolve($cast_string);

        $this->assertInstanceOf(CastsAttributes::class, $result);
    }

    #[Test]
    public function it_resolves_array_object_bitmask_cast_string()
    {
        $cast_string = AsEnumArrayObjectBitmask::class.':'.PermissionEnum::class;

        $result = CastResolver::resolve($cast_string);

        $this->assertInstanceOf(CastsAttributes::class, $result);
    }

    #[Test]
    public function it_throws_exception_for_nonexistent_class()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cast class NonExistentClass does not exist');

        CastResolver::resolve('NonExistentClass:SomeEnum');
    }

    #[Test]
    public function it_throws_exception_for_non_castable_class()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('must implement Castable');

        CastResolver::resolve(PermissionEnum::class);
    }
}
