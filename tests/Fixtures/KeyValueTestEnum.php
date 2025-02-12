<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Tests\Fixtures;

use ArtisanBuild\FatEnums\Attributes\WithData;
use ArtisanBuild\FatEnums\Traits\HasKeyValueAttributes;

enum KeyValueTestEnum
{
    use HasKeyValueAttributes;

    #[WithData(['key' => 'value'])]
    case CASE_WITH_DATA;

    case CASE_WITHOUT_DATA;
}
