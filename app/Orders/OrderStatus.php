<?php

declare(strict_types = 1);

namespace App\Orders;

final class OrderStatus
{
    public const PENDING    = 'PENDING';
    public const CONFIRMED  = 'CONFIRMED';
    public const PROCESSING = 'PROCESSING';
    public const SHIPPED    = 'SHIPPED';
    public const DELIVERED  = 'DELIVERED';
    public const CANCELLED  = 'CANCELLED';

    public function __construct()
    {}
}