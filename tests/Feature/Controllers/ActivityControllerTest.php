<?php

namespace Tests\Feature\Controllers;

use App\Http\Controllers\ActivityController;
use App\Models\User;
use App\Repositories\ActivityRepository;
use App\Services\ActivityService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class ActivityControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $activityRepository = Mockery::mock(ActivityRepository::class);
        $activityService = Mockery::mock(ActivityService::class);
        $activityService->shouldReceive('getDependency')->with('repository')->andReturn($activityRepository);

        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->andReturn(1);
        Auth::shouldReceive('user')->andReturn($user);


        $this->controller = new ActivityController($activityService);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testIndexReturnsActivitiesWithFilters()
    {
        $userId = 1;
        $startDate = '2024-04-03';
        $dueDate = '2024-04-05';

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('get')->with('start_date')->andReturn($startDate);
        $request->shouldReceive('get')->with('due_date')->andReturn($dueDate);

        $activityRecords = collect([
            [
                'id' => 1,
                'title' => 'Test Activity 1',
                'type' => 'Task',
                'description' => 'some activity description',
                'start_date' => '2024-04-01 10:00:00',
                'due_date' => '2024-04-04 17:00:00',
                'status' => 'open',
                'user_id' => $userId,
                'created_at' => '2024-04-02 05:30:00',
                'updated_at' => '2024-04-02 05:32:15',
            ],
        ]);

        $activityCollection = Mockery::mock(Collection::class);
        $activityCollection->shouldReceive('all')->andReturn($activityRecords);

        $activityRepository = Mockery::mock(ActivityRepository::class);
        $activityRepository->shouldReceive('listAll')
            ->andReturn($activityCollection);

        $activityService = Mockery::mock(ActivityService::class);
        $activityService->shouldReceive('listAll')
            ->with([
                'user_id' => $userId,
                'start_date' => $startDate,
                'due_date' => $dueDate
            ])
            ->andReturn(['rows' => $activityRecords]);

        $this->controller->setService($activityService);

        $response = $this->controller->index($request);
        $responseData = $response->getData(true);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('body', $responseData);
        $this->assertIsArray($responseData['body']['result']['rows']);
        $this->assertNotEmpty($responseData['body']['result']['rows']);
        $this->assertArrayHasKey(0, $responseData['body']['result']['rows']);
        $this->assertArrayHasKey('id', $responseData['body']['result']['rows'][0]);
        $this->assertArrayHasKey('title', $responseData['body']['result']['rows'][0]);
        $this->assertArrayHasKey('type', $responseData['body']['result']['rows'][0]);
        $this->assertArrayHasKey('description', $responseData['body']['result']['rows'][0]);
        $this->assertArrayHasKey('start_date', $responseData['body']['result']['rows'][0]);
        $this->assertArrayHasKey('due_date', $responseData['body']['result']['rows'][0]);
        $this->assertArrayHasKey('status', $responseData['body']['result']['rows'][0]);
        $this->assertArrayHasKey('user_id', $responseData['body']['result']['rows'][0]);
        $this->assertArrayHasKey('created_at', $responseData['body']['result']['rows'][0]);
        $this->assertArrayHasKey('updated_at', $responseData['body']['result']['rows'][0]);
    }

    public function testIndexReturnsActivitiesWithoutFilters()
    {
        $userId = 1;

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('get')->with('start_date')->andReturnNull();
        $request->shouldReceive('get')->with('due_date')->andReturnNull();

        $activityRecords = collect([
            [
                'id' => 1,
                'title' => 'Test Activity 1',
                'type' => 'Task',
                'description' => 'some activity description',
                'start_date' => '2024-04-01 10:00:00',
                'due_date' => '2024-04-04 17:00:00',
                'status' => 'open',
                'user_id' => $userId,
                'created_at' => '2024-04-02 05:30:00',
                'updated_at' => '2024-04-02 05:32:15',
            ],
        ]);

        $activityCollection = Mockery::mock(Collection::class);
        $activityCollection->shouldReceive('all')->andReturn($activityRecords);

        $activityRepository = Mockery::mock(ActivityRepository::class);
        $activityRepository->shouldReceive('listAll')
            ->andReturn($activityCollection);

        $activityService = Mockery::mock(ActivityService::class);
        $activityService->shouldReceive('listAll')
            ->with([
                'user_id' => $userId,
                'start_date' => null,
                'due_date' => null
            ])
            ->andReturn(['rows' => $activityRecords]);

        $this->controller->setService($activityService);

        $response = $this->controller->index($request);
        $responseData = $response->getData(true);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('body', $responseData);
        $this->assertIsArray($responseData['body']['result']['rows']);
        $this->assertNotEmpty($responseData['body']['result']['rows']);
        $this->assertArrayHasKey(0, $responseData['body']['result']['rows']);
        $this->assertArrayHasKey('id', $responseData['body']['result']['rows'][0]);
        $this->assertArrayHasKey('title', $responseData['body']['result']['rows'][0]);
        $this->assertArrayHasKey('type', $responseData['body']['result']['rows'][0]);
        $this->assertArrayHasKey('description', $responseData['body']['result']['rows'][0]);
        $this->assertArrayHasKey('start_date', $responseData['body']['result']['rows'][0]);
        $this->assertArrayHasKey('due_date', $responseData['body']['result']['rows'][0]);
        $this->assertArrayHasKey('status', $responseData['body']['result']['rows'][0]);
        $this->assertArrayHasKey('user_id', $responseData['body']['result']['rows'][0]);
        $this->assertArrayHasKey('created_at', $responseData['body']['result']['rows'][0]);
        $this->assertArrayHasKey('updated_at', $responseData['body']['result']['rows'][0]);
    }

    public function testIndexReturnsEmptyResponseWhenNoActivitiesFound()
    {
        $userId = 1;

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('get')->with('start_date')->andReturnNull();
        $request->shouldReceive('get')->with('due_date')->andReturnNull();

        $activityCollection = Mockery::mock(Collection::class);
        $activityCollection->shouldReceive('all')->andReturn([]);

        $activityRepository = Mockery::mock(ActivityRepository::class);
        $activityRepository->shouldReceive('listAll')
            ->andReturn($activityCollection);

        $activityService = Mockery::mock(ActivityService::class);
        $activityService->shouldReceive('listAll')
            ->with([
                'user_id' => $userId,
                'start_date' => null,
                'due_date' => null
            ])
            ->andReturn([]);

        $this->controller->setService($activityService);

        $response = $this->controller->index($request);
        $responseData = $response->getData(true);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('body', $responseData);
        $this->assertIsArray($responseData['body']['result']);
        $this->assertEmpty($responseData['body']['result']);
    }
}
