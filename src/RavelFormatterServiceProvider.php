<?php

namespace MicroPhpLibs\RavelFormatter;

use Illuminate\Support\ServiceProvider;

class RavelFormatterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // php artisan vendor:publish --tag=ravel-formatter
        $this->publishes([
            __DIR__.'/ravel-formatter.php' => config_path('ravel-formatter.php')
        ], 'ravel-formatter');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__.'/ravel-formatter.php', 'ravel-formatter.php'
        );
    }
}
