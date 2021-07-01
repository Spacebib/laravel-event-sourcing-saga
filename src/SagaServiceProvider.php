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

        if (! class_exists('CreateSagaStoredEventsTable')) {
            $this->publishes([
                __DIR__.'/../stubs/create_saga_stored_events_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_saga_stored_events_table.php'),
            ], 'migrations');
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/event-sourcing-saga.php', 'event-sourcing-saga');
    }
}
