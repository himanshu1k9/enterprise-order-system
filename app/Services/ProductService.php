<?php

declare(strict_types = 1);

namespace App\Services;

use App\Database\TransactionManager;
use App\DTO\CreateProductData;
use App\DTO\PaginationData;
use App\DTO\ProductFilterData;
use App\DTO\ProductSortData;
use App\DTO\UpdateProductData;
use App\Exceptions\NotFoundException;
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
    public function show(int $id): array|null
    {
        $product = $this->productRepo->findById($id);
        // var_dump($product); die;
        if($product === false || $product === null) {
            throw new NotFoundException("Product with id {$id} not found");
        }

        // return $this->productRepo->findById($id);
        return $product;
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
    public function paginate(PaginationData $pagination, ProductFilterData $filters, ProductSortData $sort): array
    {
        return $this->productRepo->paginate($pagination, $filters, $sort);
    }

    /**
     * Service layer to update the product
     *
     * @param integer $id
     * @param UpdateProductData $data
     * @return void
     */
    public function update(int $id, UpdateProductData $data): void
    {
        $updated = $this->transaction->run(fn() => $this->productRepo->update($id, $data));
        // return $this->productRepo->update($id, $data);

        if(!$updated) {
            throw new NotFoundException("Product with id {$id} not found.");
        }
    }

    /**
     * Service to delete the product
     *
     * @param integer $id
     * @return void
     */
    public function delete(int $id): void
    {
        $deleted = $this->transaction->run(fn() => $this->productRepo->delete($id));
        // return $this->productRepo->delete($id);

        if(!$deleted) {
            throw new NotFoundException("Product with id {$id} not found.");
        }

        // return true;
    }
}