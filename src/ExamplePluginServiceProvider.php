<?php

namespace Plugins\ExamplePlugin;

use App\Support\Crud\CrudResourceRegistry;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Plugins\ExamplePlugin\Support\ExampleRecordsCrudResourceDefinition;

class ExamplePluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $module = require __DIR__.'/config/module.php';

        config()->set('modules.addons.example-plugin', array_merge(
            config('modules.addons.example-plugin', []),
            $module,
        ));

        $this->app->afterResolving(CrudResourceRegistry::class, function (CrudResourceRegistry $registry): void {
            $registry->register($this->app->make(ExampleRecordsCrudResourceDefinition::class));
        });
    }

    public function boot(): void
    {
        Route::middleware('web')->group(__DIR__.'/routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }
}
