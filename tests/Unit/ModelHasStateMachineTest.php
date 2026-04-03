<?php

declare(strict_types=1);

use ArtisanBuild\FatEnums\StateMachine\InvalidStateTransition;
use ArtisanBuild\FatEnums\Tests\Fixtures\StateMachineModel;
use ArtisanBuild\FatEnums\Tests\Fixtures\StateMachineModelBadEnum;
use ArtisanBuild\FatEnums\Tests\Fixtures\StateMachineModelNoCast;
use ArtisanBuild\FatEnums\Tests\Fixtures\StateMachineModelStringCast;
use ArtisanBuild\FatEnums\Tests\Fixtures\StateMachineTestEnum;

beforeEach(function (): void {
    StateMachineModel::find(1)->forceFill(['status' => 'START'])->saveQuietly();
    StateMachineModel::find(2)->forceFill(['status' => 'MIDDLE'])->saveQuietly();
});

it('allows a valid transition on model update', function (): void {
    $model = StateMachineModel::find(1);

    $model->status = StateMachineTestEnum::MIDDLE;
    $model->save();

    expect($model->fresh()->status)->toBe(StateMachineTestEnum::MIDDLE);
});

it('throws on an invalid transition on model update', function (): void {
    $model = StateMachineModel::find(2);

    $model->status = StateMachineTestEnum::START;

    expect(fn () => $model->save())
        ->toThrow(InvalidStateTransition::class);
});

it('allows transitioning through multiple valid states', function (): void {
    $model = StateMachineModel::find(1);

    $model->status = StateMachineTestEnum::MIDDLE;
    $model->save();

    $model = $model->fresh();
    $model->status = StateMachineTestEnum::END;
    $model->save();

    expect($model->fresh()->status)->toBe(StateMachineTestEnum::END);
});

it('allows a final state to transition to self on model', function (): void {
    $model = StateMachineModel::find(1);

    $model->status = StateMachineTestEnum::CANCELLED;
    $model->save();

    $model = $model->fresh();
    $model->status = StateMachineTestEnum::CANCELLED;
    $model->save();

    expect($model->fresh()->status)->toBe(StateMachineTestEnum::CANCELLED);
});

it('throws when model has no state_machines property', function (): void {
    expect(fn () => new class extends Illuminate\Database\Eloquent\Model
    {
        use ArtisanBuild\FatEnums\StateMachine\ModelHasStateMachine;
    })->toThrow(Exception::class, 'define a $state_machines array property');
});

it('throws when state_machines property is not an array', function (): void {
    expect(fn () => new class extends Illuminate\Database\Eloquent\Model
    {
        use ArtisanBuild\FatEnums\StateMachine\ModelHasStateMachine;

        protected string $state_machines = 'not_an_array';
    })->toThrow(InvalidArgumentException::class, 'must be an array');
});

it('throws when state machine attribute is not in casts', function (): void {
    $model = StateMachineModelNoCast::find(1);

    $model->status = 'MIDDLE';

    expect(fn () => $model->save())
        ->toThrow(InvalidArgumentException::class, 'is not a valid cast');
});

it('throws when state machine cast is not an enum', function (): void {
    $model = StateMachineModelStringCast::find(1);

    $model->status = 'MIDDLE';

    expect(fn () => $model->save())
        ->toThrow(InvalidArgumentException::class, 'is not a valid enum');
});

it('throws when state machine cast does not implement StateMachine', function (): void {
    $model = StateMachineModelBadEnum::find(1);

    $model->status = ArtisanBuild\FatEnums\Tests\Fixtures\StringBackedEnum::Sad;

    expect(fn () => $model->save())
        ->toThrow(InvalidArgumentException::class, 'does not implement');
});
