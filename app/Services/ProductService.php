<?php

declare(strict_types = 1);

namespace App\Services;

use App\DTO\CreateProductData;

class ProductService
{
    public function create(CreateProductData $data): array
    {
        return [
            'name' => $data->name,
            'price' => $data->price,
            'stock' => $data->stock
        ];
    }
}