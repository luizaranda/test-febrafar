<?php

namespace App\Contracts;

interface ActivityServiceInterface
{
    public function listAll(array $filters = []);
}
