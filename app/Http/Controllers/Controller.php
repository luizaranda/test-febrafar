<?php

namespace App\Http\Controllers;

use App\Helpers\ErrorHelper;
use App\Helpers\Helper;
use App\Traits\AvailabilityWithService;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Controller extends BaseController
{
    use AuthorizesRequests,
        ValidatesRequests,
        AvailabilityWithService;

    protected int $responseHTTPCode = ResponseAlias::HTTP_OK;

    protected bool $trace = true;

    protected function setResponseHTTPCode(int $code): void
    {
        $this->responseHTTPCode = $code;
    }

    protected function getResponseHTTPCode(): int
    {
        return $this->responseHTTPCode;
    }

    protected function withTrace(): true
    {
        return $this->trace = true;
    }

    protected function withoutTrace(): false
    {
        return $this->trace = false;
    }

    protected function execute(Closure $closure): \Illuminate\Http\JsonResponse
    {
        $error = $error_type = $result = $complement = null;
        $status = false;

        try {
            $result = $closure();
            $status = true;
            $trace = null;
        } catch (ModelNotFoundException|NotFoundHttpException|Throwable $th) {
            $error_type = get_class($th);
            $error = $th->getMessage();
            $trace = $this->trace ? $th->getTraceAsString() : null;
            $complement = $th->getFile() . ':' . $th->getLine();
            $this->convertHTTPCodeToBadOnCaseError(Helper::getStatusCode($th));
        }

        $defaultJsonResponse = Helper::createDefaultJsonToResponse($status, compact(
            'error', 'error_type', 'result', 'complement', 'trace'
        ));

        return response()->json($defaultJsonResponse, $this->getResponseHTTPCode());
    }

    private function convertHTTPCodeToBadOnCaseError(int $code): void
    {
        if ($this->getResponseHTTPCode() == ResponseAlias::HTTP_OK) {
            $this->setResponseHTTPCode($code);
        }
    }
}
