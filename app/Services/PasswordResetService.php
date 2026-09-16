<?php

declare(strict_types = 1);

namespace App\Services;

use App\Database\TransactionManager;
use App\Exceptions\RateLimitException;
use App\Repositories\PasswordResetTokenRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use App\Security\RateLimiter;
use RuntimeException;

class PasswordResetService
{
    protected int $decaySeconds = 600;
    protected int $maxAttempts = 5;
    public function __construct(
        private PasswordResetTokenRepositoryInterface $passwordRepo,
        private UserRepositoryInterface $userRepo,
        private TransactionManager $transaction,
        private RateLimiter $limitter
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
        /*
        |--------------------------------------------------------------------------
        | Normalize email
        |--------------------------------------------------------------------------
        */
        $email = strtolower($email);

        /*
        |--------------------------------------------------------------------------
        | IP-based rate limit
        |--------------------------------------------------------------------------
        */
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $ipKey = 'forgot-password:ip:' . $clientIp;

        /*
        |--------------------------------------------------------------------------
        | Account-based rate limit
        |--------------------------------------------------------------------------
        |
        | Never put the actual email address into the limiter key.
        |
        */
        $emailHash = hash('sha256',$email);
        $accountKey = 'forgot-password:account:' . $emailHash;

        if($this->limitter->tooManyAttempts($ipKey, $this->maxAttempts, $this->decaySeconds)) {
            throw new RateLimitException('Too many requests. Please try again later.');
        }

        /*
        |--------------------------------------------------------------------------
        | Check account limit
        |--------------------------------------------------------------------------
        */
        if($this->limitter->tooManyAttempts($accountKey, $this->maxAttempts, $this->decaySeconds)) {
            throw new RateLimitException('Too many requests. Please try again later.');
        }

        /*
        |--------------------------------------------------------------------------
        | Count this request
        |--------------------------------------------------------------------------
        |
        | We hit BOTH keys.
        |
        */
        $this->limitter->hit($ipKey);
        $this->limitter->hit($accountKey);

        /*
        |--------------------------------------------------------------------------
        | Find user
        |--------------------------------------------------------------------------
        */
        $user = $this->userRepo->findByEmail($email);
        if(!$user) {
            // if($_ENV['APP_ENV'] === 'development') {
            //     throw new RuntimeException("Account not found.");
            // } else {
            //     return false;
            // }
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
        $rowToken = bin2hex(random_bytes(32));

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
    public function passwordReset(string $rowToken, string $newPassword, string $confirmPassword): bool
    {
        /*
        |--------------------------------------------------------------------------
        | IP RATE LIMIT
        |--------------------------------------------------------------------------
        */

        $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        $ipKey = 'reset-password:ip:' . $clientIp;

        if (
            $this->limitter->tooManyAttempts(
                $ipKey,
                $this->maxAttempts,
                $this->decaySeconds
            )
        ) {
            throw new RateLimitException(
                'Too many requests. Please try again later.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Count this request
        |--------------------------------------------------------------------------
        */

        $this->limitter->hit($ipKey);
        if($newPassword !== $confirmPassword) {
            throw new RuntimeException("Confirm password mismatched.");
        }
        /*
        |--------------------------------------------------------------------------
        | Convert submitted raw token into its database hash
        |--------------------------------------------------------------------------
        */
        $tokenHash = hash('sha256', $rowToken);

        /*
        |--------------------------------------------------------------------------
        | TOKEN RATE LIMIT
        |--------------------------------------------------------------------------
        */
        $tokenKey = 'reset-password:token:' . $tokenHash;

        if (
            $this->limitter->tooManyAttempts(
                $tokenKey,
                $this->maxAttempts,
                $this->decaySeconds
            )
        ) {
            throw new RateLimitException(
                'Too many requests. Please try again later.'
            );
        }
        $this->limitter->hit($tokenKey);

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
        return $this->transaction->run(function() use($userId, $passwordHash, $token): bool {
            /*
            |--------------------------------------------------------------------------
            | Update password
            |--------------------------------------------------------------------------
            */
            $updated = $this->userRepo->updatePassword($userId, $passwordHash);
            if(!$updated) {
                return false;
            }

            /**
             * Incrementing session version
             */
            $this->userRepo->incrementSessionVersion($userId);

            /*
            |--------------------------------------------------------------------------
            | Consume reset token
            |--------------------------------------------------------------------------
            */
            $used = $this->passwordRepo->markAsUsed((int) $token['id']);

            if (!$used) {
                throw new \RuntimeException(
                    'Unable to consume reset token.'
                );
            }

            return true;
        });
    }
}