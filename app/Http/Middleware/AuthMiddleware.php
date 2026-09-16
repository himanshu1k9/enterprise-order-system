<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

use App\Auth\SessionManager;
use App\Http\ApiResponse;
use App\Http\Request;
use App\Http\Response;
use App\Repositories\UserRepositoryInterface;
use Override;

class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private SessionManager $sessionManager,
        private UserRepositoryInterface $user
        ) {}

    #[Override]
    public function handle(Request $request, callable $next): Response
    {
        $userId = $this->sessionManager->userId();
        if($userId === null) {
            return ApiResponse::error("Authentication required.", 401);
        }

        $sessionVersion = $this->sessionManager->sessionversion();
        if($sessionVersion === null) {
            $this->sessionManager->logout();
            return ApiResponse::error("Authentication required.", 401);
        }

        $databaseVersion = $this->user->getSessionVersion($userId);
        if($sessionVersion !== $databaseVersion) {
            $this->sessionManager->logout();
            return ApiResponse::error("Session expired please login again.", 401);
        }
        // if(!isset($_SESSION['user_id'])) {
        // if($this->sessionManager->userId() === null) {
            // Response::json(
            //     [
            //         'success' => false,
            //         'message' => 'Unauthenticated'
            //     ],
            //     401
            // );
            // return next($request);
            // return ApiResponse::error("Authentication Required.", 401);
        // }
        return $next($request);
    }
}