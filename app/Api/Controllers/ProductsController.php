<?php

declare(strict_types = 1);

namespace App\Api\Controllers;

use App\DTO\PaginationData;
use App\DTO\ProductFilterData;
use App\DTO\ProductSortData;
use App\Http\ApiResponse;
use App\Http\Request;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\ProductIndexRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Response;
use App\Services\ProductService;

class ProductsController
{
    public function __construct(
        private ProductService $productServive,
        private Request $request
    ) {}

    /**
     * Endpoint to fetch products with pagination;
     *
     * @return Response
     */
    public function index(): Response
    {
        $query = new ProductIndexRequest($this->request);
        $query->validate();

        $pagination = new PaginationData(page: $query->page(), limit: $query->limit());
        $filters = new ProductFilterData(status: $query->status(), search: $query->search());
        $sort = new ProductSortData(sort: $query->sort(), direction: $query->direction());

        $response = $this->productServive->paginate($pagination, $filters, $sort);

        $products = $response['data'];
        $metaData = [
            'current_page' => $response['page'],
            'per_page' => $response['limit'],
            'total' => $response['total'],
            'last_page' => $response['total_pages']
        ];

        return ApiResponse::success('List of all products', [
            'success' => true,
            'data' => $products,
            'meta' => $metaData
        ], 200);
    }

    /**
     * Endpoint to fetch single product
     *
     * @param integer $productId
     * @return Response
     */
    public function productById(int $productId): Response
    {
        $product = $this->productServive->show($productId);
        return ApiResponse::success('Product found:',$product);
    }

    /**
     * Endpoint to store Product
     *
     * @return Response
     */
    public function store(): Response
    {
        $request = new CreateProductRequest($this->request);
        $request->validate();

        $data = $request->data();

        $product = $this->productServive->create($data);

        return ApiResponse::success(message: "Product Created",
            data: $product, status: 201);
    }

    /**
     * Endpoint to Update the product
     *
     * @param integer $productId
     * @return Response
     */
    public function update(int $productId): Response
    {
        $request = new ProductUpdateRequest($this->request);
        $request->validate();
        $data = $request->data();

        $this->productServive->update($productId, $data);

        return ApiResponse::success(message: "Product Updated", data: ['id' => $productId]);
    }

    /**
     * Endpoint to delete the product
     *
     * @param integer $productId
     * @return Response
     */
    public function destroy(int $productId): Response
    {
        $this->productServive->delete($productId);
        return ApiResponse::success('Product Deleted successfully.', [
            'id' => $productId
        ]);
    }
}