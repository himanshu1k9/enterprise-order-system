<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;
use Override;

class AuthMiddleware implements MiddlewareInterface
{
    #[Override]
    public function handle(Request $request, callable $next): Response
    {
        if(!isset($_SESSION['user_id'])) {
            Response::json(
                [
                    'success' => false,
                    'message' => 'Unauthenticated'
                ],
                401
            );
            return next($request);
        }
        return $next($request);
    }
}