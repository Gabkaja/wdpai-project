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
}
