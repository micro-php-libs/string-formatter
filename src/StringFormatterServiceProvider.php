<?php

namespace MicroPhpLibs\StringFormatter;

use Illuminate\Support\ServiceProvider;

class StringFormatterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // php artisan vendor:publish --tag=string-formatter
        $this->publishes([
            __DIR__.'/string-formatter.php' => config_path('string-formatter.php')
        ], 'string-formatter');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__.'/string-formatter.php', 'string-formatter.php'
        );
    }
}
