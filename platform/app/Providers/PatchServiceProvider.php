<?php

namespace App\Providers;

use App\Console\Commands\PatchApply;
use App\Console\Commands\PatchCheck;
use App\Console\Commands\PatchRollback;
use App\Services\Patch\PatchManager;
use Illuminate\Support\ServiceProvider;

class PatchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(PatchManager::class, function () {
            return new PatchManager();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                PatchApply::class,
                PatchCheck::class,
                PatchRollback::class,
            ]);
        }
    }
}
