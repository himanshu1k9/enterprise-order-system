<?php

declare(strict_types=1); // defining the strict type checking
require_once __DIR__ . '/../bootstrap/app.php'; // Including bootstrap app file

use App\Services\GreetingService;

$service = new GreetingService();

echo $service->message();


// echo "Enterprise Order Management System";

