<?php


namespace Spacebib\Saga\Tests\Sagas;


use Spacebib\Saga\AggregateSaga;
use Spacebib\Saga\Tests\AggregateRoots\AggregateRootA;
use Spacebib\Saga\Tests\Events\SagaEventStepFive;
use Spacebib\Saga\Tests\Events\SagaEventStepFourB;
use Spacebib\Saga\Tests\Events\SagaEventStepSeven;

class TestSagaWithExceptionSagaB extends AggregateSaga
{
    public function onSagaEventStepFourB(SagaEventStepFourB $event)
    {
        $this->persistForSaga(
            AggregateRootA::retrieve($event->aggregateRootUuid())
                ->processStepFive()
        );
    }

    public function onSagaEventStepFive(SagaEventStepFive $event)
    {
        $this->persistForSaga(
            AggregateRootA::retrieve($event->aggregateRootUuid())
                ->processStepSeven()
        );
    }

    public function onSagaEventStepSeven(SagaEventStepSeven $event)
    {
        $this->persistForSaga(
            AggregateRootA::retrieve($event->aggregateRootUuid())
                ->processStepNine()
        );
    }



}
