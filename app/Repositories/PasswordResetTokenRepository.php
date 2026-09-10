<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

class PasswordResetTokenRepository implements PasswordResetTokenRepositoryInterface
{
    public function __construct(private PDO $pdo)
    {}

    public function create(int $userId, string $tokenHash, string $expiresAt): int
    {
        $sql = "INSERT INTO password_reset_tokens (user_id, token_hash, expires_at)
                VALUES (:user_id, :token_hash), :expires_at)";

        $statement = $this->pdo->prepare($sql);
        $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $statement->bindParam(':token_hash', $tokenHash, PDO::PARAM_STR);
        $statement->bindParam(':expires_at', $expiresAt, PDO::PARAM_STR);
        $statement->execute();

        return (int) $this->pdo->lastInsertId();
    }

    public function findValidToken(string $tokenHash): array|false
    {
        $sql = "SELECT id, user_id, token_hash, expires_at, used_at, created_at FROM password_reset_tokens
                WHERE token_hash = :token_hash AND expires_at > NOW() AND used_at IS NULL LIMIT 1";

        $statement = $this->pdo->prepare($sql);
        $statement->bindParam(':token_hash', $tokenHash, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC) ?: false;
    }

    public function markAsUsed(int $id): bool
    {
        $sql = "UPDATE password_reset_tokens SET used_at = NOW() WHERE id = :id AND used_at IS NULL";

        $statement = $this->pdo->prepare($sql);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->rowCount() > 0;
    }

    public function deleteUserTokens(int $userId): bool
    {
        $sql = "DELETE FROM password_reset_tokens WHERE user_id = :user_id";

        $statement = $this->pdo->prepare($sql);
        $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->rowCount() > 0;
    }
}