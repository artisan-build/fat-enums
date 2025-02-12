<?php

namespace ArtisanBuild\FatEnums\StateMachine;

trait SerializesForNova
{
    public static function toNovaOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->value])
            ->toArray();
    }
}
