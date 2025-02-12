<?php

namespace ArtisanBuild\FatEnums\Providers;

use Illuminate\Support\ServiceProvider;

class FatEnumsServiceProvider extends ServiceProvider
{
    #[\Override]
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/fat-enums.php', 'fat-enums');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/fat-enums.php' => config_path('fat-enums.php'),
        ], 'fat-enums');
    }
}
