<?php

declare(strict_types = 1);

use App\Auth\Permission;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\OrderController;
use App\Controllers\ProductController;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\CsrfMiddleware;
use App\Http\Middleware\PermissionMiddleware;
// use App\Http\Middleware\ProductDeleteMiddleware;
// use App\Http\Middleware\ProductWriteMiddleware;
use App\Http\Response;
use App\Routing\Router;

return function(Router $router, $container): void
{
    $homeController = $container->get(HomeController::class);
    $productController = $container->get(ProductController::class);
    $orderController = $container->get(OrderController::class);
    $authController = $container->get(AuthController::class);


    /**
     * Home Routes
     */
    $router->get('/', [$homeController, 'index']);

    /**
     * Products Routes
     */
    $router->get('/products', [$productController, 'index']);
    $router->get('/products/{id}', [$productController, 'show']);
    $router->post('/products', [$productController, 'store'], [AuthMiddleware::class, [
        PermissionMiddleware::class,
        Permission::PRODUCT_CREATE
    ], CsrfMiddleware::class]);

    $router->patch('/products/{id}', [$productController, 'update'], [AuthMiddleware::class, [
        PermissionMiddleware::class,
        Permission::PRODUCT_UPDATE
    ]]);

    $router->delete('/products/{id}', [$productController, 'destroy'], [AuthMiddleware::class, [
        PermissionMiddleware::class,
        Permission::PRODUCT_DELETE
    ]]);

    $router->get('/products/{productId}/reviews/{reviewId}', [$productController, 'review']);

    /**
     * Orders Routes
     */
    $router->get('/orders', [$orderController, 'index']);
    $router->post('/orders', [$orderController, 'store']);

    /**
     * Auth Routes
     */
    $router->post('/register', [$authController, 'register']);
    $router->post('/login', [$authController, 'login']);
    $router->post('/logout', [$authController, 'logout'], [AuthMiddleware::class]);
    $router->get('/csrf-token', [$authController, 'csrf'], [AuthMiddleware::class]);

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