<?php

declare(strict_types = 1);

namespace App\Repositories\Contracts;

interface OrderRepositoryInterface
{
    public function create(array $data): int;
    public function createItems(array $data): int;
    public function findById(int $id): ?array;
    public function findItemsById(int $id): array;
    public function updateStatus(int $orderId, string $status): bool;
    public function findByIdForUpdate(int $id): ?array;
}