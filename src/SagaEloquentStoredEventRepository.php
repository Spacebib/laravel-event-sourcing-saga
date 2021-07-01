<?php

namespace Spacebib\Saga;

use Spatie\EventSourcing\StoredEvents\Repositories\EloquentStoredEventRepository;
use Spatie\EventSourcing\StoredEvents\StoredEvent;
use Spatie\EventSourcing\Exceptions\InvalidEloquentStoredEventModel;

class SagaEloquentStoredEventRepository extends EloquentStoredEventRepository
{
    public function __construct()
    {
        $this->storedEventModel = (string)config('event-sourcing-saga.saga_stored_event_model', self::class);

        if (! new $this->storedEventModel instanceof EloquentSagaStoredEvent) {
            throw new InvalidEloquentStoredEventModel("The class {$this->storedEventModel} must extend EloquentSagaStoredEvent");
        }
    }

    public function getEloquentStoredEventById($id): StoredEvent
    {
        /** @var \Illuminate\Database\Query\Builder $query */
        $query = app(config('event-sourcing.stored_event_repository'))->storedEventModel::query();
        return $query->where('id', $id)->firstOrFail()->toStoredEvent();
    }
}
