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
     * @throws \Exception
     */
    public function register()
    {
        if (!class_exists('Aws\S3\S3Client')) {
            throw new  \Exception('AWS SDK is not found, please require');
        }
    }
}
