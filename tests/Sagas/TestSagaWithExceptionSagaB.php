<?php


namespace Tests\Spacebib\Saga\Sagas;


use Spacebib\Saga\AggregateSaga;
use Tests\Spacebib\Saga\AggregateRoots\AggregateRootA;
use Tests\Spacebib\Saga\Events\SagaEventStepFive;
use Tests\Spacebib\Saga\Events\SagaEventStepFourB;
use Tests\Spacebib\Saga\Events\SagaEventStepSeven;

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
