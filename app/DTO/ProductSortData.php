<?php

declare(strict_types = 1);

namespace App\DTO;

readonly class ProductSortData
{
    public function __construct(
        public string $sort = 'id',
        public string $direction = 'desc'
    ) {}
}