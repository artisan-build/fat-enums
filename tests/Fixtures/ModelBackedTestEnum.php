<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Tests\Fixtures;

use ArtisanBuild\FatEnums\Traits\DatabaseRecordsEnum;

enum ModelBackedTestEnum: int
{
    use DatabaseRecordsEnum;

    case Foo = 1111;
    case Bar = 2222;
    case Baz = 3333;

    public const ModelName = DummyModel::class;
}
