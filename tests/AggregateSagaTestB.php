<?php

namespace Tests\Spacebib\Saga;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Ramsey\Uuid\Uuid;
use Tests\Spacebib\Saga\AggregateRoots\AggregateRootA;
use Tests\Spacebib\Saga\Events\SagaEventStepFive;
use Tests\Spacebib\Saga\Events\SagaEventStepFourB;
use Tests\Spacebib\Saga\Events\SagaEventStepNine;
use Tests\Spacebib\Saga\Events\SagaEventStepSeven;
use Tests\Spacebib\Saga\Sagas\TestSagaWithExceptionSagaAReactor;
use Tests\Spacebib\Saga\Sagas\TestSagaWithExceptionSagaBReactor;
use Tests\TestCase;

class AggregateSagaTestB extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_event_five_and_seven_and_nine()
    {
        // arrange
        \Spatie\EventSourcing\Facades\Projectionist::withoutEventHandlers();
        \Spatie\EventSourcing\Facades\Projectionist::addReactors([TestSagaWithExceptionSagaAReactor::class, TestSagaWithExceptionSagaBReactor::class]);
        // act
        $aggregateUuid = Uuid::uuid4();
        AggregateRootA::retrieve($aggregateUuid)->processStepFourB()->persist();

        $aggregateRootA = AggregateRootA::retrieve($aggregateUuid);
        // assert
        $this->assertContains(
            SagaEventStepFourB::class,
            array_map('get_class', $aggregateRootA->getAppliedEvents())
        );

        $this->assertContains(
            SagaEventStepFive::class,
            array_map('get_class', $aggregateRootA->getAppliedEvents())
        );

        $this->assertContains(
            SagaEventStepSeven::class,
            array_map('get_class', $aggregateRootA->getAppliedEvents())
        );

        $this->assertContains(
            SagaEventStepNine::class,
            array_map('get_class', $aggregateRootA->getAppliedEvents())
        );

        $this->assertEquals(4, \count($aggregateRootA->getAppliedEvents()));
    }
}
