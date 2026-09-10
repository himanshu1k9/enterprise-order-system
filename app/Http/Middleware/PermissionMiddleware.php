<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

use App\Auth\CurrentUser;
use App\Http\ApiResponse;
use App\Http\Request;
use App\Http\Response;

class PermissionMiddleware
{
    public function __construct(private CurrentUser $user)
    {}

    public function handle(Request $request, callable $next, string $permission): Response
    {
        if($this->user->role() === null || $this->user->role() === '') {
            return ApiResponse::error('Authentication required.', 401);
        }

        if(!$this->user->can($permission)) {
            return ApiResponse::error(
                'You do not have permission to perform this action.',
                403
            );
        }

        return $next($request);
    }
}