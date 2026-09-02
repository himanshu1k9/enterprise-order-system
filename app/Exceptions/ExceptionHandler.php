<?php

declare(strict_types = 1);

namespace App\Exceptions;

use App\Http\RequestId;
use App\Http\Response;
use App\Logging\Logger;
use Throwable;

class ExceptionHandler
{
    public function __construct(private Logger $logger, private RequestId $requestId)
    {}

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
            $this->logger->warning(
                $exception->getMessage(),
                [
                    'request_id' => $this->requestId->get(),
                    'exception' => $exception::class,
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'method' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
                    'url' => $_SERVER['REQUEST_URI'] ?? 'UNKNOWN',
                ]
            );

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

        $this->logger->error(
            $exception->getMessage(),
            [
                'request_id' => $this->requestId->get(),
                'exception' => $exception::class,
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'method' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
                'url' => $_SERVER['REQUEST_URI'] ?? 'UNKNOWN',
            ]
        );

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