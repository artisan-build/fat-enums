<?php

declare(strict_types=1);

use ArtisanBuild\FatEnums\StateMachine\CanTransitionTo;
use ArtisanBuild\FatEnums\StateMachine\InvalidStateTransition;
use ArtisanBuild\FatEnums\StateMachine\IsStateMachine;
use ArtisanBuild\FatEnums\StateMachine\StateMachine;
use ArtisanBuild\FatEnums\Tests\Fixtures\ClassWithStateMachine;
use ArtisanBuild\FatEnums\Tests\Fixtures\StateMachineTestEnum;

it('can get the default state', function (): void {
    expect(ClassWithStateMachine::getDefaultState('status'))
        ->toBe(StateMachineTestEnum::START);
});

it('can validate transitions using array', function (): void {
    $machine = new ClassWithStateMachine;
    $machine->status = StateMachineTestEnum::START;

    expect($machine->canTransitionTo(
        property: 'status',
        destination: [
            StateMachineTestEnum::MIDDLE,
            StateMachineTestEnum::END,
        ],
    ))->toBeTrue();
});

it('can validate transitions using string', function (): void {
    $machine = new ClassWithStateMachine;

    expect($machine->canTransitionBetween(
        property: 'status',
        source: StateMachineTestEnum::START,
        destination: StateMachineTestEnum::MIDDLE,
    ))->toBeTrue();
});

it('cannot transition to default state', function (): void {
    $machine = new ClassWithStateMachine;

    expect($machine->canTransitionBetween(
        property: 'status',
        source: StateMachineTestEnum::MIDDLE,
        destination: ClassWithStateMachine::getDefaultState('status'),
    ))->toBeFalse();
});

test('a final state cannot transition to anything', function (): void {
    $machine = new ClassWithStateMachine;
    $machine->status = StateMachineTestEnum::CANCELLED;

    expect(fn () => $machine->transitionTo(
        property: 'status',
        destination: StateMachineTestEnum::START,
    ))->toThrow(InvalidStateTransition::class);
});

it('can transition between allowed states', function (): void {
    $machine = new ClassWithStateMachine;

    $machine->transitionTo('status', StateMachineTestEnum::MIDDLE);

    expect($machine->status)->toBe(StateMachineTestEnum::MIDDLE);

    $machine->transitionTo('status', StateMachineTestEnum::END);

    expect($machine->status)->toBe(StateMachineTestEnum::END);
});

it('throws exception when transition is invalid', function (): void {
    $machine = new ClassWithStateMachine;

    $machine->transitionTo('status', StateMachineTestEnum::MIDDLE);

    expect(fn () => $machine->transitionTo('status', StateMachineTestEnum::START))
        ->toThrow(InvalidStateTransition::class);
});

it('throws exception when property does not exist', function (): void {
    $machine = new ClassWithStateMachine;

    expect(fn () => $machine->transitionTo('not_real', StateMachineTestEnum::MIDDLE))
        ->toThrow(InvalidArgumentException::class);
});

test('transition to method can handle an array of destination states', function (): void {
    $machine = new ClassWithStateMachine;

    $machine->status = StateMachineTestEnum::END;

    $canTransitionToResult = $machine->canTransitionTo('status', [StateMachineTestEnum::START, StateMachineTestEnum::MIDDLE]);
    expect($canTransitionToResult)->toBeFalse();

    $machine->status = StateMachineTestEnum::START;
    $canTransitionToResult = $machine->canTransitionTo('status', [StateMachineTestEnum::MIDDLE, StateMachineTestEnum::END]);
    expect($canTransitionToResult)->toBeTrue();
});

test('can transition to method throws exception for empty array', function (): void {
    $machine = new ClassWithStateMachine;

    expect(fn () => $machine->canTransitionTo('status', []))
        ->toThrow(InvalidArgumentException::class);
});

test('a state cannot transition to self unless explicitly allowed', function (): void {
    $machine = new ClassWithStateMachine;

    expect(fn () => $machine->transitionTo('status', StateMachineTestEnum::START))
        ->toThrow(InvalidStateTransition::class);
});

test('a final state can still transition to self', function (): void {
    $machine = new ClassWithStateMachine;

    $machine->status = StateMachineTestEnum::CANCELLED;

    $machine->transitionTo('status', StateMachineTestEnum::CANCELLED);

    // No exception thrown
    expect($machine->status)->toBe(StateMachineTestEnum::CANCELLED);
});

it('can serialize a state machine configuration', function (): void {
    $serialized = ClassWithStateMachine::serializeStateMachine('status');

    expect($serialized)->toBeArray()
        ->and($serialized)->toHaveKey('status')
        ->and($serialized['status']['Default State'])->toBe('START')
        ->and($serialized['status']['Final States'])->toBe(['CANCELLED'])
        ->and($serialized['status']['Allowed Transitions'])->toHaveKeys(['START', 'MIDDLE', 'END'])
        ->and($serialized['status']['Self Transitions'])->toBe(['CANCELLED']);
});

