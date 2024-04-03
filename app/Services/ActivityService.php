<?php

namespace App\Services;

use App\Contracts\ActivityRepositoryInterface;
use App\Contracts\ActivityServiceInterface;

class ActivityService extends AbstractService implements ActivityServiceInterface
{
    public function __construct(ActivityRepositoryInterface $repository)
    {
        $this->setDependency('repository', $repository);
    }

    public function listAll(array $filters = []){
        return $this->getDependency('repository')->listAll($filters);
    }
}
