<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\StateMachine;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
final readonly class CanTransitionToSelf
{
    // ...
}
