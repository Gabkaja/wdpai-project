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
        $statement = $this->pdo->query('SELECT lv.user_id, lv.username, lv.total_cards, lv.unique_cards, lv.rank_position, lv.joined_at, lv.rarest_card_name, lv.packs_opened, u.avatar_path FROM leaderboard_view lv JOIN users u ON u.id = lv.user_id ORDER BY lv.rank_position, lv.username');

        $entries = [];

        foreach ($statement->fetchAll() as $row) {
            $entries[] = $this->mapRowToLeaderboardEntry($row);
        }

        return $entries;
    }

    public function findByUserId(int $userId): ?LeaderboardEntry
    {
        $statement = $this->pdo->prepare('SELECT lv.user_id, lv.username, lv.total_cards, lv.unique_cards, lv.rank_position, lv.joined_at, lv.rarest_card_name, lv.packs_opened, u.avatar_path FROM leaderboard_view lv JOIN users u ON u.id = lv.user_id WHERE lv.user_id = :user_id');
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
            (int) $row['packs_opened'],
            $row['avatar_path']
        );
    }
}
