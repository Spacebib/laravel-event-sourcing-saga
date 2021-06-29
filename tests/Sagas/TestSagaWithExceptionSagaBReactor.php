<?php


namespace Tests\Spacebib\Saga\Sagas;


use Spacebib\Saga\AggregateSagaReactor;
use Tests\Spacebib\Saga\Events\SagaEventStepFive;
use Tests\Spacebib\Saga\Events\SagaEventStepFourB;
use Tests\Spacebib\Saga\Events\SagaEventStepSeven;

class TestSagaWithExceptionSagaBReactor extends AggregateSagaReactor
{

    protected array $handlesEvents = [
        SagaEventStepFourB::class => 'onStart',
        SagaEventStepFive::class => 'onRunning',
        SagaEventStepSeven::class => 'onComplete',
    ];

    public function withSaga(): string
    {
        return TestSagaWithExceptionSagaB::class;
    }


}
