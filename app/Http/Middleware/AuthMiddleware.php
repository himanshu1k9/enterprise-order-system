<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

use App\Auth\SessionManager;
use App\Http\ApiResponse;
use App\Http\Request;
use App\Http\Response;
use Override;

class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(private SessionManager $sessionManager)
    {}

    #[Override]
    public function handle(Request $request, callable $next): Response
    {
        // if(!isset($_SESSION['user_id'])) {
        if($this->sessionManager->userId() === null) {
            // Response::json(
            //     [
            //         'success' => false,
            //         'message' => 'Unauthenticated'
            //     ],
            //     401
            // );
            // return next($request);
            return ApiResponse::error("Authentication Required.", 401);
        }
        return $next($request);
    }
}