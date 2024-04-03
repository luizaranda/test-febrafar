<?php

namespace App\Providers;

use App\Contracts\ActivityRepositoryInterface;
use App\Contracts\ActivityServiceInterface;
use App\Repositories\ActivityRepository;
use App\Services\ActivityService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ActivityServiceInterface::class, ActivityService::class);
        $this->app->bind(ActivityRepositoryInterface::class, ActivityRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
