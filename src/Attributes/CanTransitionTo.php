<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Attributes;

use Attribute;
use BackedEnum;

#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
class CanTransitionTo
{
    /**
     * @param array<BackedEnum> $destinations
     */
    public function __construct(
        public array $destinations,
    ) {
    }
}
