<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Card;
use App\Model\CollectionItem;
use DateTimeImmutable;
use PDO;

final class CollectionRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function forUser(int $userId): array
    {
        $statement = $this->pdo->prepare('
            SELECT c.id AS card_id, c.name AS card_name, c.code AS card_code, c.drop_chance AS card_drop_chance, c.icon AS card_icon, c.description AS card_description, c.created_at AS card_created_at,
                   r.code AS rarity_code, r.name AS rarity_name, r.sort_order AS rarity_sort_order,
                   uc.quantity AS quantity, uc.acquired_at AS acquired_at
            FROM user_cards uc
            JOIN cards c ON c.id = uc.card_id
            JOIN rarities r ON r.id = c.rarity_id
            WHERE uc.user_id = :user_id
            ORDER BY r.sort_order DESC, c.name
        ');
        $statement->execute([':user_id' => $userId]);

        $items = [];

        foreach ($statement->fetchAll() as $row) {
            $card = new Card(
                (int) $row['card_id'],
                $row['card_name'],
                $row['rarity_code'],
                $row['rarity_name'],
                $row['card_code'],
                (float) $row['card_drop_chance'],
                $row['card_icon'],
                $row['card_description'],
                $row['card_created_at']
            );

            $items[] = new CollectionItem(
                $card,
                (int) $row['quantity'],
                new DateTimeImmutable($row['acquired_at'])
            );
        }

        return $items;
    }
}
