<?php

namespace App\Contracts;

interface ActivityRepositoryInterface
{
    public function listAll(array $filters = []);
}
