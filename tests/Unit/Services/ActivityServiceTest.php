<?php

namespace Tests\Unit\Services;

use App\Models\Activity;
use App\Models\User;
use App\Repositories\ActivityRepository;
use App\Services\ActivityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityServiceTest extends TestCase
{
    use RefreshDatabase;

    private ActivityService $service;
    private ActivityRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = \Mockery::mock(ActivityRepository::class);
        $this->service = new ActivityService($this->repository);
    }

    public function testListAll()
    {
        $expectedActivities = Activity::factory()->count(3)->make();
        $this->repository->shouldReceive('listAll')->andReturn($expectedActivities);

        $activities = $this->service->listAll();

        $this->assertEquals($expectedActivities, $activities);
    }

    public function testCreateSuccess()
    {
        $activityBody = [
            'title' => 'Test Activity 1',
            'type' => 'Task',
            'description' => 'some activity description',
            'start_date' => now()->addDays(1),
            'due_date' => now()->addDays(1),
            'status' => 'open',
        ];
        $userId = User::factory()->create()->id;
        $expectedActivity = Activity::factory()->create(['user_id' => $userId]);
        $this->repository->shouldReceive('getActivityByDateAndUserId')->andReturn([]);
        $this->repository->shouldReceive('create')->once()->andReturn($expectedActivity);

        $activity = $this->service->create($activityBody, $userId);

        $this->assertEquals($expectedActivity, $activity);
    }

    public function testCreateConflictActivitySameDate()
    {
        $activityBody = [
            'title' => 'Test Activity 1',
            'type' => 'Task',
            'description' => 'some activity description',
            'start_date' => now()->addDays(1),
            'due_date' => now()->addDays(1),
            'status' => 'open',
        ];
        $userId = User::factory()->create()->id;
        $expectedActivity = Activity::factory()->create([...$activityBody, 'user_id' => $userId]);
        $this->repository->shouldReceive('getActivityByDateAndUserId')->andReturn($expectedActivity->toArray());

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Activity conflict on same date');
        $this->expectExceptionCode(409);

        $this->service->create($activityBody, $userId);
    }

    public function testCreateInvalidStartDate()
    {
        $activityBody = [
            'title' => 'Test Activity 1',
            'type' => 'Task',
            'description' => 'some activity description',
            'start_date' => 'invalid date',
            'due_date' => now()->addDays(1),
            'status' => 'open',
        ];
        $userId = User::factory()->create()->id;
        $this->repository->shouldReceive('getActivityByDateAndUserId')->andReturn([]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid start date');
        $this->expectExceptionCode(400);
        $this->service->create($activityBody, $userId);
    }

    public function testGetByIdAndUserId()
    {
        $expectedActivity = Activity::factory()->create();
        $id = $expectedActivity->id;
        $userId = $expectedActivity->user_id;
        $this->repository->shouldReceive('getByIdAndUserId')->with($id, $userId)->andReturn($expectedActivity->toArray());

        $activity = $this->service->getByIdAndUserId($id, $userId);

        $this->assertEquals($expectedActivity->toArray(), $activity);
    }

    public function testUpdateSuccess()
    {
        $activityBody = [
            'title' => 'Test Activity 1',
            'type' => 'Task',
            'description' => 'some activity description',
            'start_date' => now()->addDays(1),
            'due_date' => now()->addDays(1),
            'status' => 'open',
        ];
        $activity = Activity::factory()->create();
        $this->repository->shouldReceive('updateActivity')->once()->andReturn(true);

        $updated = $this->service->update($activityBody, $activity);

        $this->assertTrue($updated);
    }

    public function testGetActivityModelByUserAndId()
    {
        $expectedActivity = Activity::factory()->create();
        $id = $expectedActivity->id;
        $userId = $expectedActivity->user_id;
        $this->repository->shouldReceive('getActivityModelByUserAndId')->with($id, $userId)->once()->andReturn($expectedActivity);

        $activityModel = $this->service->getActivityModelByUserAndId($id, $userId);

        $this->assertEquals($expectedActivity, $activityModel);
    }
}
