<?php

declare(strict_types = 1);

namespace App\Auth;

use App\Repositories\UserRepositoryInterface;

class CurrentUser
{
    public function __construct(
        private SessionManager $sessionManager,
        private UserRepositoryInterface $users
    ) {}

    /**
     * Method to Returning if user exists or not
     *
     * @return array|null
     */
    public function user(): array|null
    {
        $userId = $this->sessionManager->userId();
        if($userId === null) {
            return null;
        }

        return $this->users->findById($userId);
    }

    /**
     * Method to return role of existing user
     *
     * @return string|null
     */
    public function role(): ?string
    {
        $user = $this->user();
        if($user === false) {
            return null;
        }

        return $user['role'] ?? null;
    }

    /**
     * Method to returs if user can the task or not
     *
     * @param string $permission
     * @return boolean
     */
    public function can(string $permission): bool
    {
        $role = $this->role();
        if($role === null) {
            return false;
        }

        return RolePermissions::has($role, $permission);
    }
}