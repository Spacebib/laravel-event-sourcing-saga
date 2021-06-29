<?php


namespace Tests\Spacebib\Saga\Sagas;


use Spacebib\Saga\AggregateSagaReactor;
use Tests\Spacebib\Saga\Events\SagaEventStepOne;
use Tests\Spacebib\Saga\Events\SagaEventStepThree;
use Tests\Spacebib\Saga\Events\SagaEventStepTwo;

class TestSagaWithExceptionSagaReactor extends AggregateSagaReactor
{

    protected array $handlesEvents = [
        SagaEventStepOne::class => 'onStart',
        SagaEventStepTwo::class => 'onRunning',
        SagaEventStepThree::class => 'onComplete',
    ];

    public function withSaga(): string
    {
        return TestSagaWithExceptionSaga::class;
    }


}
