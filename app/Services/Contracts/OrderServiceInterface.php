<?php

declare(strict_types = 1);

namespace App\Services\Contracts;

interface OrderServiceInterface
{
    public function createOrder(int $user_id, array $items): array;
    public function updateStatus(int $orderId, string $status): array;
}