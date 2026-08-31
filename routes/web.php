<?php

declare(strict_types = 1);

use App\Controllers\HomeController;
use App\Controllers\OrderController;
use App\Controllers\ProductController;
use App\Http\Response;
use App\Routing\Router;

return function(Router $router, $container): void
{
    $homeController = $container->get(HomeController::class);
    $productController = $container->get(ProductController::class);
    $orderController = $container->get(OrderController::class);


    /**
     * Home Routes
     */
    $router->get('/', [$homeController, 'index']);

    /**
     * Products Routes
     */
    $router->get('/products', [$productController, 'index']);
    $router->get('/products/{id}', [$productController, 'show']);
    $router->post('/products', [$productController, 'store']);
    $router->put('/products/{id}', [$productController, 'update']);
    $router->delete('/products/{id}', [$productController, 'destroy']);
    $router->get('/products/{productId}/reviews/{reviewId}', [$productController, 'review']);

    /**
     * Orders Routes
     */
    $router->get('/orders', [$orderController, 'index']);
    $router->post('/orders', [$orderController, 'store']);

    /**
     * Health Check
     */
    $router->get('/api/health', function () {
            return Response::json(
                [
                    'success' => true,
                    'message' => 'API is running smoothly',
                    'timestamp' => time()
                ],
                200
            );
        }
    );
};