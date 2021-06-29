<?php

namespace Spacebib\Saga\Tests;
use Orchestra\Testbench\TestCase as Orchestra;
use Spacebib\Saga\SagaServiceProvider;
use Spatie\EventSourcing\EventSourcingServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            EventSourcingServiceProvider::class,
            SagaServiceProvider::class
        ];
    }

}
