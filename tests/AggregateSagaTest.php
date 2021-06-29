<?php

namespace Spacebib\Saga\Tests;

use Spacebib\Saga\AggregateSaga;
use Spacebib\Saga\Events\SagaRolledBack;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Ramsey\Uuid\Uuid;
use Spatie\EventSourcing\Enums\MetaData;
use Spatie\EventSourcing\Facades\Projectionist;
use Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEvent;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;
use Spatie\EventSourcing\StoredEvents\StoredEvent;
use Spacebib\Saga\Tests\Emails\FailedSagaMail;
use Spacebib\Saga\Tests\Events\SagaEventStepOne;
use Spacebib\Saga\Tests\Events\SagaEventStepThree;
use Spacebib\Saga\Tests\Events\SagaEventStepTwo;
use Spacebib\Saga\Tests\Sagas\TestSagaWithExceptionSaga;
use Spacebib\Saga\Tests\Sagas\TestSagaWithExceptionSagaReactor;

class AggregateSagaTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_rollback()
    {
        // arrange
        Mail::fake();
        Projectionist::withoutEventHandlers();

        $aggregateUuid = Uuid::uuid4();
        $sagaUuid = Uuid::uuid4();

        $saga = TestSagaWithExceptionSaga::retrieve($sagaUuid);
        $eventOne = new SagaEventStepOne();
        $eventOne->setAggregateRootUuid($aggregateUuid);
        $storedEvent = $this->createStoredEvent($eventOne);
        $saga->onStart($storedEvent, $eventOne);

        $saga = TestSagaWithExceptionSaga::retrieve($sagaUuid);
        $eventTwo = new SagaEventStepTwo();
        $eventTwo->setAggregateRootUuid($aggregateUuid);
        $storedEvent = $this->createStoredEvent($eventTwo, [AggregateSaga::SAGE_UUID_META_KEY => $sagaUuid]);

        $saga->onRunning($storedEvent, $eventTwo);

        // act
        Projectionist::addReactor(TestSagaWithExceptionSagaReactor::class);

        $saga = TestSagaWithExceptionSaga::retrieve($sagaUuid);
        $saga->setTries(1);
        $eventThree = new SagaEventStepThree();
        $eventThree->setAggregateRootUuid($aggregateUuid);
        $storedEvent = $this->createStoredEvent($eventThree, [AggregateSaga::SAGE_UUID_META_KEY => $sagaUuid]);

        try {
            $saga->onComplete($storedEvent, $eventThree);
        } catch (\Exception $exception) {
//            var_dump($exception->getMessage());
        } finally {
            // assert
            Mail::assertQueued(FailedSagaMail::class, 2);

            $this->assertContains(
                SagaRolledBack::class,
                array_map('get_class', TestSagaWithExceptionSaga::retrieve($sagaUuid)->getAppliedEvents())
            );
        }
    }

    public function test_it_tries()
    {
        // arrange
        Mail::fake();
        Projectionist::withoutEventHandlers();

        $aggregateUuid = Uuid::uuid4();
        $sagaUuid = Uuid::uuid4();

        $saga = TestSagaWithExceptionSaga::retrieve($sagaUuid);
        $eventOne = new SagaEventStepOne();
        $eventOne->setAggregateRootUuid($aggregateUuid);
        $storedEvent = $this->createStoredEvent($eventOne);

        $saga->onStart($storedEvent, $eventOne);

        $saga = TestSagaWithExceptionSaga::retrieve($sagaUuid);
        $eventTwo = new SagaEventStepTwo();
        $eventTwo->setAggregateRootUuid($aggregateUuid);
        $storedEvent = $this->createStoredEvent($eventTwo, [AggregateSaga::SAGE_UUID_META_KEY => $sagaUuid]);
        $saga->onRunning($storedEvent, $eventTwo);

        // act
        Projectionist::addReactor(TestSagaWithExceptionSagaReactor::class);

        $saga = TestSagaWithExceptionSaga::retrieve($sagaUuid);
        $saga->setTries(3);
        $eventThree = new SagaEventStepThree();
        $eventThree->setAggregateRootUuid($aggregateUuid);
        $storedEvent = $this->createStoredEvent($eventThree, [AggregateSaga::SAGE_UUID_META_KEY => $sagaUuid]);

        try {
            $saga->onComplete($storedEvent, $eventThree);
        } catch (\Exception $exception) {
//            var_dump($exception->getMessage());
        } finally {
            // assert
            Mail::assertNothingQueued();
        }
    }

    private function createStoredEvent(ShouldBeStored $eventOne, array $metaData = []): StoredEvent
    {
        $uuid = Uuid::uuid4();
        $eloquentStoredEvent = new EloquentStoredEvent();
        $eloquentStoredEvent->event_properties = [];
        $eloquentStoredEvent->meta_data = array_merge([MetaData::AGGREGATE_ROOT_UUID => $uuid], $metaData);
        $eloquentStoredEvent->aggregate_uuid = $uuid;
        $eloquentStoredEvent->aggregate_version = 1;
        $eloquentStoredEvent->event_class = \get_class($eventOne);
        $eloquentStoredEvent->created_at = Carbon::now();
        $eloquentStoredEvent->saveOrFail();
        $eloquentStoredEvent->setOriginalEvent($eventOne);
        $eloquentStoredEvent->saveOrFail();
        return $eloquentStoredEvent->toStoredEvent();
    }
}
