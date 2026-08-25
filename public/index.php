<?php

declare(strict_types=1); // defining the strict type checking

require_once __DIR__ . '/../bootstrap/app.php'; // Including bootstrap app file

use App\Controllers\HomeController;
use App\Controllers\OrderController;
use App\Controllers\ProductController;
use App\Http\Response;
use App\Services\SystemInfoService;
use App\Routing\Router;

$router = new Router();
// $service = new GreetingService();
// $systemInfoService = new SystemInfoService();

// echo $service->message() . "\n";
// echo $systemInfoService->getApplicationName()  . "\n";
// echo $systemInfoService->getEnvironment()  . "\n";


// echo "Enterprise Order Management System";
$systemInfo = new SystemInfoService();
$homeController = new HomeController($systemInfo);

$productController = new ProductController();
$orderController = new OrderController();
$response = new Response();

// $systemInfor = $homeController->index();

/**
 * Defining routes in core php
 */
$router->get('/', [$homeController, 'index']);
$router->get('/products', [$productController, 'index']);
$router->get('/orders', [$orderController, 'index']);
$router->post('/orders', [$orderController, 'store']);


$router->get('/api/helth', function() use ($response) {
    $response->json([
        "success" => true,
        "message" => "API is running smoothly",
        "timestamp" => time()
    ], 200);
});

// echo '<pre>';
// // print_r($systemInfor);
// // print_r($_SERVER);
// $homeController->index();
// echo '</pre>';

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);


