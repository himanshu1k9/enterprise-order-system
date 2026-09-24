<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

use App\Auth\JwtAuthenticatedUserResolver;
use App\Auth\JwtCurrentUser;
use App\Auth\RolePermissions;
use App\Http\ApiResponse;
use App\Http\Request;
use App\Http\Response;

class ApiPermissionMiddleware
{
    public function __construct(
        private JwtCurrentUser $jwtUser,
        private JwtAuthenticatedUserResolver $resolver
    ) {}

    public function handle(Request $request, callable $next, string $permission): Response
    {
        $userId = $this->jwtUser->id();
        $user = $this->resolver->resolve($userId);

        $role = $user['role'] ?? '';

        if ($role === '') {
            return ApiResponse::error('Authentication required.', 401);
        }

        if (!RolePermissions::has($role, $permission)) {
            return ApiResponse::error(
                'You do not have permission to perform this action.',
                403
            );
        }

        return $next($request);
    }
}