it('throws when validating a non-enum property', function (): void {
    $machine = new ClassWithStateMachine;

    expect(fn () => $machine->transitionTo('unguarded', 'anything'))
        ->toThrow(InvalidArgumentException::class, 'is not an enum');
});

it('throws when onlyRunInVerbsState is called outside a Verbs State', function (): void {
    expect(fn () => ClassWithStateMachine::onlyRunInVerbsState())
        ->toThrow(Exception::class, 'only be used on Verbs States');
});

it('can use canTransitionFrom on the enum', function (): void {
    expect(StateMachineTestEnum::MIDDLE->canTransitionFrom(StateMachineTestEnum::START))->toBeTrue();
    expect(StateMachineTestEnum::START->canTransitionFrom(StateMachineTestEnum::MIDDLE))->toBeFalse();
});

it('can use transitionTo on the enum directly', function (): void {
    $result = StateMachineTestEnum::START->transitionTo(StateMachineTestEnum::MIDDLE);

    expect($result)->toBe(StateMachineTestEnum::MIDDLE);
});

it('throws InvalidStateTransition on enum transitionTo with invalid transition', function (): void {
    expect(fn () => StateMachineTestEnum::MIDDLE->transitionTo(StateMachineTestEnum::START))
        ->toThrow(InvalidStateTransition::class);
});

it('can use transitionFrom on the enum', function (): void {
    $result = StateMachineTestEnum::MIDDLE->transitionFrom(StateMachineTestEnum::START);

    expect($result)->toBe(StateMachineTestEnum::START);
});

it('throws InvalidStateTransition on enum transitionFrom with invalid transition', function (): void {
    expect(fn () => StateMachineTestEnum::START->transitionFrom(StateMachineTestEnum::MIDDLE))
        ->toThrow(InvalidStateTransition::class);
});

it('throws RuntimeException when source case has no CanTransitionTo attribute', function (): void {
    expect(fn () => NoAttributeStateMachineEnum::Foo->canTransitionTo(NoAttributeStateMachineEnum::Bar))
        ->toThrow(RuntimeException::class, 'does not have the CanTransitionTo attribute');
});

it('can serialize to nova options', function (): void {
    $options = StateMachineTestEnum::toNovaOptions();

    expect($options)->toBe([
        'START' => 'START',
        'MIDDLE' => 'MIDDLE',
        'END' => 'END',
        'CANCELLED' => 'CANCELLED',
    ]);
});

it('throws when property has no type defined', function (): void {
    $machine = new class
    {
        use ArtisanBuild\FatEnums\StateMachine\HasStateMachine;

        public $untyped;
    };

    expect(fn () => $machine->transitionTo('untyped', StateMachineTestEnum::MIDDLE))
        ->toThrow(InvalidArgumentException::class, 'does not have a type defined');
});

it('throws when enum does not implement StateMachine', function (): void {
    $machine = new class
    {
        use ArtisanBuild\FatEnums\StateMachine\HasStateMachine;

        public ArtisanBuild\FatEnums\Tests\Fixtures\StringBackedEnum $status = ArtisanBuild\FatEnums\Tests\Fixtures\StringBackedEnum::Happy;
    };

    expect(fn () => $machine->transitionTo('status', ArtisanBuild\FatEnums\Tests\Fixtures\StringBackedEnum::Sad))
        ->toThrow(ArtisanBuild\FatEnums\StateMachine\InvalidStateMachineConfig::class, 'does not implement');
});

it('throws when enum does not have a DEFAULT constant', function (): void {
    $machine = new class
    {
        use ArtisanBuild\FatEnums\StateMachine\HasStateMachine;

        public NoDefaultTestEnum $status;
    };

    $machine->status = NoDefaultTestEnum::Foo;

    expect(fn () => $machine->transitionTo('status', NoDefaultTestEnum::Foo))
        ->toThrow(ArtisanBuild\FatEnums\StateMachine\InvalidStateMachineConfig::class, 'does not have a DEFAULT');
});

it('throws when property has a union type', function (): void {
    $machine = new class
    {
        use ArtisanBuild\FatEnums\StateMachine\HasStateMachine;

        public StateMachineTestEnum|string $status = 'test';
    };

    expect(fn () => $machine->transitionTo('status', StateMachineTestEnum::MIDDLE))
        ->toThrow(InvalidArgumentException::class, 'cannot be a union or intersection type');
});

enum NoAttributeStateMachineEnum: string implements StateMachine
{
    use IsStateMachine;

    case Foo = 'foo';
    case Bar = 'bar';

    const DEFAULT = self::Foo;
}

enum NoDefaultTestEnum: string implements StateMachine
{
    use IsStateMachine;

    #[CanTransitionTo([self::Bar])]
    case Foo = 'foo';

    case Bar = 'bar';
}
