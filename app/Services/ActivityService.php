<?php

namespace App\Services;

use App\Contracts\ActivityRepositoryInterface;
use App\Contracts\ActivityServiceInterface;
use App\Helpers\Helper;
use App\Models\Activity;
use Exception;

class ActivityService extends AbstractService implements ActivityServiceInterface
{
    public function __construct(ActivityRepositoryInterface $repository)
    {
        $this->setRepository($repository);
    }

    public function listAll(array $filters = [])
    {
        return $this->getRepository()->listAll($filters);
    }

    /**
     * @throws Exception
     */
    public function create(array $data, int $userId)
    {
        $activity = $this->getRepository()->getActivityByDateAndUserId($userId, $data);
        if (count($activity) > 0) {
            throw new Exception("Activity conflict on same date", 409);
        }
        if (!Helper::validateDate($data['start_date'])) {
            throw new Exception("Invalid start date", 400);
        }
        if (!Helper::validateDate($data['due_date'])) {
            throw new Exception("Invalid due date", 400);
        }

        return $this->getRepository()->create($data, $userId);
    }

    public function getByIdAndUserId(int $id, int $userId)
    {
        return $this->getRepository()->getByIdAndUserId($id, $userId);
    }

    public function update(array $data, Activity $activity)
    {
        return $this->getRepository()->updateActivity($data, $activity);
    }

    public function getActivityModelByUserAndId(int $id, int $userId)
    {
        return $this->getRepository()->getActivityModelByUserAndId($id, $userId);
    }

    public function destroy(int $id)
    {
        return $this->getRepository()->deleteActivity($id);
    }
}
