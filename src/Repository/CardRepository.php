<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Card;
use PDO;

final class CardRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(): array
    {
        $statement = $this->pdo->query('SELECT c.id, c.name, c.code, c.drop_chance, c.icon, c.description, c.created_at, r.code AS rarity_code, r.name AS rarity_name FROM cards c JOIN rarities r ON r.id = c.rarity_id ORDER BY c.id');

        $cards = [];

        foreach ($statement->fetchAll() as $row) {
            $cards[] = $this->mapRowToCard($row);
        }

        return $cards;
    }

    public function find(int $id): ?Card
    {
        $statement = $this->pdo->prepare('SELECT c.id, c.name, c.code, c.drop_chance, c.icon, c.description, c.created_at, r.code AS rarity_code, r.name AS rarity_name FROM cards c JOIN rarities r ON r.id = c.rarity_id WHERE c.id = :id');

        $statement->execute([':id' => $id]);

        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        return $this->mapRowToCard($row);
    }

    private function mapRowToCard(array $row): Card
    {
        return new Card(
            (int) $row['id'],
            $row['name'],
            $row['rarity_code'],
            $row['rarity_name'],
            $row['code'],
            (float) $row['drop_chance'],
            $row['icon'],
            $row['description'],
            $row['created_at']
        );
    }
}
