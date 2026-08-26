<?php

declare(strict_types = 1);

namespace App\Repositories;

use PDO;

class ProductRepository {
    public function __construct(private PDO $pdo)
    {}

    public function findById(int $id): ?array
    {
        $stm = "SELECT * FROM products WHERE id = :id";
        $this->pdo->prepare($stm);
    }
}