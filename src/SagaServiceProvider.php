<?php
namespace Spacebib\Saga;

use Illuminate\Support\ServiceProvider;

class SagaServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/event-sourcing-saga.php' => config_path('event-sourcing-saga.php'),
            ], 'config');
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/event-sourcing-saga.php', 'event-sourcing-saga');
    }
}
