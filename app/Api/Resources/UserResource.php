<?php

declare(strict_types = 1);

namespace App\Api\Resources;

final class UserResource
{
    public static function make(array $user): array
    {
        return [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
    }

    public static function collection(array $users): array
    {
        return array_map([self::class, 'make'], $users);
    }
}