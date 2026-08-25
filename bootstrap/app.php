<?php

declare(strict_types = 1);

use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php'; // requiring vender autoload for autoloading files

// Loading dotenv file
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();


error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');