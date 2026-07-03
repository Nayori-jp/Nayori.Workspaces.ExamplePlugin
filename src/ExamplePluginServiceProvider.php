<?php

namespace Plugins\ExamplePlugin;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ExamplePluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $module = require __DIR__.'/config/module.php';

        config()->set('modules.addons.example-plugin', array_merge(
            config('modules.addons.example-plugin', []),
            $module,
        ));
    }

    public function boot(): void
    {
        Route::middleware('web')->group(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'example-plugin');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }
}
