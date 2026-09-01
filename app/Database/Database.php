<?php

declare(strict_types = 1);
namespace App\Database;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private ?PDO $connection = null;
    // public function __construct()
    // {
    //     $host = $_ENV['DB_HOST'];
    //     $port = $_ENV['DB_PORT'];
    //     $database = $_ENV['DB_DATABASE'];
    //     $username = $_ENV['DB_USERNAME'];
    //     $password = $_ENV['DB_PASSWORD'];

    //     // Data Source Name
    //     $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

    //     $this->connection = new PDO(
    //         $dsn,
    //         $username,
    //         $password,
    //         [
    //             PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION // throwing if error occured
    //         ]
    //     );
    // }

    public function connection(): PDO
    {
        if($this->connection !== null) {
            return $this->connection;
        }

        $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port = $_ENV['DB_PORT'] ?? '3306';
        $database = $_ENV['DB_DATABASE'] ?? '';
        $username = $_ENV['DB_USERNAME'] ?? 'root';
        $password = $_ENV['DB_PASSWORD'] ?? '';
        
        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
        try
        {
            $this->connection = new PDO(
                $dsn,
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            return $this->connection;
        } catch(PDOException $e) {
            throw new RuntimeException('Database connection failed', 0, $e);
        }
    }
}