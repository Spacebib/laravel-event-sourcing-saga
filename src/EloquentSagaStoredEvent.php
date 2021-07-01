<?php
namespace Spacebib\Saga;

use Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEvent;

class EloquentSagaStoredEvent extends EloquentStoredEvent
{
    protected $table = 'saga_stored_events';
}
