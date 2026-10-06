<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

// use App\Auth\JwtAuthenticatedUserResolver;
use App\Auth\JwtCurrentUser;
use App\Auth\RolePermissions;
use App\Http\ApiResponse;
use App\Http\Request;
use App\Http\Response;

class ApiPermissionMiddleware
{
    public function __construct(
        private JwtCurrentUser $jwtUser,
        // private JwtAuthenticatedUserResolver $resolver
    ) {}

    public function handle(Request $request, callable $next, string $permission): Response
    {
        if (!$this->jwtUser->check()) {
            return ApiResponse::error('Authentication required.',401);
        }

        $role = $this->jwtUser->role();
        if($role === null || $role === '') {
            return ApiResponse::error('User role is not configured.', 403);
        }

        // $userId = $this->jwtUser->id();
        // // $user = $this->resolver->resolve($userId);
        // $user = $request->getAttribute('auth_user');
        // if (!is_array($user)) {
        //     return ApiResponse::error(
        //         'Authenticated user could not be resolved.',
        //         401
        //     );
        // }

        // $role = $user['role'] ?? '';

        // if ($role === '') {
        //     return ApiResponse::error('Authentication required.', 401);
        // }

        if (!RolePermissions::has($role, $permission)) {
            return ApiResponse::error(
                'You do not have permission to perform this action.',
                403
            );
        }

        return $next($request);
    }
}