<?php

namespace App\Contracts;

interface ActivityRepositoryInterface
{
    public function listAll(array $filters = []);

    public function create(array $data, int $userId);

    public function getActivityByDateAndUserId($userId, $date);
}
