<?php

namespace Spacebib\Saga;

use Spacebib\Saga\Enum\SagaInternalState;
use Spacebib\Saga\Events\SagaCompleted;
use Spacebib\Saga\Events\SagaRolledBack;
use Spacebib\Saga\Events\SagaRunning;
use Spacebib\Saga\Events\SagaStoredEventFailedToProcess;
use Spacebib\Saga\Events\SagaStoredEventProcessed;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;
use Spatie\EventSourcing\StoredEvents\StoredEvent;

class AggregateSaga extends AggregateRoot
{
    const SAGE_UUID_META_KEY = 'saga_uuid';

    protected SagaInternalState $internalState;

    protected array $processedStoredEventIds = [];

    protected array $historyEventIds = [];

    protected int $tries = 1;

    private int $failedCount = 0;

    protected string $sagaName;

    public function setTries(int $tris): self
    {
        $this->tries = $tris;
        return $this;
    }

    /**
     * @return array
     */
    public function getProcessedStoredEventIds(): array
    {
        return $this->processedStoredEventIds;
    }

    public function onStart(ShouldBeStored $domainEvent, string $aggregateRootUuid): self
    {
        if ($this->isDuplicateEvent($domainEvent->storedEventId())) {
            return $this;
        }

        if (! $this->getInternalState()->equals(SagaInternalState::NOT_STARTED())) {
            return $this;
        }

        $this->markAsRunning($domainEvent);

        $this->onDomainEvent($domainEvent, $aggregateRootUuid);

        return $this;
    }

    public function onRunning(ShouldBeStored $domainEvent, string $aggregateRootUuid): self
    {
        if (static::class !== $this->sagaName) {
            return $this;
        }

        if ($this->isDuplicateEvent($domainEvent->storedEventId())) {
            dd('aa');
            return $this;
        }

        if (! $this->getInternalState()->equals(SagaInternalState::RUNNING())) {
            return $this;
        }

        $this->onDomainEvent($domainEvent, $aggregateRootUuid);

        return $this;
    }

    public function onComplete(ShouldBeStored $domainEvent, string $aggregateRootUuid): self
    {
        if (static::class !== $this->sagaName) {
            return $this;
        }
        if ($this->isDuplicateEvent($domainEvent->storedEventId())) {
            return $this;
        }

        if (! $this->getInternalState()->equals(SagaInternalState::RUNNING())) {
            return $this;
        }

        $this->onDomainEvent($domainEvent, $aggregateRootUuid);

        $this->markAsCompleted($domainEvent, $aggregateRootUuid);

        return $this;
    }

    protected function persistForSaga(AggregateRoot $aggregateRoot): self
    {
        $storedEvents = $aggregateRoot->persistWithoutApplyingToEventHandlers();

        $storedEvents = $storedEvents->map(function (StoredEvent $storedEvent) {
            $metaData = $storedEvent->event->metaData();
            $metaData[self::SAGE_UUID_META_KEY] = $this->uuid();
            $event  = $storedEvent->event->setMetaData($metaData);
            $storedEvent->event = $event;
            $storedEvent->meta_data = $metaData;
            parent::getStoredEventRepository()->update($storedEvent);
            return $storedEvent;
        });

        $storedEvents->each(fn (StoredEvent $storedEvent) => $storedEvent->handleForAggregateRoot());

        $this->aggregateVersionAfterReconstitution = $this->aggregateVersion;

        return $this;
    }

    protected function getInternalState(): SagaInternalState
    {
        if (! isset($this->internalState)) {
            return SagaInternalState::NOT_STARTED();
        }

        return $this->internalState;
    }

    private function onDomainEventRollback(StoredEvent $storedEvent, string $aggregateRootUuid)
    {
        $domainEvent = $storedEvent->event;

        $eventShortName = $this->getShortName($domainEvent);

        $method = sprintf('on%sRollback', $eventShortName);

        if (false === method_exists($this, $method)) {
            return;
        }

        $this->$method($domainEvent);
        $this->recordThat(
            new SagaRolledBack(
                $storedEvent->id,
                $eventShortName,
                $method,
                $domainEvent->aggregateRootUuid(),
                static::class
            )
        )->persist();
    }

