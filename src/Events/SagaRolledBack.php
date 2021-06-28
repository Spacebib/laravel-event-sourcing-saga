<?php

namespace Spacebib\Saga\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class SagaRolledBack extends ShouldBeStored
{
    public int $id;

    public string $eventName;

    public string $method;

    public string $aggregateUuid;

    public string $sagaName;

    /**
     * SagaRolledBack constructor.
     * @param  int  $id
     * @param  string  $eventName
     * @param  string  $method
     * @param  string  $aggregateUuid
     * @param  string  $sagaName
     */
    public function __construct(
        int $id,
        string $eventName,
        string $method,
        string $aggregateUuid,
        string $sagaName
    ) {
        $this->id = $id;
        $this->eventName = $eventName;
        $this->method = $method;
        $this->aggregateUuid = $aggregateUuid;
        $this->sagaName = $sagaName;
    }
}
