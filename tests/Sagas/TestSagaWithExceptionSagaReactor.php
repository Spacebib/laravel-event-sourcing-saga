<?php


namespace Spacebib\Saga\Tests\Sagas;


use Spacebib\Saga\AggregateSagaReactor;
use Spacebib\Saga\Tests\Events\SagaEventStepOne;
use Spacebib\Saga\Tests\Events\SagaEventStepThree;
use Spacebib\Saga\Tests\Events\SagaEventStepTwo;

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
