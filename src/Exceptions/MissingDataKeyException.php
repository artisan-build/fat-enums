<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Exceptions;

use Exception;

class MissingDataKeyException extends Exception
{
    public function __construct(string $enum, string $case, string $key)
    {
        parent::__construct("Missing key {$key} in HasData attribute for enum case {$enum}::{$case}.");
    }
}
