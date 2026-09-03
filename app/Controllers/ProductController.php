<?php

declare(strict_types = 1);

namespace App\Controllers;

// use App\DTO\CreateProductData;
// use App\Exceptions\NotFoundException;

use App\DTO\PaginationData;
use App\DTO\ProductFilterData;
use App\Http\Request;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\ProductIndexRequest;
use App\Http\Response;
// use App\Repositories\ProductRepositoryInterface;
use App\Services\ProductService;
// use App\Validation\Validator;
// use Exception;

class ProductController
{
    public function __construct(
        // private ProductRepositoryInterface $product,
        private Request $request,
        private ProductService $productService
        ) {}

    public function index(): Response
    {
        $query = new ProductIndexRequest($this->request);
        $query->validate();
        // $page = (int) $this->request->query('page', 1);
        // $limit = (int) $this->request->query('limit', 10);
        // $status = $this->request->query('status');
        // $search = $this->request->query('search');
        // $page = isset($_GET) && isset($_GET['page']) ? $_GET['page'] : NULL;
        // $limit = isset($_GET) && isset($_GET['limit']) ? $_GET['limit'] : NULL;
        // echo 'Product List' . PHP_EOL;
        // echo 'Page: ' . $page . PHP_EOL;
        // echo 'Limit: ' . $limit . PHP_EOL;

        $pagination = new PaginationData(page: $query->page(), limit: $query->limit());
        $filters = new ProductFilterData(status: $query->status(), search: $query->search());
        $products = $this->productService->paginate($pagination, $filters);
        return Response::json([
            "success" => true,
            'data' => $products
        ], 200);
        // throw new Exception("Something went wrong");
        // throw new NotFoundException("Product not found.");
    }

    /**
     * Endpoint to get product by id
     */
    public function show(string $id): Response
    {
        // $product = $this->product->findById((int) $id);
        $product = $this->productService->show((int) $id);
        if(!$product) {
            return Response::json(
                [
                    'success' => false,
                    'message' => 'Product not found.'
                ],
                404
            );
        }

        // if($product === null) {
        //     return Response::json([
        //         'success' => false,
        //         'message' => 'Product not found.'
        //     ], 404);
        // }

        return Response::json(
            [
                'success' => true,
                'data' => $product
            ],
            200
        );
        // echo "Product ID: {$id}";
    }

    public function store(): Response
    {
        // var_dump($this->request->json()); die;
        // $validator = new Validator(json_decode(file_get_contents('php://input'), true) ?? []);
        // $validator = new Validator($this->request->json());
        // $validator->validate([
        //     'name' => ['required', 'string'],
        //     'price' => ['required', 'mumeric', 'min:0'],
        //     'stock' => ['required', 'numeric', 'min:0']
        // ]);

        $request = new CreateProductRequest($this->request);
        $request->validate();

        // $productData = CreateProductData::fromRequest($this->request);
        $productData = $request->data();
        // var_dump($productData); die;
        $product = $this->productService->create($productData);
        return Response::json(
            [
                'success' => true,
                'message' => 'Product Created.',
                // 'data' => [
                //     'name' => $productData->name,
                //     'price' => $productData->price,
                //     'stock' => $productData->stock
                // ]
                'data' => $product
            ],201);
    }

    public function update(string $id): Response
    {
        // echo 'Updating product: ' . $id;
        return Response::json(
            [
                'success' => true,
                'message' => 'Product updated.',
                'id' => $id
            ]
        );
    }

    public function destroy(string $id): Response
    {
        // echo 'Deleting product: ' . $id;
        return Response::json(
            [
                'success' => true,
                'message' => 'Product deleted.',
                'id' => $id
            ]
        );
    }

    public function review(
        string $productId,
        string $reviewId
    ): Response {
        // echo "Product: {$productId}, Review: {$reviewId}";
        return Response::json(
            [
                'success' => true,
                'product_id' => $productId,
                'review_id' => $reviewId
            ]
        );
    }
}