<?php


namespace Tests\Spacebib\Saga\AggregateRoots;


use Spatie\EventSourcing\AggregateRoots\AggregateRoot;
use Tests\Spacebib\Saga\Events\SagaEventStepEight;
use Tests\Spacebib\Saga\Events\SagaEventStepFive;
use Tests\Spacebib\Saga\Events\SagaEventStepFourA;
use Tests\Spacebib\Saga\Events\SagaEventStepFourB;
use Tests\Spacebib\Saga\Events\SagaEventStepNine;
use Tests\Spacebib\Saga\Events\SagaEventStepOne;
use Tests\Spacebib\Saga\Events\SagaEventStepSeven;
use Tests\Spacebib\Saga\Events\SagaEventStepSix;
use Tests\Spacebib\Saga\Events\SagaEventStepThree;
use Tests\Spacebib\Saga\Events\SagaEventStepTwo;

class AggregateRootA extends AggregateRoot
{
    public function processStepOne(): self
    {
        $this->recordThat(
            new SagaEventStepOne()
        );

        return $this;
    }

    public function processStepTwo(): self
    {
        $this->recordThat(
            new SagaEventStepTwo()
        );

        return $this;
    }

    public function processStepThree(): self
    {
        $this->recordThat(
            new SagaEventStepThree()
        );

        return $this;
    }

    public function processStepFourA():self
    {
        $this->recordThat(
            new SagaEventStepFourA()
        );

        return $this;
    }

    public function processStepFourB():self
    {
        $this->recordThat(
            new SagaEventStepFourB()
        );

        return $this;
    }

    public function processStepFive():self
    {
        $this->recordThat(
            new SagaEventStepFive()
        );

        return $this;
    }

    public function processStepSix():self
    {
        $this->recordThat(
            new SagaEventStepSix()
        );

        return $this;
    }

    public function processStepSeven():self
    {
        $this->recordThat(
            new SagaEventStepSeven()
        );

        return $this;
    }

    public function processStepEight():self
    {
        $this->recordThat(
            new SagaEventStepEight()
        );

        return $this;
    }

    public function processStepNine():self
    {
        $this->recordThat(
            new SagaEventStepNine()
        );

        return $this;
    }
}
