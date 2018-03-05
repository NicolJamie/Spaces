<?php

namespace NicolJamie\Spaces;

use Illuminate\Support\ServiceProvider;

class SpacesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/spaces.php' => config_path('spaces.php'),
        ], 'spaces');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Detect if AWS is installed
        // Load the main class
    }
}
