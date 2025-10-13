<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Enums;

use ArtisanBuild\FatEnums\Traits\UsesContext;

enum ContextUsers: string
{
    use UsesContext;

    case Happy = 'happy';
    case Sad = 'sad';
}
