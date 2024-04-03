<?php

namespace App\Helpers;

use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
     * @param Throwable|HttpException $e The caught exception.
     * @return int The corresponding HTTP status code for the exception.
     */
    public static function getStatusCode(Throwable|HttpException $e): int
    {
        $code = $e->getStatusCode() ?? $e->getCode();
        return match ($code) {
            404 => ResponseAlias::HTTP_NOT_FOUND,
            401 => ResponseAlias::HTTP_UNAUTHORIZED,
            422, 400, 409 => ResponseAlias::HTTP_BAD_REQUEST,
            default => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
        };
    }

    /**
     * Validates the given date against today's date and checks if it falls on a weekend.
     *
     * @param string $date The date to validate in 'Y-m-d' format
     * @return bool Returns true if the date is valid and not a weekend, false otherwise
     */
    public static function validateDate(string $date): bool
    {
        $dateObj = \DateTime::createFromFormat('Y-m-d H:i:s', $date);
        $today = new \DateTime();

        if ($dateObj < $today) {
            return false;
        }
        $dayOfWeek = $dateObj->format('N');
        return match ($dayOfWeek) {
            '6', '7' => false,
            default => true
        };
    }
}
