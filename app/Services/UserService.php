<?php

declare(strict_types = 1);

namespace App\Services;

use App\Exceptions\AuthenticationException;
use App\Exceptions\ConflictException;
use App\Repositories\UserRepositoryInterface;

class UserService
{
    public function __construct(private UserRepositoryInterface $repo)
    {}

    /**
     * Service to register User
     *
     * @param string $name
     * @param string $email
     * @param string $password
     * @return integer
     */
    public function register(string $name, string $email, string $password): int
    {
        $existingUser = $this->repo->findByEmail($email);
        if($existingUser !== false) {
            throw new ConflictException("Email is already registered.");
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        return $this->repo->create($name, $email, $passwordHash);
    }

    /**
     * Method to login the user
     *
     * @param string $email
     * @param string $password
     * @return array
     */
    public function login(string $email, string $password): array
    {
        $user = $this->repo->findByEmail($email);

        if($user === false) {
            throw new AuthenticationException("Invalid Credentials.");
        }

        if(!password_verify($password, $user['password'])) {
            throw new AuthenticationException("Invalid Credentials");
        }

        return $user;
    }
}