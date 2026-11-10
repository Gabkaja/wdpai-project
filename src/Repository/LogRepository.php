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
}
