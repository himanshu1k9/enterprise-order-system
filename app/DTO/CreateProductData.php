<?php

declare(strict_types = 1);

namespace App\DTO;

use App\Http\Request;

final class CreateProductData
{
    public function __construct(
        public readonly string $name,
        public readonly float $price,
        public readonly int $stock
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: (string) $request->input('name'),
            price: (float) $request->input('price'),
            stock: (int) $request->input('stock')
        );
    }
}