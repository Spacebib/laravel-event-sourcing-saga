<?php


namespace Spacebib\Saga\Tests\Sagas;


use Spacebib\Saga\AggregateSagaReactor;
use Spacebib\Saga\Tests\Events\SagaEventStepFive;
use Spacebib\Saga\Tests\Events\SagaEventStepFourA;
use Spacebib\Saga\Tests\Events\SagaEventStepSix;

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
