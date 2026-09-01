<?php

declare(strict_types = 1);

namespace App\Repositories;

use App\DTO\CreateProductData;
use Override;
use PDO;

class ProductRepository implements ProductRepositoryInterface {
    public function __construct(private PDO $pdo)
    {}

    /**
     * Method to get single product by id
     *
     * @param integer $id
     * @return array|null
     */
    #[Override]
    public function findById(int $id): ?array
    {
        $sql = "SELECT id, name, sku, description, price, stock,
            status, created_at, updated_at FROM `products` WHERE
            id = :id LIMIT 1";

        $statement = $this->pdo->prepare($sql);
        $statement->execute(['id' => $id]);
        $product = $statement->fetch();
        // var_dump($products); exit;
        return $product ?: NULL;
    }

    /**
     * Method to gett all products
     *
     * @return array
     */
    #[Override]
    public function all(): array
    {
        $sql = "SELECT id, name, sku, description, price, stock,
            status, created_at, updated_at FROM products ORDER BY id
            desc";

        $statement = $this->pdo->query($sql);
        $statement->execute();
        $products = $statement->fetchAll();
        return $products;
    }

    /**
     * Method to create new product
     *
     * @param CreateProductData $data
     * @return array
     */
    #[Override]
    public function create(CreateProductData $data): array
    {
        $sql = "INSERT INTO products(name, sku, description, price, stock, status)
            VALUES(:name, :sku, :description, :price, :stock, :status)";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'name' => $data->name,
            'sku' => $data->sku,
            'description' => $data->description,
            'price' => $data->price,
            'stock' => $data->stock,
            'status' => $data->status ?? 'active'
        ]);

        $id = (int) $this->pdo->lastInsertId();
        return $this->findById($id);
    }
}