<?php

namespace Spacebib\Saga\Tests;

use Ramsey\Uuid\Uuid;
use Spacebib\Saga\Tests\AggregateRoots\AggregateRootA;
use Spacebib\Saga\Tests\Events\SagaEventStepEight;
use Spacebib\Saga\Tests\Events\SagaEventStepFive;
use Spacebib\Saga\Tests\Events\SagaEventStepFourA;
use Spacebib\Saga\Tests\Events\SagaEventStepSix;
use Spacebib\Saga\Tests\Sagas\TestSagaWithExceptionSagaAReactor;
use Spacebib\Saga\Tests\Sagas\TestSagaWithExceptionSagaBReactor;

class AggregateSagaATest extends TestCase
{

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
