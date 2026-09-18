<?php

declare(strict_types = 1);

namespace App\Auth;

use RuntimeException;

class JwtCurrentUser
{
    private ?int $userId = null;

    public function set(int $userId): void
    {
        $this->userId = $userId;
    }

    public function id(): int
    {
        if($this->userId === null) {
            throw new RuntimeException('No Authenticated user.');
        }

        return $this->userId;
    }

    public function check(): bool
    {
        return $this->userId !== null;
    }

    public function clear(): void
    {
        $this->userId = null;
    }
}