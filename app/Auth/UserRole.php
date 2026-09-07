<?php

declare(strict_types = 1);

namespace App\Auth;

class UserRole
{
    public const SUPER_ADMIN = 'super_admin';
    public const ADMIN = 'admin';
    public const STAFF = 'staff';
    public const CUSTOMER = 'customer';

    public static function all(): array
    {
        return [
            self::SUPER_ADMIN,
            self::ADMIN,
            self::STAFF,
            self::CUSTOMER
        ];
    }
}