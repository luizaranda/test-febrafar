<?php

namespace App\Helpers;

use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class Helper
{
    /**
     * Create a default JSON response array.
     *
     * @param bool $status
     * @param mixed|null $content
     * @return array
     */
    public static function createDefaultJsonToResponse(bool $status, mixed $content = null): array
    {
        return ['status' => $status, 'body' => $content];
    }

    /**
     * Gets the appropriate HTTP status code for a caught exception.
     *
     * @param Throwable $e The caught exception.
     * @return int The corresponding HTTP status code for the exception.
     */
    public static function getStatusCode(Throwable $e): int
    {
        return match ($e->getCode()) {
            404 => ResponseAlias::HTTP_NOT_FOUND,
            422, 400 => ResponseAlias::HTTP_BAD_REQUEST,
            default => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
        };
    }
}
