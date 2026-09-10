<?php

declare(strict_types = 1);

namespace App\Repositories;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): array|false;
    public function findById(int $id): array|false;
    public function create(string $name, string $email, string $passwordHash, string $role = 'customer'): int;
    public function updatePassword(int $userId, string $passwordHash): bool;
}