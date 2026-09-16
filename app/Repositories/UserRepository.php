<?php

declare(strict_types = 1);

namespace App\Repositories;

use Override;
use PDO;
use RuntimeException;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(private PDO $pdo)
    {}

    /**
     * Method to find user by email
     *
     * @param string $email
     * @return array|false
     */
    #[Override]
    public function findByEmail(string $email): array|false
    {
        $statement = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $statement->execute(['email' => $email]);
        return $statement->fetch();
    }

    /**
     * Method to get user by id
     *
     * @param integer $id
     * @return array|false
     */
    #[Override]
    public function findById(int $id): array|false
    {
        $statement = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $statement->execute(['id' => $id]);
        return $statement->fetch();
    }

    /**
     * Method to create new user
     *
     * @param string $name
     * @param string $email
     * @param string $passwordHash
     * @param string $role
     * @return integer
     */
    #[Override]
    public function create(string $name, string $email, string $passwordHash, string $role = 'customer'): int
    {
        $statement = $this->pdo->prepare("INSERT INTO users(name, email, password, role) VALUES(:name, :email, :password, :role)");
        $statement->execute([
            'name' => $name,
            'email' => $email,
            'password' => $passwordHash,
            'role' => $role
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Method to update the password
     *
     * @param integer $userId
     * @param string $passwordHash
     * @return boolean
     */
    #[Override]
    public function updatePassword(int $userId, string $passwordHash): bool
    {
        $sql = "UPDATE users SET password = :password WHERE id = :id";
        $statement = $this->pdo->prepare($sql);
        $statement->bindParam(':id', $userId, PDO::PARAM_INT);
        $statement->bindParam(':password', $passwordHash, PDO::PARAM_STR);
        return  $statement->execute();
    }

    /**
     * Method to increase session version
     *
     * @param integer $userId
     * @return void
     */
    #[Override]
    public function incrementSessionVersion(int $userId): void
    {
        $statement = $this->pdo->prepare("UPDATE users SET session_version = session_version + 1 WHERE id = :id");
        $statement->execute([
            'id' => $userId
        ]);
    }

    /**
     * Method to get session version
     *
     * @param integer $userId
     * @return integer
     */
    #[Override]
    public function getSessionVersion(int $userId): int
    {
        $statement = $this->pdo->prepare("SELECT session_version FROM users WHERE id = :id");
        $statement->execute([
            ':id' => $userId
        ]);

        $version = $statement->fetchColumn();
        if($version === false) {
            throw new RuntimeException("User Not found");
        }

        return (int) $version;
    }
}