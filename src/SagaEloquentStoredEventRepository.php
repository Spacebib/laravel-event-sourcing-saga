<?php

namespace Spacebib\Saga;

use Spatie\EventSourcing\StoredEvents\Repositories\EloquentStoredEventRepository;
use Spatie\EventSourcing\StoredEvents\StoredEvent;

class SagaEloquentStoredEventRepository extends EloquentStoredEventRepository
{
    public function getById($id): StoredEvent
    {
        /** @var \Illuminate\Database\Query\Builder $query */
        $query = $this->storedEventModel::query();

        return $query->where('id', $id)->firstOrFail()->toStoredEvent();
    }
}
