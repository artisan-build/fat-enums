<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
class WithData
{
    public function __construct(public array $data) {}
}
