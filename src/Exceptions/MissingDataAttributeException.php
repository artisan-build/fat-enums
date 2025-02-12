<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Exceptions;

use Exception;

class MissingDataAttributeException extends Exception
{
    public function __construct(string $enum, string $case)
    {
        parent::__construct("Missing WithData attribute for enum case {$enum}::{$case}.");
    }
}
