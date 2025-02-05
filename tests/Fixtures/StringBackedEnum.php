<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Tests\Fixtures;

enum StringBackedEnum: string
{
    case Happy = 'happy';
    case Sad = 'sad';
    case Hungry = 'hungry';

}
