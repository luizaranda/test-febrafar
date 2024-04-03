<?php

namespace App\Http\Controllers;

use App\Contracts\ActivityServiceInterface;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ActivityController extends Controller
{
    public function __construct(ActivityServiceInterface $service)
    {
        $this->setService($service);
    }

    /**
     * Display a listing of the resource.
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
     */
    public function update(UpdateActivityRequest $request, Activity $activity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity)
    {
        //
    }
}
