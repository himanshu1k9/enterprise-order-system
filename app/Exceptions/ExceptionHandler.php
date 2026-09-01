<?php

declare(strict_types = 1);

namespace App\Exceptions;

use App\Http\Response;
use Throwable;

class ExceptionHandler
{
    public function handle(Throwable $exception): Response
    {
        /**
         * Handeling conflicts exceptions
         */
        if($exception instanceof ConflictException) {
            return Response::json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 409);
        }

        /**
         * If exception is Not found then send this response
         */
        if($exception instanceof NotFoundException) {
            return Response::json(
                [
                    'success' => false,
                    'message' => $exception->getMessage()
                ], 404
            );
        }

        /**
         * Handeling Validation exceptions
         */
        if($exception instanceof ValidationException) {
            return Response::json([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->errors()
            ], 422);
        }

        $environment = $_ENV['APP_ENV'] ?? 'production';
        if($environment === 'development') {
            return Response::json(
                [
                    'success' => false,
                    'message' => $exception->getMessage(),
                    'exception' => $exception::class,
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine()
                ], 500
            );
        }

        return Response::json(
            [
                'success' => false,
                'message' => 'Internal Server Error.'
            ], 500
        );
    }
}