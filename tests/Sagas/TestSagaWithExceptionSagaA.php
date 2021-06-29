<?php


namespace Tests\Spacebib\Saga\Sagas;


use Spacebib\Saga\AggregateSaga;
use Tests\Spacebib\Saga\AggregateRoots\AggregateRootA;
use Tests\Spacebib\Saga\Events\SagaEventStepFive;
use Tests\Spacebib\Saga\Events\SagaEventStepFourA;
use Tests\Spacebib\Saga\Events\SagaEventStepSix;

class TestSagaWithExceptionSagaA extends AggregateSaga
{
    public function onSagaEventStepFourA(SagaEventStepFourA $event)
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
                ->processStepSix()
        );
    }

    public function onSagaEventStepSix(SagaEventStepSix $event)
    {
        $this->persistForSaga(
            AggregateRootA::retrieve($event->aggregateRootUuid())
                ->processStepEight()
        );
    }



}
