<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Tests\Fixtures;

use ArtisanBuild\FatEnums\StateMachine\ModelHasStateMachine;
use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class StateMachineModel extends Model
{
    use ModelHasStateMachine;
    use Sushi;

    protected array $state_machines = ['status'];

    protected $rows = [
        ['id' => 1, 'status' => 'START'],
        ['id' => 2, 'status' => 'MIDDLE'],
    ];

    protected function casts(): array
    {
        return [
            'status' => StateMachineTestEnum::class,
        ];
    }
}
