<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\LeaderboardEntry;
use PDO;

final class LeaderboardRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(): array
    {
        $statement = $this->pdo->query('SELECT user_id, username, total_cards, unique_cards, rank_position FROM leaderboard_view ORDER BY rank_position, username');

        $entries = [];

        foreach ($statement->fetchAll() as $row) {
            $entries[] = $this->mapRowToLeaderboardEntry($row);
        }

        return $entries;
    }

    private function mapRowToLeaderboardEntry(array $row): LeaderboardEntry
    {
        return new LeaderboardEntry(
            (int) $row['user_id'],
            $row['username'],
            (int) $row['total_cards'],
            (int) $row['unique_cards'],
            (int) $row['rank_position']
        );
    }
}
