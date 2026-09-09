<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

use App\Http\ApiResponse;
use App\Http\Request;
use App\Http\Response;
use App\Security\CsrfManager;
use Override;

class CsrfMiddleware implements MiddlewareInterface
{
    public function __construct(private CsrfManager $csrf)
    {}

    #[Override]
    public function handle(Request $request, callable $next): Response
    {
        $method = strtoupper($request->method());
        if(in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return next($request);
        }

        $token = $request->header('X-CSRF-TOKEN');
        if($token === null || !$this->csrf->validate($token)) {
            return ApiResponse::error('Invalid CSRF token', 419);
        }

        return $next($request);
    }
}