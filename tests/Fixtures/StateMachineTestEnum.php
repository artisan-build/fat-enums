<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Tests\Fixtures;

use ArtisanBuild\FatEnums\StateMachine\CanTransitionTo;
use ArtisanBuild\FatEnums\StateMachine\CanTransitionToSelf;
use ArtisanBuild\FatEnums\StateMachine\FinalState;
use ArtisanBuild\FatEnums\StateMachine\IsStateMachine;
use ArtisanBuild\FatEnums\StateMachine\SerializesForNova;
use ArtisanBuild\FatEnums\StateMachine\StateMachine;

enum StateMachineTestEnum: string implements StateMachine
{
    use IsStateMachine;
    use SerializesForNova;

    const DEFAULT = self::START;

    #[CanTransitionTo([
        self::MIDDLE,
        self::END,
        self::CANCELLED,
    ])]
    case START = 'START';

    #[CanTransitionTo([
        self::END,
        self::CANCELLED,
    ])]
    case MIDDLE = 'MIDDLE';

    #[CanTransitionTo([
        self::CANCELLED,
    ])]
    case END = 'END';

    #[CanTransitionToSelf]
    #[FinalState]
    case CANCELLED = 'CANCELLED';
}
