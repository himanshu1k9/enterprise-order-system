<?php

declare(strict_types = 1);

namespace App\Services;

use App\Auth\SessionManager;
use App\Exceptions\AuthenticationException;
use App\Exceptions\RateLimitException;
use App\Repositories\UserRepositoryInterface;
use App\Security\RateLimiter;

class AuthService
{
    private int $deckaySeconds = 300;
    private int $maxAttempts = 5;

    public function __construct(
        private UserRepositoryInterface $repo,
        private SessionManager $session,
        private RateLimiter $limitter
    ) {}

    /**
     * Method to login the user
     *
     * @param string $email
     * @param string $password
     * @return array
     */
    public function login(string $email, string $password): array
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $key = 'login' . $ip;

        if($this->limitter->tooManyAttempts($key, $this->maxAttempts, $this->deckaySeconds)) {
            throw new RateLimitException('Too many login attempts. Please try again later.');
        }

        $user = $this->repo->findByEmail($email);
        if(!$user || !password_verify($password, $user['password'])) {
            $this->limitter->hit($key);
            throw new AuthenticationException('Invalid Credentials.');
        }

        // if($user === false) {
        //     throw new AuthenticationException("Invalid Credentials.");
        // }

        // if(!password_verify($password, $user['password'])) {
        //     throw new AuthenticationException("Invalid Credentials");
        // }

        $this->limitter->clear($key);

        return $user;
    }
}