<?php


namespace Spacebib\Saga\Tests\Sagas;


use Spacebib\Saga\AggregateSaga;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Spacebib\Saga\Tests\AggregateRoots\AggregateRootA;
use Spacebib\Saga\Tests\Emails\FailedSagaMail;
use Spacebib\Saga\Tests\Events\SagaEventStepOne;
use Spacebib\Saga\Tests\Events\SagaEventStepThree;
use Spacebib\Saga\Tests\Events\SagaEventStepTwo;

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
