<?php

namespace MapIr\LaravelLogUsage;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use MapIr\LaravelLogUsage\Http\Middleware\LogUsageMiddleware;

class LaravelLogUsageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('logUsage.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'logUsage');
        // Add middleware
        $kernel =$this->app->make(Kernel::class);
        $kernel->pushMiddleware(LogUsageMiddleware::class);
    }
}
