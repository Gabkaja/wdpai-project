<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Rarity;
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
}
