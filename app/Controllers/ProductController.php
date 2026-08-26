<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\Http\Response;
use App\Repositories\ProductRepository;

class ProductController 
{
    public function __construct(private ProductRepository $product)
    {
    }

    public function index(): void 
    {
        $page = isset($_GET) && isset($_GET['page']) ? $_GET['page'] : NULL;
        $limit = isset($_GET) && isset($_GET['limit']) ? $_GET['limit'] : NULL;
        echo 'Product List' . PHP_EOL;
        echo 'Page: ' . $page . PHP_EOL;
        echo 'Limit: ' . $limit . PHP_EOL;
    }

    /**
     * Endpoint to get product by id
     */
    public function show(string $id): void
    {
        $product = $this->product->findById((int) $id);
        if(!$product) {
            Response::json(
                [
                    'success' => false,
                    'message' => 'Product not found.'
                ],
                404
            );
            return;
        }

        Response::json(
            [
                'success' => true,
                'data' => $product
            ],
            200
        );
        // echo "Product ID: {$id}";
    }

    public function store(): void {
        echo 'Product Created.';
    }

    public function update(string $id): void {
        echo 'Updating product: ' . $id;
    }

    public function destroy(string $id): void {
        echo 'Deleting product: ' . $id;
    }

    public function review(
        string $productId,
        string $reviewId
    ): void {
        echo "Product: {$productId}, Review: {$reviewId}";
    }
}