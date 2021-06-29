<?php


namespace Tests\Spacebib\Saga\Sagas;


use Spacebib\Saga\AggregateSagaReactor;
use Tests\Spacebib\Saga\Events\SagaEventStepFive;
use Tests\Spacebib\Saga\Events\SagaEventStepFourA;
use Tests\Spacebib\Saga\Events\SagaEventStepSix;

class TestSagaWithExceptionSagaAReactor extends AggregateSagaReactor
{

    protected array $handlesEvents = [
        SagaEventStepFourA::class => 'onStart',
        SagaEventStepFive::class => 'onRunning',
        SagaEventStepSix::class => 'onComplete',
    ];

    public function withSaga(): string
    {
        return TestSagaWithExceptionSagaA::class;
    }


}
