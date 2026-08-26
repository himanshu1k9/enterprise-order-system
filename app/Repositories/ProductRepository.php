<?php

declare(strict_types = 1);

namespace App\Repositories;

use PDO;

class ProductRepository implements ProductRepositoryInterface {
    public function __construct(private PDO $pdo)
    {}

    /**
     * Methid to get product by id
     */
    public function findById(int $id): ?array
    {
        $stm = $this->pdo->prepare("SELECT * FROM `products` WHERE id = :id");
        $stm->execute(['id' => $id]);
        $products = $stm->fetch(PDO::FETCH_ASSOC);
        // var_dump($products); exit;
        return $products ?: NULL;
    }
}