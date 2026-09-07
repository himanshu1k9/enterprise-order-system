<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Auth\UserRole;

class ProductDeleteMiddleware extends RoleMiddleware
{
    protected function allowedRoles(): array
    {
        return [
            UserRole::SUPER_ADMIN,
            UserRole::ADMIN,
        ];
    }
}