<?php

namespace App\Traits;

use App\Repositories\AbstractRepository;

trait AvailabilityWithRepository
{
    private AbstractRepository $repository;

    public function setRepository(AbstractRepository $repository): void
    {
        $this->repository = $repository;
    }

    public function getRepository(): AbstractRepository
    {
        return $this->repository;
    }
}
