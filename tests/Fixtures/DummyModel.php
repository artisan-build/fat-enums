<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Tests\Fixtures;

use ArtisanBuild\FatEnums\StateMachine\ModelHasStateMachine;
use Illuminate\Database\Eloquent\Model;

class DummyModel extends Model
{
    use ModelHasStateMachine;
    use \Sushi\Sushi;

    protected array $state_machines = [];

    protected $rows = [
        ['id' => 1111, 'name' => 'Foo'],
        ['id' => 2222, 'name' => 'Bar'],
    ];
}
