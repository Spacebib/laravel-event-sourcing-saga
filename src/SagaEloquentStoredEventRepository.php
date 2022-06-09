<?php
namespace Spacebib\Saga;

use Spatie\EventSourcing\StoredEvents\Repositories\EloquentStoredEventRepository;
use Spatie\EventSourcing\AggregateRoots\Exceptions\InvalidEloquentStoredEventModel;

class SagaEloquentStoredEventRepository extends EloquentStoredEventRepository
{
    public function __construct()
    {
        $this->storedEventModel = (string)config('event-sourcing-saga.saga_stored_event_model', self::class);

        if (! new $this->storedEventModel instanceof EloquentSagaStoredEvent) {
            throw new InvalidEloquentStoredEventModel("The class {$this->storedEventModel} must extend EloquentSagaStoredEvent");
        }
    }
}