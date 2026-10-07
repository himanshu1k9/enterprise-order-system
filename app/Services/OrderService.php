<?php

declare(strict_types = 1);

namespace App\Services;

use App\Orders\OrderStatusTransition;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\ProductRepositoryInterface;
use App\Services\Contracts\OrderServiceInterface;
use DomainException;
use Override;
use PDO;
use Throwable;

class OrderService implements OrderServiceInterface
{
    public function __construct(
        private PDO $pdo,
        private OrderRepositoryInterface $orderRepo,
        private ProductRepositoryInterface $productRepo
    ) {}

    #[Override]
    public function createOrder(int $user_id, array $items): array
    {
        if($items === []) {
            throw new DomainException('Order must contain at least one item.');
        }

        $this->pdo->beginTransaction();
        try {
            $preparedItems = [];
            $orderSubTotal = 0.00;

            foreach($items as $item) {
                $productId = (int) $item['product_id'];
                $quantiy = (int) $item['quantity'];

                if($quantiy <= 0) {
                    throw new DomainException('Product quantity must be greater than zero.');
                }

                // $product = $this->productRepo->findForOrder($productId);
                $product = $this->productRepo->findForUpdate($productId);
                if($product === false) {
                    throw new DomainException("Product {$productId} not found.");
                }

                if($product['status'] !== 'active') {
                    throw new DomainException("Product {$productId} is not available.");
                }

                if((int) $product['stock'] < $quantiy) {
                    throw new DomainException("Insufficient stock for product {$productId}.");
                }

                $unitPrice = (float) $product['price'];
                $subTotal = $quantiy * $unitPrice;
                $orderSubTotal += $subTotal;

                $preparedItems[] = [
                    'product_id' => $productId,
                    'quantity' => $quantiy,
                    'unit_price' => number_format($unitPrice, 2, '.', ''),
                    'subtotal' => number_format($subTotal, 2, '.', '')
                ];
            }

            $orderId = $this->orderRepo->create([
                'user_id'      => $user_id,
                'status'       => 'PENDING',
                'subtotal'     => number_format($orderSubTotal, 2, '.', ''),
                'total_amount' => number_format($orderSubTotal, 2, '.', ''),
            ]);

            foreach ($preparedItems as $item) {
                $this->orderRepo->createItems([
                    'order_id'   => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal'   => $item['subtotal'],
                ]);

                $stockUpdated = $this->productRepo->decreaseStock($item['product_id'], $item['quantity']);
                if(!$stockUpdated) {
                    throw new DomainException("Unable to reserve stock for product {$item['product_id']}.");
                }
            }

            $this->pdo->commit();

            return [
                'order_id'     => $orderId,
                'status'       => 'PENDING',
                'subtotal'     => number_format($orderSubTotal, 2, '.', ''),
                'total_amount' => number_format($orderSubTotal, 2, '.', ''),
            ];
        } catch(Throwable $e) {
            if($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    #[Override]
    public function updateStatus(int $orderId, string $newStatus): array
    {
        $this->pdo->beginTransaction();
        try {
            // $order = $this->orderRepo->findById($orderId);
            $order = $this->orderRepo->findByIdForUpdate($orderId);
            if($order === null) {
                throw new DomainException("Order {$orderId} not found.");
            }

            $currentStatus = $order['status'];
            OrderStatusTransition::validate($currentStatus, $newStatus);

            $updated = $this->orderRepo->updateStatus($orderId, $newStatus);
            if(!$updated) {
                throw new DomainException("Unable to update order status.");
            }

            $this->pdo->commit();
            $order['status'] = $newStatus;
            return $order;
        } catch(Throwable $th) {
            if($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $th;
        }
    }
}