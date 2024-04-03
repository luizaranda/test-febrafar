<?php

namespace App\Services;

use App\Traits\AvailabilityWithDependency;

abstract class AbstractService
{
    use AvailabilityWithDependency;
}
