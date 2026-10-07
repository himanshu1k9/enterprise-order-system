<?php

declare(strict_types = 1);

namespace App\Repositories;

use App\DTO\CreateProductData;
use App\DTO\PaginationData;
use App\DTO\ProductFilterData;
use App\DTO\ProductSortData;
use App\DTO\UpdateProductData;

interface ProductRepositoryInterface
{
    public function findById(int $id): ?array;
    public function create(CreateProductData $data): array;
    public function all(): array;
    public function paginate(PaginationData $pagination, ProductFilterData $productFilters, ProductSortData $sort): array;
    public function update(int $id, UpdateProductData $data): bool;
    public function delete(int $id): bool;
    public function findForOrder(int $productId): ?array;
    public function findForUpdate(int $productId): ?array;
    public function decreaseStock(int $productId, int $quantity): bool;
}