<?php


namespace Spacebib\Saga\Tests\Sagas;


use Spacebib\Saga\AggregateSaga;
use Spacebib\Saga\Tests\AggregateRoots\AggregateRootA;
use Spacebib\Saga\Tests\Events\SagaEventStepFive;
use Spacebib\Saga\Tests\Events\SagaEventStepFourA;
use Spacebib\Saga\Tests\Events\SagaEventStepSix;

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
