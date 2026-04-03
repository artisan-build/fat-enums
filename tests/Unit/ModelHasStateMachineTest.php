<?php

declare(strict_types=1);

use ArtisanBuild\FatEnums\StateMachine\InvalidStateTransition;
use ArtisanBuild\FatEnums\Tests\Fixtures\StateMachineModel;
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
