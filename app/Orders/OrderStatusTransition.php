<?php

declare(strict_types = 1);

namespace App\Orders;

use DomainException;

final class OrderStatusTransition
{
    private const TRANSACTIONS = [
        OrderStatus::PENDING => [
            OrderStatus::CONFIRMED,
            OrderStatus::CANCELLED
        ],
        OrderStatus::CONFIRMED => [
            OrderStatus::PROCESSING,
            OrderStatus::CANCELLED
        ],
        OrderStatus::PROCESSING => [
            OrderStatus::SHIPPED
        ],
        OrderStatus::SHIPPED => [
            OrderStatus::DELIVERED
        ],
        OrderStatus::DELIVERED => [],
        OrderStatus::CANCELLED => []
    ];

    public function __construct()
    {}

    /**
     * Method to if exists
     */
    public static function canTransaction(string $from, string $to): bool
    {
        return in_array($to, self::TRANSACTIONS[$from], true);
    }

    /**
     * Method to validate the status transactions
     */
    public static function validate(string $from, string $to): void
    {
        if(!self::canTransaction($from, $to)) {
            throw new DomainException("Invalid order status transition: {$from} → {$to}.");
        }
    }
}