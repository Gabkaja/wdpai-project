<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Rarity;
use App\Model\RarityOdds;
use PDO;

final class RarityRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(): array
    {
        $statement = $this->pdo->query('SELECT id, code, name, sort_order FROM rarities ORDER BY sort_order');

        $rarities = [];

        foreach ($statement->fetchAll() as $row) {
            $rarities[] = new Rarity(
                (int) $row['id'],
                $row['code'],
                $row['name'],
                (int) $row['sort_order']
            );
        }

        return $rarities;
    }

    public function odds(): array
    {
        $statement = $this->pdo->query('SELECT r.code, r.name, r.drop_weight, COUNT(c.id) AS card_count FROM rarities r LEFT JOIN cards c ON c.rarity_id = r.id GROUP BY r.id ORDER BY r.sort_order');
        $rows = $statement->fetchAll();

        $totalWeight = 0;

        foreach ($rows as $row) {
            if ((int) $row['drop_weight'] > 0 && (int) $row['card_count'] > 0) {
                $totalWeight += (int) $row['drop_weight'];
            }
        }

        $count = count($rows);
        $odds = [];

        foreach ($rows as $index => $row) {
            $dropWeight = (int) $row['drop_weight'];
            $cardCount = (int) $row['card_count'];

            if ($dropWeight > 0 && $cardCount > 0 && $totalWeight > 0) {
                $percent = (float) round(($dropWeight / $totalWeight) * 100, 1);
            } else {
                $percent = 0.0;
            }

            $level = $count <= 1 ? 1 : 1 + (int) floor($index * 4 / ($count - 1));

            $odds[] = new RarityOdds(
                $row['code'],
                $row['name'],
                $dropWeight,
                $cardCount,
                $level,
                $percent,
            );
        }

        return $odds;
    }
}
