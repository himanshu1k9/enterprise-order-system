<?php

declare(strict_types=1);

namespace App\Repositories;

interface PasswordResetTokenRepositoryInterface
{
    public function create(int $userId, string $tokenHash, string $expiresAt): int;

    public function findValidToken(string $tokenHash): array|false;

    public function markAsUsed(int $id): bool;

    public function deleteUserTokens(int $userId): bool;
}