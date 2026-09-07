<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

use App\Auth\CurrentUser;
use App\Http\ApiResponse;
use App\Http\Request;
use App\Http\Response;
use Override;

class RoleMiddleware implements MiddlewareInterface
{
    public function __construct(private CurrentUser $user)
    {}

    #[Override]
    public function handle(Request $request, callable $next): Response
    {
        $role = $this->user->role();
        if($role === null) {
            return ApiResponse::error('Authentication required.', 401);
        }

        if (!in_array($role, $this->allowedRole(), true)) {
            return ApiResponse::error(
                'You do not have permission to perform this action.',
                403
            );
        }
        return $next($request);
    }

    protected function allowedRole(): array
    {
        return [];
    }
}