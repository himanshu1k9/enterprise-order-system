<?php

declare(strict_types = 1);
namespace App\Database;

use PDO;

class Database {
    private PDO $connection;
    public function __construct()
    {
        $host = $_ENV['DB_HOST'];
        $port = $_ENV['DB_PORT'];
        $database = $_ENV['DB_DATABASE'];
        $username = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];

        // Data Source Name
        $dsn = "mysql:host={$host};port:{$port};dbname:{$database};charset=utf8mb4";

        $this->connection = new PDO(
            $dsn,
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION // throwing if error occured
            ]
        );
    }

    public function connection(): PDO 
    {
        return $this->connection;
    }
}