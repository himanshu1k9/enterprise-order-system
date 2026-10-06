<?php

declare(strict_types = 1);

namespace App\Auth;

use RuntimeException;

class JwtCurrentUser
{
    private ?array $user = null;
    public function set(array $user): void
    {
        $this->user = $user;
    }

    public function id(): int
    {
        if($this->user === null) {
            throw new RuntimeException('No Authenticated user.');
        }
        return (int) $this->user['id'];
    }

    public function user(): array
    {
        if($this->user === null) {
            throw new RuntimeException('No authenticated user.');
        }
        return $this->user;
    }

    public function role(): ?string
    {
        return $this->user['role'] ?? null;
    }

    public function check(): bool
    {
        return $this->user !== null;
    }

    public function clear(): void
    {
        $this->user = null;
    }
}