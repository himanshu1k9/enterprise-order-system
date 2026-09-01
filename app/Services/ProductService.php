<?php

declare(strict_types = 1);

namespace App\Services;

use App\Database\TransactionManager;
use App\DTO\CreateProductData;
use App\Repositories\ProductRepositoryInterface;

class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $productRepo,
        private TransactionManager $transaction
        )
    {}

    public function create(CreateProductData $data): array
    {
        // return [
        //     'name' => $data->name,
        //     'price' => $data->price,
        //     'stock' => $data->stock
        // ];
        return $this->transaction->run(function() use($data) {
            return $this->productRepo->create($data);
        });
    }

    public function show(int $id): array
    {
        return $this->productRepo->findById($id);
    }

    public function all(): array
    {
        return $this->productRepo->all();
    }
}