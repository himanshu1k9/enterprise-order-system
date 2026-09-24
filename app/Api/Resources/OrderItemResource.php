<?php

declare(strict_types = 1);

namespace App\Api\Resources;

final class OrderItemResource
{
    public static function make(array $item): array
    {
        return [
            'id' => (int) $item['id'],
            'product_name' => $item['product_name'],
            'quantity' => (int) $item['quantity'],
            'unit_price' => (float) $item['price'],
            'subtotal' => (float) $item['subtotal'],
        ];
    }

    public static function collection(array $items): array
    {
        return array_map([self::class, 'make'], $items);
    }
}