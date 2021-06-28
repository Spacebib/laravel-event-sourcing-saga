<?php

namespace Spacebib\Saga\Enum;

use Spatie\Enum\Enum;

/**
 * @method static self NOT_STARTED()
 * @method static self RUNNING()
 * @method static self COMPLETED()
 */

class SagaInternalState extends Enum
{
    public function equals(self ...$others): bool
    {
        foreach ($others as $other) {
            if (
                static::class === \get_class($other)
                && $this->value === $other->value
            ) {
                return true;
            }
        }

        return false;
    }
}
