<?php

namespace App\Traits;

trait AvailabilityWithDependency
{
    protected array $dependencies = [];

    public function setDependency(string $key, $object): void
    {
        $this->dependencies[$key] = $object;
    }

    public function getDependency(string $key)
    {
        return (isset($this->dependencies[$key])) ? $this->dependencies[$key] : null;
    }
}
