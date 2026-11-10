<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\LogEntry;
use DateTimeImmutable;
use PDO;

final class LogRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function latest(int $limit): array
    {
        $statement = $this->pdo->prepare('SELECT l.id, l.level, l.message, u.username, l.created_at FROM system_logs l LEFT JOIN users u ON u.id = l.user_id ORDER BY l.created_at DESC, l.id DESC LIMIT :limit');

        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        $logs = [];

        foreach ($statement->fetchAll() as $row) {
            $logs[] = new LogEntry(
                (int) $row['id'],
                $row['level'],
                $row['message'],
                $row['username'],
                new DateTimeImmutable($row['created_at'])
            );
        }

        return $logs;
    }

    public function add(string $level, string $message, ?array $context = null, ?int $userId = null): void
    {
        $contextJson = null;

        if ($context !== null) {
            $contextJson = json_encode($context, JSON_THROW_ON_ERROR);
        }

        $statement = $this->pdo->prepare('INSERT INTO system_logs (level, message, context, user_id) VALUES (:level, :message, CAST(:context AS JSONB), :user_id)');

        $statement->bindValue(':level', $level, PDO::PARAM_STR);
        $statement->bindValue(':message', $message, PDO::PARAM_STR);
        $statement->bindValue(':context', $contextJson, $contextJson === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $statement->bindValue(':user_id', $userId, $userId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);

        $statement->execute();
    }
}
