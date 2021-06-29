<?php


namespace Tests\Spacebib\Saga\Sagas;


use Spacebib\Saga\AggregateSaga;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\Spacebib\Saga\AggregateRoots\AggregateRootA;
use Tests\Spacebib\Saga\Emails\FailedSagaMail;
use Tests\Spacebib\Saga\Events\SagaEventStepOne;
use Tests\Spacebib\Saga\Events\SagaEventStepThree;
use Tests\Spacebib\Saga\Events\SagaEventStepTwo;

class TestSagaWithExceptionSaga extends AggregateSaga
{
    public function onSagaEventStepOne(SagaEventStepOne $event)
    {
        $this->persistForSaga(
            AggregateRootA::retrieve($event->aggregateRootUuid())
                ->processStepTwo($event->aggregateRootUuid())
        );
    }

    public function onSagaEventStepOneRollback(SagaEventStepOne $event)
    {
        Mail::to('xuding@spacebib.com')->send(new FailedSagaMail());
    }

    public function onSagaEventStepTwo(SagaEventStepTwo $event)
    {
        $this->persistForSaga(
            AggregateRootA::retrieve($event->aggregateRootUuid())
                ->processStepThree()
        );
    }

    public function onSagaEventStepTwoRollback(SagaEventStepTwo $event)
    {
        Mail::to('xuding@spacebib.com')->send(new FailedSagaMail());
    }

    public function onSagaEventStepThree(SagaEventStepThree $event)
    {
        throw new RuntimeException('Unexpected error');
    }

}
