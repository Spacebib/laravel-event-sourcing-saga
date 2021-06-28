<?php

namespace Spacebib\Saga\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class SagaCompleted extends ShouldBeStored
{
    public int $id;

    public string $eventName;

    public string $aggregateUuid;

    public string $sagaName;

    /**
     * SagaCompleted constructor.
     * @param  int  $id
     * @param  string  $eventName
     * @param  string  $aggregateUuid
     * @param  string  $sagaName
     */
    public function __construct(int $id, string $eventName, string $aggregateUuid, string $sagaName)
    {
        $this->id = $id;
        $this->eventName = $eventName;
        $this->aggregateUuid = $aggregateUuid;
        $this->sagaName = $sagaName;
    }
}
