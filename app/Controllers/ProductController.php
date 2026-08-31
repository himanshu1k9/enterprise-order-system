<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\Exceptions\NotFoundException;
use App\Http\Request;
use App\Http\Response;
use App\Repositories\ProductRepositoryInterface;
use App\Validation\Validator;
use Exception;

class ProductController
{
    public function __construct(private ProductRepositoryInterface $product)
    {
    }

    public function index(): Response
    {
        $page = isset($_GET) && isset($_GET['page']) ? $_GET['page'] : NULL;
        $limit = isset($_GET) && isset($_GET['limit']) ? $_GET['limit'] : NULL;
        // echo 'Product List' . PHP_EOL;
        // echo 'Page: ' . $page . PHP_EOL;
        // echo 'Limit: ' . $limit . PHP_EOL;
        // return Response::json([
        //     "success" => true,
        //     'data' => [
        //         'message' => 'Product List',
        //         'page' => $page,
        //         'limit' => $limit
        //     ]
        // ]);
        // throw new Exception("Something went wrong");
        throw new NotFoundException("Product not found.");
    }

    /**
     * Endpoint to get product by id
     */
    public function show(string $id): Response
    {
        $product = $this->product->findById((int) $id);
        if(!$product) {
            return Response::json(
                [
                    'success' => false,
                    'message' => 'Product not found.'
                ],
                404
            );
        }

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
        $validator = new Validator(json_decode(file_get_contents('php://input'), true) ?? []);
        $validator->validate([
            'name' => ['required', 'string'],
            'price' => ['required', 'mumeric', 'min:0'],
            'stock' => ['required', 'numeric', 'min:0']
        ]);

        return Response::json(
            [
                'success' => true,
                'message' => 'Product Created.'
            ],
            201
        );
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