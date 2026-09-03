<?php

declare(strict_types = 1);

namespace App\Services;

use App\Database\TransactionManager;
use App\DTO\CreateProductData;
use App\DTO\PaginationData;
use App\DTO\ProductFilterData;
use App\Repositories\ProductRepositoryInterface;

class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $productRepo,
        private TransactionManager $transaction
        )
    {}

    /**
     * Service to create the product
     *
     * @param CreateProductData $data
     * @return array
     */
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

    /**
     * Service to get Single product
     *
     * @param integer $id
     * @return array
     */
    public function show(int $id): array
    {
        return $this->productRepo->findById($id);
    }

    /**
     * Service to get all products
     *
     * @return array
     */
    public function all(): array
    {
        return $this->productRepo->all();
    }

    /**
     * Service to get paginated product data
     *
     * @param PaginationData $pagination
     * @param ProductFilterData $filters
     * @return array
     */
    public function paginate(PaginationData $pagination, ProductFilterData $filters): array
    {
        return $this->productRepo->paginate($pagination, $filters);
    }
}