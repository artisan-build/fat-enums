<?php

namespace ArtisanBuild\FatEnums\StateMachine;

use RuntimeException;

class InvalidStateTransition extends RuntimeException
{
    /**
     * @example sprintf(InvalidStateTransition::MESSAGE, $from->value, $to->value, $class)
     */
    const MESSAGE = 'Transition from `%s` to `%s` on `%s::%s` was not found, did you forget to register it in `%s`?';

    public function __construct($message = 'Invalid state transition')
    {
        parent::__construct($message);
    }
}
