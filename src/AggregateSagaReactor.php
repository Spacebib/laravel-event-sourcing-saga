<?php

namespace Spacebib\Saga;

use Illuminate\Contracts\Queue\ShouldQueue;
use Ramsey\Uuid\Uuid;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;
use Spatie\EventSourcing\StoredEvents\StoredEvent;

class AggregateSagaReactor extends Reactor implements ShouldQueue
{
    private string $sagaUUid;

    public function getSagaUUid(): string
    {
        return $this->sagaUUid;
    }

    public function withSaga(): string
    {
        return '';
    }

    public function withTries(): int
    {
        if ($this instanceof ShouldQueue) {
            return config('event-sourcing-saga.rollback_tries');
        }
        return  1;
    }

    private function resolveSaga(): AggregateSaga
    {
        if (empty($this->withSaga())) {
            throw new \RuntimeException(
                sprintf(
                    'Please specify a saga in method %s::withSaga()',
                    static::class
                )
            );
        }

        return app()->get($this->withSaga());
    }

    public function resolveSagaUuid()
    {
        $this->sagaUUid = Uuid::uuid4();
    }

    public function onStart(StoredEvent $storedEvent, ShouldBeStored $event)
    {
        $this->resolveSagaUuid();

        $this
            ->resolveSaga()::retrieve($this->getSagaUUid())
            ->setTries($this->withTries())
            ->onStart($storedEvent, $event);
    }

    public function onRunning(StoredEvent $storedEvent, ShouldBeStored $event)
    {
        if (! isset($storedEvent->meta_data[AggregateSaga::SAGE_UUID_META_KEY])) {
            return;
        }

        $this
            ->resolveSaga()::retrieve($storedEvent->meta_data[AggregateSaga::SAGE_UUID_META_KEY])
            ->setTries($this->withTries())
            ->onRunning($storedEvent, $event);
    }

    public function onComplete(StoredEvent $storedEvent, ShouldBeStored $event)
    {
        if (! isset($storedEvent->meta_data[AggregateSaga::SAGE_UUID_META_KEY])) {
            return;
        }

        $this
            ->resolveSaga()::retrieve($storedEvent->meta_data[AggregateSaga::SAGE_UUID_META_KEY])
            ->setTries($this->withTries())
            ->onComplete($storedEvent, $event);
    }
}
