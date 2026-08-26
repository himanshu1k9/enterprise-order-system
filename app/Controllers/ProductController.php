<?php

declare(strict_types = 1);

namespace App\Controllers;

class ProductController {
    public function index(): void {
        $page = isset($_GET) && isset($_GET['page']) ? $_GET['page'] : NULL;
        $limit = isset($_GET) && isset($_GET['limit']) ? $_GET['limit'] : NULL;
        echo 'Product List' . PHP_EOL;
        echo 'Page: ' . $page . PHP_EOL;
        echo 'Limit: ' . $limit . PHP_EOL;
    }

    public function show(string $id): void
    {
        echo "Product ID: {$id}";
    }

    public function review(
        string $productId,
        string $reviewId
    ): void {
        echo "Product: {$productId}, Review: {$reviewId}";
    }
}