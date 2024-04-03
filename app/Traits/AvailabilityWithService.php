<?php

namespace App\Traits;

use App\Services\AbstractService;

trait AvailabilityWithService
{
    private AbstractService $service;

    public function setService(AbstractService $service): void
    {
        $this->service = $service;
    }

    public function getService(): AbstractService
    {
        return $this->service;
    }
}
