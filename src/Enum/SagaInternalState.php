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
}
