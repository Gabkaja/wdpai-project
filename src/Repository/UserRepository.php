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
        $statement = $this->pdo->query('SELECT id, username, email, role, points, unopened_packs, avatar_path, created_at FROM users ORDER BY id');

        $users = [];

        foreach ($statement->fetchAll() as $row) {
            $users[] = new User(
                (int) $row['id'],
                $row['username'],
                $row['email'],
                $row['role'],
                (int) $row['points'],
                (int) $row['unopened_packs'],
                new DateTimeImmutable($row['created_at']),
                $row['avatar_path'] !== null ? (string) $row['avatar_path'] : null
            );
        }

        return $users;
    }

    public function findById(int $id): ?User
    {
        $statement = $this->pdo->prepare('SELECT id, username, email, role, points, unopened_packs, avatar_path, created_at FROM users WHERE id = :id');

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
        $statement = $this->pdo->prepare('INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash) RETURNING id, username, email, role, points, unopened_packs, avatar_path, created_at');

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
        $statement = $this->pdo->prepare('SELECT id, username, email, password_hash, role, points, unopened_packs, avatar_path, created_at FROM users WHERE LOWER(email) = LOWER(:email)');

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

    public function findPasswordHashById(int $id): ?string
    {
        $statement = $this->pdo->prepare('SELECT password_hash FROM users WHERE id = :id');

        $statement->execute([':id' => $id]);

        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        return $row['password_hash'];
    }

    public function updatePasswordHash(int $id, string $passwordHash): void
    {
        $statement = $this->pdo->prepare('UPDATE users SET password_hash = :password_hash WHERE id = :id');

        $statement->execute([
            ':id' => $id,
            ':password_hash' => $passwordHash,
        ]);
    }

    public function updateAvatarPath(int $userId, ?string $path): void
    {
        $statement = $this->pdo->prepare('UPDATE users SET avatar_path = :avatar_path WHERE id = :id');
        $statement->bindValue(':avatar_path', $path, $path === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $statement->bindValue(':id', $userId, PDO::PARAM_INT);
        $statement->execute();
    }

    public function addPoints(int $userId, int $amount): void
    {
        $statement = $this->pdo->prepare('UPDATE users SET points = points + :amount WHERE id = :id');

        try {
            $statement->execute([':amount' => $amount, ':id' => $userId]);
        } catch (\PDOException $exception) {
            if ($exception->getCode() === '23514') {
                throw new \DomainException('points_out_of_range');
            }

            throw $exception;
        }

        if ($statement->rowCount() === 0) {
            throw new \DomainException('user_not_found');
        }
    }

    private function mapRowToUserWithoutPassword(array $row): User
    {
        return new User(
            (int) $row['id'],
            $row['username'],
            $row['email'],
            $row['role'],
            (int) $row['points'],
            (int) $row['unopened_packs'],
            new DateTimeImmutable($row['created_at']),
            $row['avatar_path'] !== null ? (string) $row['avatar_path'] : null
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
            (int) $row['unopened_packs'],
            new DateTimeImmutable($row['created_at']),
            $row['avatar_path'] !== null ? (string) $row['avatar_path'] : null
        );
    }
}
