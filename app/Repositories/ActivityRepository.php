<?php

namespace App\Repositories;

use App\Contracts\ActivityRepositoryInterface;
use App\Models\Activity;

class ActivityRepository extends AbstractRepository implements ActivityRepositoryInterface
{

    public function listAll(array $filters = [])
    {
        return Activity::query()
            ->where('user_id', $filters['user_id'])
            ->filterByDateRange($filters['start_date'], $filters['due_date'])
            ->get();
    }
}
