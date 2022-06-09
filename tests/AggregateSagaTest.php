<?php

namespace Spacebib\Saga\Tests;

use Spacebib\Saga\EloquentSagaStoredEvent;
use Spacebib\Saga\Events\SagaCompleted;
use Spacebib\Saga\Events\SagaRolledBack;
use Illuminate\Support\Facades\Mail;
use Ramsey\Uuid\Uuid;
use Spacebib\Saga\Events\SagaRunning;
use Spacebib\Saga\Events\SagaStoredEventProcessed;
use Spacebib\Saga\Tests\AggregateRoots\AggregateRootA;
use Spacebib\Saga\Tests\Events\SagaEventStepEight;
use Spacebib\Saga\Tests\Events\SagaEventStepFive;
use Spacebib\Saga\Tests\Events\SagaEventStepFourA;
use Spacebib\Saga\Tests\Events\SagaEventStepSix;
use Spacebib\Saga\Tests\Sagas\TestSagaWithExceptionSagaAReactor;
use Spatie\EventSourcing\Facades\Projectionist;
use Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEvent;
use Spatie\EventSourcing\StoredEvents\Repositories\EloquentStoredEventRepository;
use Spacebib\Saga\Tests\Emails\FailedSagaMail;
use Spacebib\Saga\Tests\Events\SagaEventStepOne;
use Spacebib\Saga\Tests\Events\SagaEventStepThree;
use Spacebib\Saga\Tests\Events\SagaEventStepTwo;
use Spacebib\Saga\Tests\Sagas\TestSagaWithExceptionSaga;
use Spacebib\Saga\Tests\Sagas\TestSagaWithExceptionSagaReactor;

class AggregateSagaTest extends TestCase
{
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
        $eventOne->setAggregateRootVersion(1);
        $this->app->get(EloquentStoredEventRepository::class)->persist($eventOne, $aggregateUuid);
        $eventOne = EloquentStoredEvent::query()->where('event_class', SagaEventStepOne::class)->first()->event;

        $saga->onStart($eventOne, $eventOne->aggregateRootUuid());

        $saga = TestSagaWithExceptionSaga::retrieve($sagaUuid);

        $eventTwo = EloquentStoredEvent::query()->where('event_class',SagaEventStepTwo::class)->first()->event;
        $saga->onRunning($eventTwo, $eventTwo->aggregateRootUuid());

        // act
        Projectionist::addReactor(TestSagaWithExceptionSagaReactor::class);
        $saga = TestSagaWithExceptionSaga::retrieve($sagaUuid);
        $saga->setTries(1);
        $eventThree = EloquentStoredEvent::query()->where('event_class',SagaEventStepThree::class)->first()->event;;

        try {
            $saga->onComplete($eventThree, $eventThree->aggregateRootUuid());
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
        $eventOne->setStoredEventId(1);

        $saga->onStart($eventOne, $eventOne->aggregateRootUuid());

        $saga = TestSagaWithExceptionSaga::retrieve($sagaUuid);
        $eventTwo = new SagaEventStepTwo();
        $eventTwo->setAggregateRootUuid($aggregateUuid);
        $eventTwo->setStoredEventId(2);
        $saga->onRunning($eventTwo, $eventTwo->aggregateRootUuid());

        // act
        Projectionist::addReactor(TestSagaWithExceptionSagaReactor::class);

        $saga = TestSagaWithExceptionSaga::retrieve($sagaUuid);
        $saga->setTries(3);
        $eventThree = new SagaEventStepThree();
        $eventThree->setAggregateRootUuid($aggregateUuid);
        $eventThree->setStoredEventId(3);

        try {
            $saga->onComplete($eventThree, $eventThree->aggregateRootUuid());
        } catch (\Exception $exception) {
//            var_dump($exception->getMessage());
        } finally {
            // assert
            Mail::assertNothingQueued();
        }
    }


    public function test_it_can_save_saga_dedicated_database()
    {
        // arrange
        \Spatie\EventSourcing\Facades\Projectionist::withoutEventHandlers();
        \Spatie\EventSourcing\Facades\Projectionist::addReactors([TestSagaWithExceptionSagaAReactor::class]);
        // act
        $aggregateUuid = Uuid::uuid4();
        AggregateRootA::retrieve($aggregateUuid)->processStepFourA()->persist();

        EloquentStoredEvent::query()->where('aggregate_uuid', $aggregateUuid)->get()->each(function (EloquentStoredEvent $storedEvent) {
            $this->assertContains($storedEvent->event_class, [SagaEventStepFourA::class, SagaEventStepFive::class, SagaEventStepSix::class, SagaEventStepEight::class]);
        });

        EloquentSagaStoredEvent::query()->get()->each(function (EloquentSagaStoredEvent $sagaStoredEvent) {
            $this->assertContains($sagaStoredEvent->event_class, [SagaRunning::class, SagaStoredEventProcessed::class, SagaCompleted::class]);
        });
    }

}
