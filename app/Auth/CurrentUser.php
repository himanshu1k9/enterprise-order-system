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

    public function user(): array|null
    {
        $userId = $this->sessionManager->userId();
        if($userId === null) {
            return null;
        }

        return $this->users->findById($userId);
    }

    public function role(): ?string
    {
        $user = $this->user();
        if($user === false) {
            return null;
        }

        return $user['role'] ?? null;
    }
}