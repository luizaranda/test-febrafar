<?php

namespace App\Http\Controllers;

use App\Contracts\ActivityServiceInterface;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Http\Resources\ActivityResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

/**
 * @OA\Swagger(
 *     basePath="/api",
 *     @OA\Info(
 *         version="1.0.0",
 *         title="Activity API",
 *         description="API for managing activities"
 *     )
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="apiKey",
 *     in="header",
 *     name="Authorization"
 * )
 * @OA\Schema(
 *      schema="Activity",
 *      type="object",
 *      @OA\Property(property="id", type="string", description="ID of the activity"),
 *      @OA\Property(property="title", type="string", description="Title of the activity"),
 *      @OA\Property(property="type", type="string", description="Type of the activity"),
 *      @OA\Property(property="description", type="string", description="Description of the activity"),
 *      @OA\Property(property="start_date", type="string", format="date-time", description="Start date of the activity"),
 *      @OA\Property(property="due_date", type="string", format="date-time", description="Due date of the activity"),
 *      @OA\Property(property="completion_date", type="string", format="date-time", description="Completion date of the activity (optional)"),
 *      @OA\Property(property="status", type="string", description="Status of the activity"),
 *      @OA\Property(property="user_id", type="integer", description="ID of the user who created the activity"),
 *      @OA\Property(property="created_at", type="datetime", description="Date and time when the activity was created"),
 *      @OA\Property(property="updated_at", type="datetime", description="Date and time when the activity was updated")
 *  )
 *
 * @OA\Schema(
 *      schema="ActivityRequest",
 *      type="object",
 *      @OA\Property(property="title", type="string", description="Title of the activity"),
 *      @OA\Property(property="type", type="string", description="Type of the activity"),
 *      @OA\Property(property="description", type="string", description="Description of the activity"),
 *      @OA\Property(property="start_date", type="string", format="date-time", description="Start date of the activity"),
 *      @OA\Property(property="due_date", type="string", format="date-time", description="Due date of the activity"),
 *      @OA\Property(property="completion_date", type="string", format="date-time", description="Completion date of the activity (optional)"),
 *      @OA\Property(property="status", type="string", description="Status of the activity"),
 *  )
 */
class ActivityController extends Controller
{
    public function __construct(ActivityServiceInterface $service)
    {
        $this->setService($service);
    }

    /**
     * Display a listing of the resource.
     * @OA\Get(
     *      path="/activities",
     *      operationId="getActivities",
     *      tags={"Activity"},
     *      summary="List activities",
     *      description="Retrieve a list of activities for the authenticated user.",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *           name="start_date",
     *           in="path",
     *           required=false,
     *           description="Ex.: 2024-04-04 13:00:00",
     *           @OA\Schema(type="string")
     *       ),
     *      @OA\Parameter(
     *           name="due_date",
     *           in="path",
     *           required=false,
     *           description="Ex.: 2024-04-04 13:00:00",
     *           @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Activity")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      )
     *  )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return parent::execute(function () use ($request) {
            $userId = auth()->user()->id ?? null;
            if ($userId == null) {
                abort(ResponseAlias::HTTP_UNAUTHORIZED, 'Unauthorized');
            }
            $result = $this->getService()->listAll([
                'user_id' => $userId,
                'start_date' => $request->get('start_date') ?? null,
                'due_date' => $request->get('due_date') ?? null
            ]);
            if (isset($result['rows'])) {
                $result = ActivityResource::collection($result);
            }
            return $result;
        });
    }


    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *      path="/activities",
     *      operationId="createActivity",
     *      tags={"Activity"},
     *      summary="Create a new activity",
     *      description="Create a new activity for the authenticated user.",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/ActivityRequest")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Created",
     *          @OA\JsonContent(ref="#/components/schemas/Activity")
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request (validation errors)"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      )
     *  )
     */
    public function store(StoreActivityRequest $request)
    {
        return parent::execute(function () use ($request) {
            $userId = auth()->user()->id ?? null;
            if ($userId == null) {
                abort(ResponseAlias::HTTP_UNAUTHORIZED, 'Unauthorized');
            }
            $data = $request->all();
            $result = $this->getService()->create($data, $userId);
            if ($result->exists()) {
                $this->setResponseHTTPCode(ResponseAlias::HTTP_CREATED);
            }
            return $result;
        });
    }

    /**
     * Display the specified resource.
     *
     * Passing ID to avoid ModelNotFoundException and show friendly error
     * on not found
     *
     * @OA\Get(
     *      path="/activities/{id}",
     *      operationId="getActivityById",
     *      tags={"Activity"},
     *      summary="Get activity details",
     *      description="Retrieve details of a specific activity for the authenticated user.",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(ref="#/components/schemas/Activity")
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found"
     *      )
     *  )
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        return parent::execute(function () use ($id) {
            $userId = auth()->user()->id ?? null;
            if ($userId == null) {
                abort(ResponseAlias::HTTP_UNAUTHORIZED, 'Unauthorized');
            }
            $result = $this->getService()->getByIdAndUserId($id, $userId);
            if (count($result) > 0) {
                $this->setResponseHTTPCode(ResponseAlias::HTTP_OK);
                return $result;
            }
            $this->setResponseHTTPCode(ResponseAlias::HTTP_NOT_FOUND);
            return false;
        });
    }

    /**
     * Update the specified resource in storage.
     *
     * Passing ID to avoid ModelNotFoundException and show friendly error
     * on not found
     *
     * @OA\Put(
     *      path="/activities/{id}",
     *      operationId="updateActivity",
     *      tags={"Activity"},
     *      summary="Update an activity",
     *      description="Update an existing activity for the authenticated user.",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *           name="id",
     *           in="path",
     *           required=true,
     *           @OA\Schema(type="integer")
     *       ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/ActivityRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(ref="#/components/schemas/Activity")
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request (validation errors)"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found"
     *      )
     *  )
     *
     * @param UpdateActivityRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateActivityRequest $request, int $id): JsonResponse
    {
        return parent::execute(function () use ($request, $id) {
            $userId = auth()->user()->id ?? null;
            $activity = $this->getService()->getActivityModelByUserAndId($id, $userId);
            if ($userId == null || $activity == null) {
                abort(ResponseAlias::HTTP_UNAUTHORIZED, 'Unauthorized');
            }
            $data = $request->validated();
            $result = $this->getService()->update($data, $activity);
            if ($result) {
                $this->setResponseHTTPCode(ResponseAlias::HTTP_OK);
            }
            return $activity;
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     *  Passing ID to avoid ModelNotFoundException and show friendly error
     *  on not found
     *
     * @OA\Delete(
     *      path="/activities/{id}",
     *      operationId="deleteActivity",
     *      tags={"Activity"},
     *      summary="Delete an activity",
     *      description="Delete an existing activity for the authenticated user.",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *           name="id",
     *           in="path",
     *           required=true,
     *           @OA\Schema(type="integer")
     *       ),
     *      @OA\Response(
     *          response=204,
     *          description="No Content"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found"
     *      )
     *  )
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        return parent::execute(function () use ($id) {
            $userId = auth()->user()->id ?? null;
            $activity = $this->getService()->getActivityModelByUserAndId($id, $userId);
            if ($userId == null || $activity == null) {
                abort(ResponseAlias::HTTP_UNAUTHORIZED, 'Unauthorized');
            }
            $result = $this->getService()->destroy($id);
            if ($result) {
                $this->setResponseHTTPCode(ResponseAlias::HTTP_NO_CONTENT);
            }
        });
    }
}
