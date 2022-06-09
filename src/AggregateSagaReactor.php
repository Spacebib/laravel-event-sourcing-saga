<?php

namespace Spacebib\Saga;

use Illuminate\Contracts\Queue\ShouldQueue;
use Ramsey\Uuid\Uuid;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;
use Spatie\EventSourcing\StoredEvents\StoredEvent;

class AggregateSagaReactor extends Reactor implements ShouldQueue
{
    protected array $handlesEvents = [];

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
            return config('event-sourcing-saga.rollback_tries', 3);
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

    public function onStart(ShouldBeStored $event)
    {
        $this->resolveSagaUuid();

        $this
            ->resolveSaga()::retrieve($this->getSagaUUid())
            ->setTries($this->withTries())
            ->onStart($event, $event->aggregateRootUuid());
    }

    public function onRunning(ShouldBeStored $event)
    {
        if (! isset($event->metaData()[AggregateSaga::SAGE_UUID_META_KEY])) {
            return;
        }

        $this
            ->resolveSaga()::retrieve($event->metaData()[AggregateSaga::SAGE_UUID_META_KEY])
            ->setTries($this->withTries())
            ->onRunning($event, $event->aggregateRootUuid());
    }

    public function onComplete(ShouldBeStored $event)
    {
        if (! isset($event->metaData()[AggregateSaga::SAGE_UUID_META_KEY])) {
            return;
        }

        $this
            ->resolveSaga()::retrieve($event->metaData()[AggregateSaga::SAGE_UUID_META_KEY])
            ->setTries($this->withTries())
            ->onComplete($event, $event->aggregateRootUuid());
    }

    public function handle(StoredEvent $storedEvent): void
    {
        $handlerMethod = collect($this->handlesEvents ?? [])
            ->mapWithKeys(function (string $handlerMethod, $eventClass) {
                if (is_numeric($eventClass)) {
                    return [$handlerMethod => 'on'.ucfirst(class_basename($handlerMethod))];
                }

                return [$eventClass => $handlerMethod];
            })->get($storedEvent->event_class);

        if ($handlerMethod && method_exists($this, $handlerMethod)) {
            $this->$handlerMethod($storedEvent->event);
        }
    }
}
