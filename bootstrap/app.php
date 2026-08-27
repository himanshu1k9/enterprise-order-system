<?php

declare(strict_types = 1);

use App\Container\Container;
// use App\Controllers\ProductController;
use App\Database\Database;
use App\Repositories\ProductRepository;
use App\Repositories\ProductRepositoryInterface;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php'; // requiring vender autoload for autoloading files

// Loading dotenv file
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();


error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

$container = new Container();
$container->set(PDO::class, function () {
    $database = new Database();
    return $database->connection();
});

$container->set(ProductRepositoryInterface::class, function() use($container) {
    return new ProductRepository($container->get(PDO::class));
});

// $container->set(ProductController::class, function() use($container) {
//     return new ProductController($container->get(ProductRepositoryInterface::class));
// });