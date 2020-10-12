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
                __DIR__.'/../config/config.php' => config_path('laravel-log-usage.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-log-usage');
        // Add middleware
        $kernel =$this->app->make(Kernel::class);

        $kernel->pushMiddleware(LogUsageMiddleware::class);
        // Register the main class to use with the facade
        $this->app->singleton('laravel-log-usage', function () {
            return new LaravelLogUsage;
        });
    }
}
