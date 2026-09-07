<?php

declare(strict_types = 1);

namespace App\DTO;

use App\Http\Request;

readonly class UpdateProductData
{
    public function __construct(
        public string $name,
        public string $sku,
        public ?string $description,
        public float $price,
        public int $stock,
        public string $status
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: (string) $request->input('name'),
            price: (float) $request->input('price'),
            stock: (int) $request->input('stock'),
            description: (string) $request->input('description'),
            sku: (string) $request->input('sku'),
            status: (string) $request->input('status') ?? 'active'
        );
    }
}