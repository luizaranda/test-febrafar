<?php

namespace App\Repositories;

use App\Contracts\ActivityRepositoryInterface;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ActivityRepository extends AbstractRepository implements ActivityRepositoryInterface
{

    public function listAll(array $filters = [])
    {
        return Activity::query()
            ->where('user_id', $filters['user_id'])
            ->filterByDateRange($filters['start_date'], $filters['due_date'])
            ->get();
    }

    public function create(array $data, int $userId)
    {
        $data['user_id'] = $userId;
        return Activity::create($data);
    }

    public function getActivityByDateAndUserId($userId, $date): Collection|array
    {
        return Activity::query()
            ->where('user_id', $userId)
            ->filterByDateRange($date['start_date'], $date['due_date'])
            ->get();
    }

    public function getByIdAndUserId(int $id, int $userId): Collection|array
    {
        return Activity::query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->get();
    }

    public function updateActivity(array $data, Activity $activity): bool
    {
        return $activity->fill($data)->save($data);
    }

    public function getActivityModelByUserAndId(int $id, int $userId): Model|null
    {
        return Activity::query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    public function deleteActivity(int $id): bool
    {
        return Activity::destroy($id);
    }
}
