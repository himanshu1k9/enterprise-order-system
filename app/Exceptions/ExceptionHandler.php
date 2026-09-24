<?php

declare(strict_types = 1);

namespace App\Exceptions;

use App\Http\ApiResponse;
use App\Http\RequestId;
use App\Http\Response;
use App\Logging\Logger;
use Throwable;

class ExceptionHandler
{
    public function __construct(private Logger $logger, private RequestId $requestId)
    {}

    /**
     * Handle exceptions
     *
     * @param Throwable $exception
     * @return Response
     */
    public function handle(Throwable $exception): Response
    {
        /**
         * 429 Too many requests
         */
        if($exception instanceof RateLimitException) {
            return ApiResponse::error($exception->getMessage(), 429);
        }

        /**
         * 401 Authentication failled
         */
        if($exception instanceof AuthenticationException) {
            return ApiResponse::error($exception->getMessage(), 401);
        }

        /**
         * 409 Resource/state conflicts
         */
        if($exception instanceof ConflictException) {
            // return Response::json([
            //     'success' => false,
            //     'message' => $exception->getMessage()
            // ], 409);
            return ApiResponse::error($exception->getMessage(), 409);
        }

        /**
         * 404 Resource not found
         */
        if($exception instanceof NotFoundException) {
            $this->logger->warning(
                $exception->getMessage(),
                // [
                //     'request_id' => $this->requestId->get(),
                //     'exception' => $exception::class,
                //     'file' => $exception->getFile(),
                //     'line' => $exception->getLine(),
                //     'method' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
                //     'url' => $_SERVER['REQUEST_URI'] ?? 'UNKNOWN',
                // ]
                $this->context($exception)
            );

            // return Response::json(
            //     [
            //         'success' => false,
            //         'message' => $exception->getMessage()
            //     ], 404
            // );

            return ApiResponse::error($exception->getMessage(), 404);
        }

        /**
         * 422 Validation failled
         */
        if($exception instanceof ValidationException) {
            // return Response::json([
            //     'success' => false,
            //     'message' => $exception->getMessage(),
            //     'errors' => $exception->errors()
            // ], 422);

            return ApiResponse::error($exception->getMessage(), 422, $exception->errors());
        }

        $this->logger->error(
            $exception->getMessage(),
            // [
            //     'request_id' => $this->requestId->get(),
            //     'exception' => $exception::class,
            //     'file' => $exception->getFile(),
            //     'line' => $exception->getLine(),
            //     'method' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
            //     'url' => $_SERVER['REQUEST_URI'] ?? 'UNKNOWN',
            // ]
            $this->context($exception)
        );

        /*
         * Development environment:
         * expose debugging information.
         */
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

        // return Response::json(
        //     [
        //         'success' => false,
        //         'message' => 'Internal Server Error.'
        //     ], 500
        // );
        // return ApiResponse::error($exception->getMessage(), 500);
        /*
         * Production:
         * never expose internal exception details.
         */
        return ApiResponse::error('Internal server error.',500);
    }

    /**
     * Helper to log clear
     *
     * @param Throwable $exception
     * @return array
     */
    private function context(Throwable $exception): array
    {
        return [
            'request_id' => $this->requestId->get(),
            'exception' => $exception::class,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
            'url' => $_SERVER['REQUEST_URI'] ?? 'UNKNOWN',
        ];
    }
}