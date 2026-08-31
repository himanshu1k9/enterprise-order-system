<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;
use Override;

class LoggingMiddleware implements MiddlewareInterface
{
    #[Override]
    public function handle(Request $request, callable $next): Response
    {
        // echo 'Before request <br>';
        $response = $next($request);
        // echo 'After request </br>';
        return $response;
    }
}