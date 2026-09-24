<?php

declare(strict_types = 1);

use App\Api\Controllers\AuthController;
use App\Api\Controllers\ProductsController;
use App\Auth\Permission;
use App\Http\Middleware\ApiPermissionMiddleware;
use App\Http\Middleware\JwtAuthMiddleware;
use App\Routing\Router;

return function (Router $router, $container): void
{
    $authController = $container->get(AuthController::class);
    $productController = $container->get(ProductsController::class);


    /**-----------------------------------------
     * APIs auth routes
     * -------------------------------------------
     */
    $router->post('/api/v1/auth/login', [$authController, 'login']);
    $router->get('/api/v1/auth/me', [$authController, 'me'], [
        JwtAuthMiddleware::class
    ]);

    /**-----------------------------------------
     * APIs products routes
     * -------------------------------------------
     */
    $router->get('/api/v1/products', [$productController, 'index'], [
        JwtAuthMiddleware::class,
        [
            ApiPermissionMiddleware::class,
            Permission::PRODUCT_VIEW
        ]
    ]);
    $router->get('/api/v1/products/{id}', [$productController, 'productById'], [
        JwtAuthMiddleware::class,
        [
            ApiPermissionMiddleware::class,
            Permission::PRODUCT_VIEW
        ]
    ]);
    $router->post('/api/v1/products', [$productController, 'store'], [
        JwtAuthMiddleware::class,
        [
            ApiPermissionMiddleware::class,
            Permission::PRODUCT_CREATE
        ]
    ]);
    $router->patch('/api/v1/products/{id}', [$productController, 'update'], [
        JwtAuthMiddleware::class,
        [
            ApiPermissionMiddleware::class,
            Permission::PRODUCT_UPDATE
        ]
    ]);
    $router->delete('/api/v1/products/{id}', [$productController, 'destroy'], [
        JwtAuthMiddleware::class,
        [
            ApiPermissionMiddleware::class,
            Permission::PRODUCT_DELETE
        ]
    ]);
};