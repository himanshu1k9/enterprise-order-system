<?php

declare(strict_types = 1);

namespace App\Repositories;

use App\Repositories\Contracts\OrderRepositoryInterface;
use Override;
use PDO;

class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(private PDO $pdo)
    {}

    #[Override]
    public function create(array $data): int
    {
        $sql = "INSERT INTO orders
                    (user_id, status, subtotal, total_amount)
                VALUES
                    (:user_id, :status, :sub_total, :total_amount)
                ";
        $statement = $this->pdo->prepare($sql);
        $statement->bindParam(':user_id', $data['user_id'], PDO::PARAM_STR);
        $statement->bindParam(':status', $data['status'], PDO::PARAM_STR);
        $statement->bindParam(':sub_total', $data['sub_total']);
        $statement->bindParam(':total_amount', $data['total_amount']);
        if($statement->execute()) {
            return (int) $this->pdo->lastInsertId();
        }
        return 0;
    }

    #[Override]
    public function createItems(array $data): int
    {
        $sql = "INSERT INTO order_items
                    (order_id, product_id, quantity, unit_price, subtotal)
                VALUES
                    (:order_id, :product_id, :quantity, :unit_price, :subtotal)
                ";
        $statement = $this->pdo->prepare($sql);
        $statement->bindParam(':order_id', $data['order_id'], PDO::PARAM_INT);
        $statement->bindParam(':product_id', $data['product_id'], PDO::PARAM_INT);
        $statement->bindParam(':quantity', $data['quantity'], PDO::PARAM_INT);
        $statement->bindParam(':unit_price', $data['unit_price']);
        $statement->bindParam(':subtotal', $data['subtotal']);

        if($statement->execute()) {
            return (int) $this->pdo->lastInsertId();
        }
        return 0;
    }

    #[Override]
    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM orders WHERE id = :id";

        $statement = $this->pdo->prepare($sql);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $order = $statement->fetch(PDO::FETCH_ASSOC);
        return $order ?? NULL;
    }

    #[Override]
    public function findItemsById(int $id): array
    {
        $sql = "SELECT * FROM order_items WHERE order_id = :order_id ORDER BY id ASC";

        $statement = $this->pdo->prepare($sql);
        $statement->bindParam(':order_id', $id, PDO::PARAM_INT);
        $statement->execute();
        $orderItems = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $orderItems;
    }

    #[Override]
    public function updateStatus(int $orderId, string $status): bool
    {
        $sql = "UPDATE orders SET status = :status WHERE id = :id";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':id', $orderId, PDO::PARAM_INT);
        $statement->bindValue(':status', $status, PDO::PARAM_STR);
        $statement->execute();

        return $statement->rowCount() === 1;
    }

    #[Override]
    public function findByIdForUpdate(int $id): ?array
    {
        $sql = "SELECT * FROM orders WHERE id = :id LIMIT 1 FOR UPDATE";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        $order = $statement->fetch(PDO::FETCH_ASSOC);
        return $order !== false ? $order : null;
    }
}