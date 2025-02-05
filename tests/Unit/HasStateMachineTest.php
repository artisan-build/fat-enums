<?php

use ArtisanBuild\FatEnums\StateMachine\InvalidStateTransition;
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
