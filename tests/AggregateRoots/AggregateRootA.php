<?php


namespace Spacebib\Saga\Tests\AggregateRoots;


use Spatie\EventSourcing\AggregateRoots\AggregateRoot;
use Spacebib\Saga\Tests\Events\SagaEventStepEight;
use Spacebib\Saga\Tests\Events\SagaEventStepFive;
use Spacebib\Saga\Tests\Events\SagaEventStepFourA;
use Spacebib\Saga\Tests\Events\SagaEventStepFourB;
use Spacebib\Saga\Tests\Events\SagaEventStepNine;
use Spacebib\Saga\Tests\Events\SagaEventStepOne;
use Spacebib\Saga\Tests\Events\SagaEventStepSeven;
use Spacebib\Saga\Tests\Events\SagaEventStepSix;
use Spacebib\Saga\Tests\Events\SagaEventStepThree;
use Spacebib\Saga\Tests\Events\SagaEventStepTwo;

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
