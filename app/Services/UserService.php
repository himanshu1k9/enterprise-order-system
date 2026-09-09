<?php

declare(strict_types = 1);

namespace App\Services;

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
}