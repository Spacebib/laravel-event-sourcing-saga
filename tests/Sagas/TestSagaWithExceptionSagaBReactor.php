<?php


namespace Spacebib\Saga\Tests\Sagas;


use Spacebib\Saga\AggregateSagaReactor;
use Spacebib\Saga\Tests\Events\SagaEventStepFive;
use Spacebib\Saga\Tests\Events\SagaEventStepFourB;
use Spacebib\Saga\Tests\Events\SagaEventStepSeven;

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
