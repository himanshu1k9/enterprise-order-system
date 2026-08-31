<?php

declare(strict_types = 1);

namespace App\Exceptions;

use App\Http\Response;
use Throwable;

class ExceptionHandler
{
    public function handle(Throwable $exception): Response
    {
        if($exception instanceof NotFoundException) {
            return Response::json(
                [
                    'success' => false,
                    'message' => $exception->getMessage()
                ], 404
            );
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