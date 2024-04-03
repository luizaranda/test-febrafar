<?php

namespace App\Services;

use App\Contracts\ActivityRepositoryInterface;
use App\Contracts\ActivityServiceInterface;
use App\Helpers\Helper;
use Exception;

class ActivityService extends AbstractService implements ActivityServiceInterface
{
    public function __construct(ActivityRepositoryInterface $repository)
    {
        $this->setDependency('repository', $repository);
    }

    public function listAll(array $filters = [])
    {
        return $this->getDependency('repository')->listAll($filters);
    }

    /**
     * @throws Exception
     */
    public function create(array $data, int $userId)
    {
        $repository = $this->getDependency('repository');
        $activity = $repository->getActivityByDateAndUserId($userId, $data);
        if (count($activity) > 0) {
            throw new Exception("Activity conflict on same date", 409);
        }
        if (!Helper::validateDate($data['start_date'])) {
            throw new Exception("Invalid start date", 400);
        }
        if (!Helper::validateDate($data['due_date'])) {
            throw new Exception("Invalid due date", 400);
        }

        return $this->getDependency('repository')->create($data, $userId);
    }

    public function getByIdAndUserId(int $id, int $userId)
    {
        return $this->getDependency('repository')->getByIdAndUserId($id, $userId);
    }
}
