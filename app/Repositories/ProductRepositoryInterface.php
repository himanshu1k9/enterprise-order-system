<?php

declare(strict_types = 1);

namespace App\Repositories;

use App\DTO\CreateProductData;
use App\DTO\PaginationData;
use App\DTO\ProductFilterData;

interface ProductRepositoryInterface
{
    public function findById(int $id): ?array;
    public function create(CreateProductData $data): array;
    public function all(): array;
    public function paginate(PaginationData $pagination, ProductFilterData $productFilters): array;
}