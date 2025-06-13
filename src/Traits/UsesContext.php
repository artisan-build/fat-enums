<?php

namespace ArtisanBuild\FatEnums\Traits;

trait UsesContext
{
    public function push(mixed $value): void
    {
        Context::push($this->value, $value);
    }

    public function unshift(mixed $value): void
    {
        $context = Context::get($this->value);
        array_unshift($context, $value);
        Context::add($this->value, $context);
    }

    public function get(): mixed
    {
        return Context::get($this->value, []);
    }
}
