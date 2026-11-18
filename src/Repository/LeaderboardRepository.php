<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\LeaderboardEntry;
use DateTimeImmutable;
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
        $statement = $this->pdo->query('SELECT user_id, username, total_cards, unique_cards, rank_position, joined_at, rarest_card_name, packs_opened FROM leaderboard_view ORDER BY rank_position, username');

        $entries = [];

        foreach ($statement->fetchAll() as $row) {
            $entries[] = $this->mapRowToLeaderboardEntry($row);
        }

        return $entries;
    }

    public function findByUserId(int $userId): ?LeaderboardEntry
    {
        $statement = $this->pdo->prepare('SELECT user_id, username, total_cards, unique_cards, rank_position, joined_at, rarest_card_name, packs_opened FROM leaderboard_view WHERE user_id = :user_id');
        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->execute();
        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        return $this->mapRowToLeaderboardEntry($row);
    }

    private function mapRowToLeaderboardEntry(array $row): LeaderboardEntry
    {
        return new LeaderboardEntry(
            (int) $row['user_id'],
            $row['username'],
            (int) $row['total_cards'],
            (int) $row['unique_cards'],
            (int) $row['rank_position'],
            new DateTimeImmutable($row['joined_at']),
            $row['rarest_card_name'],
            (int) $row['packs_opened']
        );
    }
}
