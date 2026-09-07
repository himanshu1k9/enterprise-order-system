<?php

declare(strict_types = 1);

namespace App\Repositories;

use App\DTO\CreateProductData;
use App\DTO\PaginationData;
use App\DTO\ProductFilterData;
use App\DTO\ProductSortData;
use App\DTO\UpdateProductData;
use App\Exceptions\ConflictException;
use Override;
use PDO;
use PDOException;

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
        try
        {
            $statement->execute([
                'name' => $data->name,
                'sku' => $data->sku,
                'description' => $data->description,
                'price' => $data->price,
                'stock' => $data->stock,
                'status' => $data->status ?? 'active'
            ]);
        } catch(PDOException $e) {
            if(isset($e->errorInfo[1]) && (int) $e->errorInfo[1] === 1062) {
                throw new ConflictException('Product SKU already exists.');
            }
            throw $e;
        }

        $id = (int) $this->pdo->lastInsertId();
        return $this->findById($id);
    }

    /**
     * This method returning the pagination data.
     *
     * @param PaginationData $pagination
     * @return array
     */
    #[Override]
    public function paginate(PaginationData $pagination, ProductFilterData $productFilter, ProductSortData $sort): array
    {
        $where = [];
        $params = [];

        /**
         * Whitelisting columns for sorting
         */
        $allowedSortColumns = [
            'id' => 'id',
            'name' => 'name',
            'price' => 'price',
            'stock' => 'stock',
            'created_at' => 'created_at'
        ];
        $sortColumn = $allowedSortColumns[$sort->sort];
        $sortDirection = strtoupper($sort->direction);

        if($productFilter->status !== null) {
            $where[] = 'status = :status';
            $params['status'] = $productFilter->status;
        }

        if($productFilter->search !== null) {
            $where[] = '( name LIKE :name OR sku LIKE :sku )';
            $params['name'] = '%' . $productFilter->search . '%';
            $params['sku'] = '%' . $productFilter->search . '%';
        }

        $whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT id, name, sku, description, price, stock,
            status, created_at, updated_at FROM products $whereSQL ORDER BY
            $sortColumn $sortDirection LIMIT :limit OFFSET :offset";

        $statement = $this->pdo->prepare($sql);
        foreach($params as $name => $param) {
            $statement->bindValue(':' . $name, $param);
        }

        $statement->bindValue(':limit', $pagination->limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $pagination->offset(), PDO::PARAM_INT);
        $statement->execute();

        $products = $statement->fetchAll();

        $countStatement = $this->pdo->prepare("SELECT COUNT(*) FROM products $whereSQL");
        foreach($params as $name => $param) {
            $countStatement->bindValue(':' . $name, $param);
        }
        $countStatement->execute();
        $total = (int) $countStatement->fetchColumn();

        return [
            'data' => $products,
            'page' => $pagination->page,
            'limit' => $pagination->limit,
            'total' => $total,
            'total_pages' => (int) ceil( $total / $pagination->limit )
        ];
    }

    /**
     * Method to update the product
     *
     * @param integer $id
     * @param UpdateProductData $data
     * @return boolean
     */
    #[Override]
    public function update(int $id, UpdateProductData $data): bool
    {
        $sql = "UPDATE products set name = :name, sku = :sku,
            description = :description, price = :price, stock = :stock,
            status = :status WHERE id = :id";

        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':name', $data->name, PDO::PARAM_STR);
        $statement->bindValue(':sku', $data->sku, PDO::PARAM_STR);
        $statement->bindValue(':description', $data->description, PDO::PARAM_STR);
        $statement->bindValue(':price', $data->price);
        $statement->bindValue(':stock', $data->stock, PDO::PARAM_INT);
        $statement->bindValue(':status', $data->status, PDO::PARAM_STR);

        $statement->execute();

        return $statement->rowCount() > 0;
    }

    /**
     * Method to delete the product
     *
     * @param integer $id
     * @return boolean
     */
    #[Override]
    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare("DELETE FROM products WHERE id = :id");
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->rowCount() > 0;
    }
}