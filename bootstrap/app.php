<?php

declare(strict_types = 1);

use App\Application;
use App\Container\Container;
// use App\Controllers\ProductController;
use App\Database\Database;
use App\Http\Kernel;
use App\Http\Request;
use App\Repositories\ProductRepository;
use App\Repositories\ProductRepositoryInterface;
use App\Routing\Router;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php'; // requiring vender autoload for autoloading files

// Loading dotenv file
$dotenv = Dotenv::createImmutable(__DIR__ . '/../'); 
$dotenv->safeLoad();

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

$container = new Container(); // Creating instance of Container

// Implementing singleton : Create Instance once and use multiple times
$container->singleton(PDO::class, function () {
    $database = new Database();
    return $database->connection();
});

$container->singleton(Router::class, function() {return new Router();});
$container->singleton(Request::class, function() {return new Request();});
$container->bind(Application::class, Application::class);

// $container->set(ProductRepositoryInterface::class, function() use($container) {
//     return new ProductRepository($container->get(PDO::class));
// });

$container->bind(ProductRepositoryInterface::class, ProductRepository::class);

// $container->set(ProductController::class, function() use($container) {
//     return new ProductController($container->get(ProductRepositoryInterface::class));
// });

/**
 * Calling routes
 * -----------------
 */
$router = $container->get(Router::class);
$routes = require __DIR__ . '/../routes/web.php';
$routes($router, $container);

/**
 * Registering Kernel
 */
$container->singleton(Kernel::class, function() use($container) {
    return new Kernel($container, $container->get(Router::class));
});