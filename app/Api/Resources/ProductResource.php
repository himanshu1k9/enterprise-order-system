<?php

declare(strict_types = 1);

namespace App\Api\Resources;

final class ProductResource
{
    public static function make(array $product): array
    {
        $data = [
            'id' => (int) $product['id'],
            'name' => $product['name'],
            'price' => (float) $product['price']
        ];

        if(isset($product['category']) && is_array($product['category'])) {
            $data['category'] = CategoryResource::make($product['category']);
        }

        return $data;
    }

    public static function collection(array $products): array
    {
        return array_map([self::class, 'make'], $products);
    }
}