<?php

namespace ArtisanBuild\FatEnums\StateMachine;

use Attribute;
use BackedEnum;
use UnitEnum;

#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
final readonly class CanTransitionTo
{
    /**
     * @param  array<BackedEnum|UnitEnum>  $destinations
     */
    public function __construct(public array $destinations)
    {
        // ...
    }
}
