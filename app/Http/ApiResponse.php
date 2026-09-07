<?php

declare(strict_types = 1);

namespace App\Http;

class ApiResponse
{
    /**
     * Method to return success response
     *
     * @param string $message
     * @param mixed $data
     * @param integer $status
     * @return Response
     */
    public static function success(string $message, mixed $data = null, int $status = 200): Response
    {
        return Response::json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    /**
     * Method to return errors response
     *
     * @param string $message
     * @param integer $status
     * @param array $errors
     * @return Response
     */
    public static function error(string $message, int $status = 400, array $errors = []):Response
    {
        return Response::json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }
}