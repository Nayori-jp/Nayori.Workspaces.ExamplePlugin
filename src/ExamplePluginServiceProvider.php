<?php

namespace Plugins\ExamplePlugin;

use App\Models\Business;
use App\Models\User;
use App\Support\Crud\CrudResourceRegistry;
use Illuminate\Support\Facades\Gate;
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
        Gate::define('view-example-plugin', static function (User $user, Business $business): bool {
            return $user->businesses()->whereKey($business->id)->exists();
        });

        Gate::define('manage-example-plugin', static function (User $user, Business $business): bool {
            return $user->businesses()
                ->whereKey($business->id)
                ->wherePivotIn('role', ['owner', 'admin'])
                ->exists();
        });

        Route::middleware('web')->group(__DIR__.'/routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }
}
