<?php

namespace App\Contracts;

interface ActivityServiceInterface
{
    public function listAll(array $filters = []);

    public function create(array $data, int $userId);
}
