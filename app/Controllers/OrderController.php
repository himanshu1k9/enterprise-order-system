<?php

declare(strict_types = 1);

namespace App\Controllers;

class OrderController {

    public function index(): void {
        echo 'List of All Orders.';
    }

    public function store(): void {
        echo 'Order Created';
    }
}