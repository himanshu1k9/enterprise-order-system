<?php

declare(strict_types = 1);

namespace App\Services;

use App\Repositories\PasswordResetTokenRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use RuntimeException;

class PasswordResetService
{
    public function __construct(
        private PasswordResetTokenRepositoryInterface $passwordRepo,
        private UserRepositoryInterface $userRepo
    ) {}

    /**
     * Generate a new password reset token.
     *
     * Returns the RAW token.
     *
     * The database stores only its SHA-256 hash.
     */
    public function createResetToken(string $email): string|false
    {
        $user = $this->userRepo->findByEmail($email);
        if(!$user) {
            return false;
        }

        $userId = (int) $user['id'];
        /*
        /---------------------------------------------------------------------------
        | Invalidate Previous token if exists
        |---------------------------------------------------------------------------
        */
        $this->passwordRepo->deleteUserTokens($userId);

        /*
        |--------------------------------------------------------------------------
        | Generate cryptographically secure random token
        |--------------------------------------------------------------------------
        */
        $rowToken = hex2bin(random_bytes(32));

        /*
        |--------------------------------------------------------------------------
        | Never store the raw token
        |--------------------------------------------------------------------------
        */
        $tokenHash = hash('sha256', $rowToken);

        /*
        |--------------------------------------------------------------------------
        | Token lifetime
        |--------------------------------------------------------------------------
        |
        | Example: 30 minutes
        */
        $expires_at = date('Y-m-d H:i:s', time() + (30 * 60));

        $this->passwordRepo->create($userId, $tokenHash, $expires_at);
        return $rowToken;
    }

    /**
     * Validate reset token and change password.
    */
    public function passwordReset(string $rowToken, string $newPassword): bool
    {
        /*
        |--------------------------------------------------------------------------
        | Convert submitted raw token into its database hash
        |--------------------------------------------------------------------------
        */
        $tokenHash = hash('sha256', $rowToken);

        /*
        |--------------------------------------------------------------------------
        | Find token that is:
        |
        | 1. Correct
        | 2. Not used
        | 3. Not expired
        |--------------------------------------------------------------------------
        */
        $token = $this->passwordRepo->findValidToken($tokenHash);
        if($token === false) {
            return false;
        }

        $userId = (int) $token['user_id'];
        /*
        |--------------------------------------------------------------------------
        | Hash the NEW PASSWORD
        |--------------------------------------------------------------------------
        */
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        if($passwordHash === false) {
            throw new RuntimeException('Unable to hash password.');
        }

        /*
        |--------------------------------------------------------------------------
        | Update user password
        |--------------------------------------------------------------------------
        */
        $updated = $this->userRepo->updatePassword($userId, $passwordHash);
        if(!$updated) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Consume reset token
        |--------------------------------------------------------------------------
        |
        | Once used, this token can never be reused.
        |
        */
        return $this->passwordRepo->markAsUsed((int) $token['id']);
    }
}