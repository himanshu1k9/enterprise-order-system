<?php

declare(strict_types = 1);

namespace App\Auth;

use App\Repositories\UserRepositoryInterface;
use RuntimeException;

class JwtAuthenticatedUserResolver
{
    public function __construct(private UserRepositoryInterface $user)
    {}

    /**
     * Method to authenticate Jwt user
     *
     * @param integer $userId
     * @return array
     */
    public function resolve(int $userId): array
    {
        $user = $this->user->findById($userId);
        if($user === false) {
            throw new RuntimeException('Authenticated user not found');
        }

        if($user['status'] !== 'active') {
            throw new RuntimeException('User account is not active');
        }

        return $user;
    }
}