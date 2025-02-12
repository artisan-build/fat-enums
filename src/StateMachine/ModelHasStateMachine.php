<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\StateMachine;

use BackedEnum;
use Exception;
use InvalidArgumentException;
use ReflectionClass;

trait ModelHasStateMachine
{
    use HasStateMachine;

    /**
     * This will run on it's own when the model is booted.
     * This should _never_ be run manually.
     *
     * @internal
     */
    protected static function bootModelHasStateMachine(): void
    {
        if (! (new ReflectionClass(static::class))->hasProperty('state_machines')) {
            throw new Exception('The HasStateMachine::bootHasStateMachine() method can only be used on models that define a $state_machines array property.');
        }

        if (self::getNonUnionNonIntersectionType('state_machines', allow_null: true) !== 'array') {
            throw new InvalidArgumentException('The $state_machines property must be an array on '.static::class);
        }

        static::updating(function (self $model): void {
            $casts = $model->getCasts();

            foreach ($model->getDirty() as $attribute => $value) {
                if (in_array($attribute, $model->state_machines)) {
                    // Make sure the attribute exists in $casts array
                    if (! array_key_exists($attribute, $casts)) {
                        throw new InvalidArgumentException("{$attribute} is not a valid cast on ".$model::class);
                    }

                    // Make sure it's an enum
                    if (! enum_exists($casts[$attribute])) {
                        throw new InvalidArgumentException("{$casts[$attribute]} is not a valid enum.");
                    }

                    // Make sure it implements the StateMachine contract
                    if (! in_array(StateMachine::class, class_implements($casts[$attribute]))) {
                        throw new InvalidArgumentException("{$casts[$attribute]} does not implement ".StateMachine::class);
                    }

                    if (is_string($value) || is_int($value)) {
                        $value = is_a($casts[$attribute], BackedEnum::class, true)
                            ? $casts[$attribute]::from($value)
                            : $casts[$attribute]::{$value};
                    }

                    $original = $model->getOriginal($attribute);

                    if (! $original->canTransitionTo($value)) {
                        throw new InvalidStateTransition(sprintf(
                            InvalidStateTransition::MESSAGE,
                            $original->value,
                            $value->value,
                            $model::class,
                            $attribute,
                            $casts[$attribute],
                        ));
                    }
                }
            }
        });
    }
}
