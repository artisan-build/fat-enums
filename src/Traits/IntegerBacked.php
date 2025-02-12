<?php

declare(strict_types=1);

namespace ArtisanBuild\FatEnums\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Number;

trait IntegerBacked
{
    public function eq(): Enumerable|Collection
    {
        return collect(self::cases())->filter(fn ($case) => $case->value === $this->value);
    }

    public function neq(): Enumerable|Collection
    {
        return collect(self::cases())->filter(fn ($case) => $case->value !== $this->value);
    }

    public function gt(): Enumerable|Collection
    {
        return collect(self::cases())->filter(fn ($case) => $case->value > $this->value);
    }

    public function gte(): Enumerable|Collection
    {
        return collect(self::cases())->filter(fn ($case) => $case->value >= $this->value);
    }

    public function lt(): Enumerable|Collection
    {
        return collect(self::cases())->filter(fn ($case) => $case->value < $this->value);
    }

    public function lte(): Enumerable|Collection
    {
        return collect(self::cases())->filter(fn ($case) => $case->value <= $this->value);
    }

    public function ordinal(): string
    {
        return Number::ordinal($this->value);
    }
}