    private function onDomainEvent(ShouldBeStored $domainEvent, string $aggregateRootUuid)
    {
        $eventShortName = $this->getShortName($domainEvent);
        $method = sprintf('on%s', $eventShortName);

        if (false === method_exists($this, $method)) {
            throw new \BadMethodCallException(
                sprintf(
                    'Method %s does not exist on class %s',
                    $method,
                    static::class
                )
            );
        }

        try {
            $this->$method($domainEvent);
            $this->acknowledgeStoredEvent($domainEvent->storedEventId(), $eventShortName, $domainEvent->aggregateRootUuid());
        } catch (\Exception $exception) {
            $this->recordThat(
                new SagaStoredEventFailedToProcess(
                    $domainEvent->storedEventId(),
                    $eventShortName,
                    $domainEvent->aggregateRootUuid(),
                    static::class
                )
            )->persist();

            $this->rollbackIfNeeded();

            throw $exception;
        }
    }

    private function acknowledgeStoredEvent(int $id, string $eventName, string $aggregateUuid)
    {
        $this::retrieve($this->uuid())
            ->recordThat(
                new SagaStoredEventProcessed($id, $eventName, $aggregateUuid, static::class)
            )
            ->persist();
    }

    private function isDuplicateEvent(int $id): bool
    {
        return \in_array($id, $this->processedStoredEventIds);
    }

    protected function applySagaStoredEventProcessed(SagaStoredEventProcessed $storedEventProcessed)
    {
        $this->sagaName = $storedEventProcessed->sagaName;
        $this->historyEventIds[] = $this->processedStoredEventIds[] = $storedEventProcessed->id;
    }

    protected function applySagaStoredEventFailedToProcess(SagaStoredEventFailedToProcess $storedEventFailedToProcess)
    {
        $this->sagaName = $storedEventFailedToProcess->sagaName;
        $this->historyEventIds[] = $storedEventFailedToProcess->id;
        $this->failedCount++;
    }

    protected function applySagaRunning(SagaRunning $running)
    {
        $this->sagaName = $running->sagaName;
        $this->internalState = SagaInternalState::RUNNING();
    }

    protected function applySagaCompleted(SagaCompleted $completed)
    {
        $this->sagaName = $completed->sagaName;
        $this->internalState = SagaInternalState::COMPLETED();
    }

    private function rollbackIfNeeded()
    {
        if ($this->failedCount < $this->tries) {
            return;
        }
        collect($this->processedStoredEventIds)
            ->unique()
            ->reverse()
            ->map(fn (int $id) => parent::getStoredEventRepository()->find($id))
            ->each(fn (StoredEvent $storedEvent) => $this->onDomainEventRollback($storedEvent, $storedEvent->aggregate_uuid));
    }

    protected function getStoredEventRepository(): SagaEloquentStoredEventRepository
    {
        return app(config('event-sourcing-saga.saga_stored_event_repository') ?? SagaEloquentStoredEventRepository::class);
    }

    /**
     * @param  ShouldBeStored  $domainEvent
     * @return string
     */
    private function getShortName(ShouldBeStored $domainEvent): string
    {
        return (new \ReflectionClass($domainEvent))->getShortName();
    }

    /**
     * @param  ShouldBeStored  $domainEvent
     * @param  string  $aggregateUuid
     */
    private function markAsCompleted(ShouldBeStored $domainEvent, string $aggregateUuid): void
    {
        $this::retrieve($this->uuid())
            ->recordThat(
                new SagaCompleted(
                    $domainEvent->storedEventId(),
                    $this->getShortName($domainEvent),
                    $domainEvent->aggregateRootUuid(),
                    static::class
                )
            )
            ->persist();
    }

    /**
     * @param  ShouldBeStored  $domainEvent
     */
    private function markAsRunning(ShouldBeStored $domainEvent): void
    {
        $this
            ::retrieve($this->uuid())
            ->recordThat(
                new SagaRunning($this->getShortName($domainEvent), $domainEvent->aggregateRootUuid(), static::class)
            )
            ->persist();
    }
}
