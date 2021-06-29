<?php

namespace Tests\Spacebib\Saga;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Ramsey\Uuid\Uuid;
use Tests\Spacebib\Saga\AggregateRoots\AggregateRootA;
use Tests\Spacebib\Saga\Events\SagaEventStepEight;
use Tests\Spacebib\Saga\Events\SagaEventStepFive;
use Tests\Spacebib\Saga\Events\SagaEventStepFourA;
use Tests\Spacebib\Saga\Events\SagaEventStepSix;
use Tests\Spacebib\Saga\Sagas\TestSagaWithExceptionSagaAReactor;
use Tests\Spacebib\Saga\Sagas\TestSagaWithExceptionSagaBReactor;
use Tests\TestCase;

class AggregateSagaTestA extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_event_five_and_six_eight()
    {
        // arrange
        \Spatie\EventSourcing\Facades\Projectionist::withoutEventHandlers();
        \Spatie\EventSourcing\Facades\Projectionist::addReactors([TestSagaWithExceptionSagaAReactor::class, TestSagaWithExceptionSagaBReactor::class]);
        // act
        $aggregateUuid = Uuid::uuid4();
        AggregateRootA::retrieve($aggregateUuid)->processStepFourA()->persist();

        $aggregateRootA = AggregateRootA::retrieve($aggregateUuid);
        // assert
        $this->assertContains(
            SagaEventStepFourA::class,
            array_map('get_class', $aggregateRootA->getAppliedEvents())
        );

        $this->assertContains(
            SagaEventStepFive::class,
            array_map('get_class', $aggregateRootA->getAppliedEvents())
        );

        $this->assertContains(
            SagaEventStepSix::class,
            array_map('get_class', $aggregateRootA->getAppliedEvents())
        );

        $this->assertContains(
            SagaEventStepEight::class,
            array_map('get_class', $aggregateRootA->getAppliedEvents())
        );

        $this->assertEquals(4, \count($aggregateRootA->getAppliedEvents()));
    }
}
