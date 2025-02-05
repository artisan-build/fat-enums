<?php

namespace ArtisanBuild\FatEnums\StateMachine;

use BackedEnum;
use Exception;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionClassConstant;
use ReflectionNamedType;

trait HasStateMachine
{
    public static function onlyRunInVerbsState(): void
    {
        if (! is_a(static::class, \Thunk\Verbs\State::class, true)) {
            throw new Exception('This method can only be used on Verbs States.');
        }
    }

    /**
     * @return enum-string|null
     */
    private static function getNonUnionNonIntersectionType(string $property, $allow_null = false): ?string
    {
        $type = (new ReflectionClass(static::class))
            ->getProperty($property)
            ->getType();

        if (is_null($type)) {
            if ($allow_null) {
                return null;
            } else {
                throw new InvalidArgumentException("Property {$property} does not have a type defined on ".static::class);
            }
        }

        if (! $type instanceof ReflectionNamedType) {
            throw new InvalidArgumentException("Property {$property} cannot be a union or intersection type on ".static::class);
        }

        return $type->getName();
    }

    private static function validateStateMachine(string $property): void
    {
        if (! (new ReflectionClass(static::class))->hasProperty($property)) {
            throw new InvalidArgumentException("Property {$property} does not exist on ".static::class);
        }

        $type = self::getNonUnionNonIntersectionType($property);

        if (! enum_exists($type)) {
            throw new InvalidArgumentException("Property {$property} is not an enum on ".static::class);
        }

        if (! in_array(StateMachine::class, class_implements($type))) {
            throw new InvalidStateMachineConfig("Enum {$type} does not implement ".StateMachine::class);
        }

        if (! defined("{$type}::DEFAULT")) {
            throw new InvalidStateMachineConfig("Enum {$type} does not have a DEFAULT defined");
        }
    }

    public static function getDefaultState(string $property): BackedEnum
    {
        self::validateStateMachine($property);

        $type = self::getNonUnionNonIntersectionType($property);

        return $type::DEFAULT;
    }

    /**
     * @param  BackedEnum|array<BackedEnum>  $destination
     */
    public function canTransitionTo(
        string $property,
        BackedEnum|array $destination,
    ): bool {
        if (empty($destination)) {
            throw new InvalidArgumentException("Cannot transition '{$property}' to an empty destination");
        }

        $destinations = is_array($destination) ? $destination : [$destination];
        foreach ($destinations as $destination) {
            if (! $this->canTransitionBetween($property, $this->{$property}, $destination)) {
                return false;
            }
        }

        return true;
    }

    public function canTransitionBetween(
        string $property,
        BackedEnum $source,
        BackedEnum $destination,
    ): bool {
        self::validateStateMachine($property);

        assert($source instanceof StateMachine);
        assert($destination instanceof StateMachine);

        // `assert` becomes a no-op in production. So even though
        // it helps with our internal type checking, it doesn't
        // actually provided the needed safety in production.
        foreach ([$source, $destination] as $enum) {
            if (
                collect(class_implements($enum))->doesntContain(StateMachine::class)
            ) {
                throw new InvalidArgumentException(sprintf(
                    '%s does not implement %s',
                    $enum::class,
                    StateMachine::class,
                ));
            }
        }

        return $source->canTransitionTo($destination);
    }

    public function transitionTo(string $property, mixed $destination): self
    {
        self::validateStateMachine($property);

        $source = $this->{$property};

        if (! $this->canTransitionBetween($property, $source, $destination)) {
            throw new InvalidStateTransition(sprintf(
                'Cannot transition %s from %s to %s on %s',
                $property,
                $source->value,
                $destination->value,
                $this::class,
            ));
        }

        $this->{$property} = $destination;

        return $this;
    }

    public static function serializeStateMachine(string $property): array
    {
        self::validateStateMachine($property);

        $enum = self::getNonUnionNonIntersectionType($property);

        $cases = collect($enum::cases());

        $caseValueSorter = function (string $first, string $second) use ($cases, $enum): int {
            $first = $cases->search($enum::from($first));
            $second = $cases->search($enum::from($second));

            return $first <=> $second;
        };

        $config = [
            'Default State' => $enum::DEFAULT->value,
            'Final States' => $cases
                ->mapWithKeys(fn ($case) => [$case->value => $case])
                ->map(fn ($case) => (new ReflectionClassConstant($enum, $case->name))
                    ->getAttributes(FinalState::class)
                )
                ->reject(fn ($attributes) => empty($attributes))
                ->keys()
                ->sort($caseValueSorter)
                ->values()
                ->toArray(),
            'Allowed Transitions' => $cases
                ->mapWithKeys(fn ($case) => [$case->value => $case])
                ->map(fn ($case) => (new ReflectionClassConstant($enum, $case->name))
                    ->getAttributes(CanTransitionTo::class)
                )
                ->reject(fn ($attributes) => empty($attributes))
                ->map(fn ($attributes) => $attributes[0]->newInstance()->destinations)
                ->map(fn ($destinations) => collect($destinations)
                    ->map(fn ($destination) => $destination->value ?? $destination->name)
                    ->sort($caseValueSorter)
                    ->values()
                    ->toArray()
                )
                ->toArray(),
            'Self Transitions' => $cases
                ->mapWithKeys(fn ($case) => [$case->value => $case])
                ->map(fn ($case) => (new ReflectionClassConstant($enum, $case->name))
                    ->getAttributes(CanTransitionToSelf::class)
                )
                ->reject(fn ($attributes) => empty($attributes))
                ->keys()
                ->sort($caseValueSorter)
                ->values()
                ->toArray(),
        ];

        return [$property => $config];
    }
}
