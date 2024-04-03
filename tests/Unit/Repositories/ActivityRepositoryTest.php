<?php

namespace Tests\Unit\Repositories;

use App\Models\Activity;
use App\Models\User;
use App\Repositories\ActivityRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ActivityRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ActivityRepository();
    }

    public function testListAllWithFilters()
    {
        $user = User::factory()->create();
        Activity::factory()->create([
            'user_id' => $user->id,
            'start_date' => now()->subDays(5),
            'due_date' => now()->addDays(2),
        ]);
        $startDate = now()->subDays(5);
        $dueDate = now()->addDays(2);
        $filters = [
            'user_id' => $user->id,
            'start_date' => $startDate,
            'due_date' => $dueDate,
        ];

        $result = $this->repository->listAll($filters);

        $this->assertCount(1, $result);
        $this->assertTrue($result->firstWhere('due_date', '>=', $startDate)
            && $result->firstWhere('due_date', '<=', $dueDate));
    }

    public function testCreateActivity()
    {
        $user = User::factory()->create();
        $activityBody = [
            'title' => 'Test Activity 1',
            'type' => 'Task',
            'description' => 'some activity description',
            'start_date' => '2024-04-02 10:00:00',
            'due_date' => '2024-04-0402 11:00:00',
            'status' => 'open',
        ];

        $activity = $this->repository->create($activityBody, $user->id);

        $this->assertInstanceOf(Activity::class, $activity);
        $this->assertEquals($activityBody['title'], $activity->title);
        $this->assertEquals($activityBody['type'], $activity->type);
        $this->assertEquals($activityBody['description'], $activity->description);
        $this->assertEquals($activityBody['start_date'], $activity->start_date);
        $this->assertEquals($activityBody['due_date'], $activity->due_date);
        $this->assertEquals($activityBody['status'], $activity->status);
        $this->assertEquals($user->id, $activity->user_id);
    }

    public function testGetActivityByDateAndUserId()
    {
        $user = User::factory()->create();
        Activity::factory()->create([
            'user_id' => $user->id,
            'start_date' => now()->subDays(2),
            'due_date' => now()->subDays(2),
        ]);
        $activityBody = [
            'start_date' => now()->subDays(3),
            'due_date' => now()->subDays(1),
        ];

        $result = $this->repository->getActivityByDateAndUserId($user->id, $activityBody);

        $this->assertCount(1, $result);
        $this->assertTrue($result->firstWhere('due_date', '>=', $activityBody['start_date'])
            && $result->firstWhere('due_date', '<=', $activityBody['due_date']));
    }

    public function testGetByIdAndUserId()
    {
        $user = User::factory()->create();
        $activity = Activity::factory()->create(['user_id' => $user->id]);
        $id = $activity->id;

        $result = $this->repository->getByIdAndUserId($id, $user->id);

        $this->assertCount(1, $result);
        $this->assertEquals($activity->id, $result->first()->id);
        $this->assertEquals($user->id, $result->first()->user_id);
    }

    public function testUpdateActivity()
    {
        $user = User::factory()->create();
        $activity = Activity::factory()->create(['user_id' => $user->id]);
        $activityBody = [
            'title' => 'Atividade atualizada',
        ];

        $updatedActivity = $this->repository->updateActivity($activityBody, $activity);

        $this->assertTrue($updatedActivity);
        $this->assertEquals($activityBody['title'], $activity->fresh()->title);
    }

    public function testGetActivityModelByUserAndId()
    {
        $user = User::factory()->create();
        $activity = Activity::factory()->create(['user_id' => $user->id]);
        $id = $activity->id;
        $userId = $user->id;

        $activityModel = $this->repository->getActivityModelByUserAndId($id, $userId);

        $this->assertInstanceOf(Activity::class, $activityModel);
        $this->assertEquals($id, $activityModel->id);
        $this->assertEquals($userId, $activityModel->user_id);
    }

    public function testDeleteActivity()
    {
        $user = User::factory()->create();
        $activity = Activity::factory()->create(['user_id' => $user->id]);
        $id = $activity->id;

        $deleted = $this->repository->deleteActivity($id);

        $this->assertTrue($deleted);
        $this->assertNull(Activity::find($id));
    }
}
