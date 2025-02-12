<?php

namespace ArtisanBuild\FatEnums\StateMachine;

use BackedEnum;
use ReflectionClassConstant;
use RuntimeException;
use UnitEnum;

trait IsStateMachine
{
    public static function canTransitionBetween(
        BackedEnum|UnitEnum $source,
        BackedEnum|UnitEnum $destination,
    ): bool {
        if (
            $source === $destination &&
            (new ReflectionClassConstant(self::class, $source->name))
                ->getAttributes(CanTransitionToSelf::class)
        ) {
            return true;
        }

        if (
            (new ReflectionClassConstant(self::class, $source->name))
                ->getAttributes(FinalState::class)
        ) {
            return false;
        }

        $attributes = (new ReflectionClassConstant(self::class, $source->name))
            ->getAttributes(CanTransitionTo::class);

        if (count($attributes) === 0) {
            throw new RuntimeException(sprintf(
                '%s::%s does not have the CanTransitionTo attribute.',
                $source::class,
                $source->name,
            ));
        }

        return in_array($destination, $attributes[0]->newInstance()->destinations);
    }

    public function canTransitionTo(BackedEnum|UnitEnum $case): bool
    {
        return self::canTransitionBetween($this, $case);
    }

    public function canTransitionFrom(BackedEnum|UnitEnum $case): bool
    {
        return self::canTransitionBetween($case, $this);
    }

    public function transitionTo(BackedEnum|UnitEnum $case): BackedEnum|UnitEnum
    {
        if ($this->canTransitionTo($case)) {
            return $case;
        }

        throw new InvalidStateTransition('Invalid state transition');
    }

    public function transitionFrom(BackedEnum|UnitEnum $case): BackedEnum|UnitEnum
    {
        if ($this->canTransitionFrom($case)) {
            return $case;
        }

        throw new InvalidStateTransition('Invalid state transition');
    }
}
