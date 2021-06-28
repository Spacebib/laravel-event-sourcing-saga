<?php

namespace Spacebib\Saga\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class SagaRunning extends ShouldBeStored
{
    public string $triggerEventName;

    public string $aggregateUuid;

    public string $sagaName;

    /**
     * SagaRunning constructor.
     * @param  string  $triggerEventName
     * @param  string  $aggregateUuid
     * @param  string  $sagaName
     */
    public function __construct(string $triggerEventName, string $aggregateUuid, string $sagaName)
    {
        $this->triggerEventName = $triggerEventName;
        $this->aggregateUuid = $aggregateUuid;
        $this->sagaName = $sagaName;
    }
}
