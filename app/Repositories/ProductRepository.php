<?php

declare(strict_types = 1);

namespace App\Repositories;

use App\DTO\CreateProductData;
use App\DTO\PaginationData;
use App\DTO\ProductFilterData;
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
    public function paginate(PaginationData $pagination, ProductFilterData $productFilter): array
    {
        $where = [];
        $params = [];

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
            id DESC LIMIT :limit OFFSET :offset";

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
}