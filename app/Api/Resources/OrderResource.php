<?php

declare(strict_types = 1);

namespace App\Api\Resources;

final class OrderResource
{
    public static function make(array $order): array
    {
        $data = [
            'id' => (int) $order['id'],
            'status' => $order['status'],
            'total' => (float) $order['total']
        ];

        if(isset($order['user']) && is_array($order['user'])) {
            $data['customer'] = UserResource::make($order['data']);
        }

        if(isset($order['items']) && is_array($order['items'])) {
            $data['items'] = OrderItemResource::collection($order['items']);
        }

        return $data;
    }

    public static function collection(array $orders): array
    {
        return array_map([self::class, 'make'], $orders);
    }
}