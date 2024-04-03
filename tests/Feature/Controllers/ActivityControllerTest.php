<?php

namespace Tests\Feature\Controllers;

use App\Http\Controllers\ActivityController;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use App\Models\User;
use App\Repositories\ActivityRepository;
use App\Services\ActivityService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class ActivityControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $activityRepository = Mockery::mock(ActivityRepository::class);
        $activityService = Mockery::mock(ActivityService::class);
        $activityService->shouldReceive('getDependency')->with('repository')->andReturn($activityRepository);

        $this->controller = new ActivityController($activityService);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function mockUser(int $id = 1)
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->andReturn($id);
        $user->shouldReceive('offsetExists')->andReturn(true);
        Auth::shouldReceive('user')->andReturn($user);
        return $user;
    }

    public function testIndexReturnsActivitiesWithFilters()
    {
        $this->mockUser();
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
        $this->mockUser();
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
        $this->mockUser();
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

    public function testStoreReturnsUnauthorizedWithoutUserId()
    {
        $response = $this->postJson('/api/activity', []);

        $response->assertStatus(401)
            ->assertContent('{"message":"Unauthenticated."}');
    }

    public function testStoreReturnsActivityHasBeenCreated()
    {
        $this->mockUser();
        $userId = 1;

        $activityBody = [
            'title' => 'Test Activity 1',
            'type' => 'Task',
            'description' => 'some activity description',
            'start_date' => '2024-04-02 10:00:00',
            'due_date' => '2024-04-0402 11:00:00',
            'status' => 'open',
        ];
        $activityMock = Activity::factory([
            ...$activityBody,
            'user_id' => $userId,
            'id' => 1,
            'created_at' => '2024-04-02 05:30:00',
            'updated_at' => '2024-04-02 05:32:15'])->make();
        $request = Mockery::mock(StoreActivityRequest::class);
        $request->shouldReceive('all')->andReturn($activityBody);

        $activity = Mockery::mock(Activity::class);
        $activity->shouldReceive('exists')->andReturn(true);
        $activity->shouldReceive('jsonSerialize')->andReturn(['rows' => [$activityMock]]);

        $activityCollection = Mockery::mock(Collection::class);
        $activityCollection->shouldReceive('get')->andReturn([]);

        $activityRepository = Mockery::mock(ActivityRepository::class);
        $activityRepository->shouldReceive('getActivityByDateAndUserId')
            ->andReturn($activityCollection);

        $activityService = Mockery::mock(ActivityService::class);
        $activityService->shouldReceive('create')
            ->with($activityBody, $userId)
            ->andReturn($activity);

        $this->controller->setService($activityService);

        $response = $this->controller->store($request);
        $responseData = $response->getData(true);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
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

    public function testShowAuthorizedWithData()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $activityService = $this->createMock(ActivityService::class);
        $activityService->expects($this->once())
            ->method('getByIdAndUserId')
            ->with($this->equalTo(1), $this->equalTo($user->id))
            ->willReturn(['data' => 'result']);

        $this->controller->setService($activityService);

        $response = $this->controller->show(1);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson(json_encode(['data' => 'result']));
    }

    public function testShowUnauthorized()
    {
        $response = $this->getJson('/api/activity/1');

        $response->assertStatus(401)
            ->assertContent('{"message":"Unauthenticated."}');
    }

    public function testShowNotFound()
    {
        $user = $this->mockUser();

        $activityService = Mockery::mock(ActivityService::class);
        $activityService->shouldReceive('getByIdAndUserId')
            ->with($this->equalTo(1), $this->equalTo($user->id))
            ->andReturn([]);

        $this->controller->setService($activityService);

        $response = $this->controller->show(1);
        $responseData = $response->getData(true);

        $this->assertFalse($responseData['body']['result']);
    }

    public function testUpdateAuthorized()
    {
        $activityMock = Activity::factory()->create();
        $this->mockUser($activityMock->user_id);

        $activityBody = [
            'title' => 'Test Activity 1',
            'type' => 'Task',
            'description' => 'some activity description',
            'start_date' => '2024-04-02 10:00:00',
            'due_date' => '2024-04-0402 11:00:00',
            'status' => 'open',
        ];
        $request = Mockery::mock(UpdateActivityRequest::class);
        $request->shouldReceive('validated')->andReturn($activityBody);

        $activityService = Mockery::mock(ActivityService::class);
        $activityService->shouldReceive('getActivityModelByUserAndId')->andReturn($activityMock);
        $activityService->shouldReceive('update')
            ->with($activityBody, $activityMock)
            ->andReturn($activityMock->fill($activityBody));

        $this->controller->setService($activityService);

        $response = $this->controller->update($request, $activityMock->id);
        $responseData = $response->getData(true);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('body', $responseData);
        $this->assertIsArray($responseData['body']['result']);
        $this->assertNotEmpty($responseData['body']['result']);
        $this->assertEquals($activityBody['title'], $responseData['body']['result']['title']);
        $this->assertEquals($activityBody['type'], $responseData['body']['result']['type']);
        $this->assertEquals($activityBody['description'], $responseData['body']['result']['description']);
        $this->assertEquals($activityBody['start_date'], $responseData['body']['result']['start_date']);
        $this->assertEquals($activityBody['due_date'], $responseData['body']['result']['due_date']);
        $this->assertEquals($activityBody['status'], $responseData['body']['result']['status']);
    }

    public function testUpdateUnauthorized()
    {
        $response = $this->putJson('/api/activity/1');

        $response->assertStatus(401)
            ->assertContent('{"message":"Unauthenticated."}');
    }

    public function testDeleteUnauthorized()
    {
        $response = $this->deleteJson('/api/activity/1');

        $response->assertStatus(401)
            ->assertContent('{"message":"Unauthenticated."}');
    }

    public function testDeleteAuthorized()
    {
        $activityMock = Activity::factory()->create();
        $this->mockUser($activityMock->user_id);

        $activityService = Mockery::mock(ActivityService::class);
        $activityService->shouldReceive('getActivityModelByUserAndId')->andReturn($activityMock);
        $activityService->shouldReceive('destroy')
            ->with($activityMock->id)
            ->andReturn(true);

        $this->controller->setService($activityService);

        $response = $this->controller->destroy($activityMock->id);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(204, $response->getStatusCode());
    }
}
