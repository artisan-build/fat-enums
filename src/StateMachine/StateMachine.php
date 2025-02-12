<?php

namespace ArtisanBuild\FatEnums\StateMachine;

use BackedEnum;
use UnitEnum;

interface StateMachine
{
    public function canTransitionTo(BackedEnum|UnitEnum $case): bool;

    public function canTransitionFrom(BackedEnum|UnitEnum $case): bool;

    public function transitionTo(BackedEnum|UnitEnum $case): BackedEnum|UnitEnum;

    public function transitionFrom(BackedEnum|UnitEnum $case): BackedEnum|UnitEnum;
}
