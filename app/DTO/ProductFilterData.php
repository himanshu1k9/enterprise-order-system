<?php

declare(strict_types = 1);

namespace App\DTO;

readonly class ProductFilterData
{
    public function __construct(
        public ?string $status = null,
        public ?string $search = null,
        public string $sort = 'created_at',
        public string $order = 'desc'
    ) {}
}