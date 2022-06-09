<?php

namespace Spacebib\Saga\Tests;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use Spacebib\Saga\SagaServiceProvider;
use Spatie\EventSourcing\EventSourcingServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDatabase();
    }

    protected function getPackageProviders($app)
    {
        return [
            EventSourcingServiceProvider::class,
            SagaServiceProvider::class
        ];
    }

    protected function setUpDatabase()
    {
        Schema::dropIfExists('stored_events');
        $createStoredEventsTable = require __DIR__.'/../stubs/create_stored_events_table.php.stub';
        $createStoredEventsTable->up();

        Schema::dropIfExists('snapshots');
        $createSnapshotsTable = require __DIR__.'/../stubs/create_snapshots_table.php.stub';
        $createSnapshotsTable->up();

        Schema::dropIfExists('saga_stored_events');
        $createSagaStoredEventsTable = require __DIR__.'/../stubs/create_saga_stored_events_table.php.stub';
        $createSagaStoredEventsTable->up();
    }

}
