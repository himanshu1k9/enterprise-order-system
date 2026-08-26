<?php

declare(strict_types = 1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Database\Database;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$database = new Database();
$pdo = $database->connection();

echo "Database connected successfully.";