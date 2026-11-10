<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\User;
use DateTimeImmutable;
use PDO;

final class UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(): array
    {
        $statement = $this->pdo->query('SELECT id, username, email, role, points, created_at FROM users ORDER BY id');

        $users = [];

        foreach ($statement->fetchAll() as $row) {
            $users[] = new User(
                (int) $row['id'],
                $row['username'],
                $row['email'],
                $row['role'],
                (int) $row['points'],
                new DateTimeImmutable($row['created_at'])
            );
        }

        return $users;
    }

    public function findById(int $id): ?User
    {
        $statement = $this->pdo->prepare('SELECT id, username, email, role, points, created_at FROM users WHERE id = :id');

        $statement->execute([':id' => $id]);

        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        return $this->mapRowToUser($row);
    }

    public function existsByUsername(string $username): bool
    {
        $statement = $this->pdo->prepare('SELECT 1 FROM users WHERE username = :username');

        $statement->execute([':username' => $username]);

        return $statement->fetch() !== false;
    }

    public function existsByEmail(string $email): bool
    {
        $statement = $this->pdo->prepare('SELECT 1 FROM users WHERE LOWER(email) = LOWER(:email)');

        $statement->execute([':email' => $email]);

        return $statement->fetch() !== false;
    }

    public function create(string $username, string $email, string $passwordHash): User
    {
        $statement = $this->pdo->prepare('INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash) RETURNING id, username, email, role, points, created_at');

        $statement->execute([
            ':username' => $username,
            ':email' => $email,
            ':password_hash' => $passwordHash,
        ]);

        $row = $statement->fetch();

        if ($row === false) {
            throw new \RuntimeException('User could not be created.');
        }

        return $this->mapRowToUser($row);
    }

    public function findByEmailWithHash(string $email): ?array
    {
        $statement = $this->pdo->prepare('SELECT id, username, email, password_hash, role, points, created_at FROM users WHERE LOWER(email) = LOWER(:email)');

        $statement->execute([':email' => $email]);

        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        return [
            'user' => $this->mapRowToUserWithoutPassword($row),
            'password_hash' => $row['password_hash'],
        ];
    }

    public function updatePasswordHash(int $id, string $passwordHash): void
    {
        $statement = $this->pdo->prepare('UPDATE users SET password_hash = :password_hash WHERE id = :id');

        $statement->execute([
            ':id' => $id,
            ':password_hash' => $passwordHash,
        ]);
    }

    private function mapRowToUserWithoutPassword(array $row): User
    {
        return new User(
            (int) $row['id'],
            $row['username'],
            $row['email'],
            $row['role'],
            (int) $row['points'],
            new DateTimeImmutable($row['created_at'])
        );
    }

    private function mapRowToUser(array $row): User
    {
        return new User(
            (int) $row['id'],
            $row['username'],
            $row['email'],
            $row['role'],
            (int) $row['points'],
            new DateTimeImmutable($row['created_at'])
        );
    }
}
