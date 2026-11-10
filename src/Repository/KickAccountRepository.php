<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class KickAccountRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findUserIdByKickId(int $kickId): ?int
    {
        $statement = $this->pdo->prepare('SELECT user_id FROM kick_accounts WHERE kick_user_id = :kick_user_id');
        $statement->execute([':kick_user_id' => $kickId]);
        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        return (int) $row['user_id'];
    }

    public function findByUserId(int $userId): ?array
    {
        $statement = $this->pdo->prepare('SELECT user_id, kick_user_id, kick_username, linked_at FROM kick_accounts WHERE user_id = :user_id');
        $statement->execute([':user_id' => $userId]);
        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        return $row;
    }

    public function link(int $userId, int $kickId, string $username): void
    {
        $statement = $this->pdo->prepare('INSERT INTO kick_accounts (user_id, kick_user_id, kick_username) VALUES (:user_id, :kick_user_id, :kick_username)');
        $statement->execute([
            ':user_id' => $userId,
            ':kick_user_id' => $kickId,
            ':kick_username' => $username,
        ]);
    }

    public function unlink(int $userId): void
    {
        $statement = $this->pdo->prepare('DELETE FROM kick_accounts WHERE user_id = :user_id');
        $statement->execute([':user_id' => $userId]);
    }
}
