<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Tests\Fixtures;

use ArtisanBuild\FatEnums\StateMachine\HasStateMachine;

class ClassWithStateMachine
{
    use HasStateMachine;

    public StateMachineTestEnum $status = StateMachineTestEnum::DEFAULT;

    public string $unguarded = 'AN UNRELATED PROPERTY';
}
