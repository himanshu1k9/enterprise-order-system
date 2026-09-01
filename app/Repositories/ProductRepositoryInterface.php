<?php

declare(strict_types = 1);

namespace App\Repositories;

use App\DTO\CreateProductData;

interface ProductRepositoryInterface
{
    public function findById(int $id): ?array;
    public function create(CreateProductData $data): array;
    public function all(): array;
}