<?php

declare(strict_types = 1);

namespace App\Repositories;

interface ProductRepositoryInterface 
{
    public function findById(int $id): ?array;
    public function create(array $data): array;
    public function all(): array;
}