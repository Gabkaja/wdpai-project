<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Card;
use App\Model\PackOpening;
use DateTimeImmutable;
use PDO;

final class PackRepository
{
    private PDO $pdo;
    private LogRepository $logRepository;

    public function __construct(PDO $pdo, LogRepository $logRepository)
    {
        $this->pdo = $pdo;
        $this->logRepository = $logRepository;
    }

    public function open(int $userId): int
    {
        $this->pdo->beginTransaction();

        try {
            $statement = $this->pdo->prepare('SELECT points FROM users WHERE id = :id FOR UPDATE');
            $statement->execute([':id' => $userId]);
            $row = $statement->fetch();

            if ($row === false) {
                throw new \DomainException('user_not_found');
            }

            $points = (int) $row['points'];

            $statement = $this->pdo->query('SELECT cards_per_pack, cost_points FROM pack_settings WHERE id = 1');
            $settings = $statement->fetch();

            if ($settings === false) {
                throw new \DomainException('pack_not_configured');
            }

            $cardsPerPack = (int) $settings['cards_per_pack'];
            $costPoints = (int) $settings['cost_points'];

            if ($points < $costPoints) {
                throw new \DomainException('insufficient_points');
            }

            $statement = $this->pdo->prepare('
                SELECT r.id AS rarity_id, r.code AS rarity_code, r.name AS rarity_name, r.sort_order AS rarity_sort_order, r.drop_weight AS rarity_drop_weight,
                       c.id AS card_id, c.name AS card_name, c.code AS card_code, c.drop_chance AS card_drop_chance, c.icon AS card_icon, c.description AS card_description, c.created_at AS card_created_at
                FROM rarities r
                JOIN cards c ON c.rarity_id = r.id
                WHERE r.drop_weight > 0
                ORDER BY r.sort_order, c.id
            ');
            $statement->execute();

            $rows = $statement->fetchAll();

            if ($rows === false || $rows === []) {
                throw new \DomainException('pack_not_configured');
            }

            $rarities = [];
            $cardsByRarity = [];
            $totalWeight = 0;

            foreach ($rows as $row) {
                $rarityId = (int) $row['rarity_id'];
                $dropWeight = (int) $row['rarity_drop_weight'];

                if (! isset($rarities[$rarityId])) {
                    $rarities[$rarityId] = [
                        'id' => $rarityId,
                        'code' => $row['rarity_code'],
                        'name' => $row['rarity_name'],
                        'sortOrder' => (int) $row['rarity_sort_order'],
                        'dropWeight' => $dropWeight,
                    ];
                    $totalWeight += $dropWeight;
                    $cardsByRarity[$rarityId] = [];
                }

                $cardsByRarity[$rarityId][] = new Card(
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
            }

            if ($totalWeight <= 0) {
                throw new \DomainException('pack_not_configured');
            }

            $drawnCards = [];

            for ($i = 0; $i < $cardsPerPack; $i++) {
                $roll = random_int(1, $totalWeight);
                $accumulated = 0;
                $selectedRarityId = null;

                foreach ($rarities as $rarityId => $rarity) {
                    $accumulated += $rarity['dropWeight'];
                    if ($roll <= $accumulated) {
                        $selectedRarityId = $rarityId;
                        break;
                    }
                }

                $rarityCards = $cardsByRarity[$selectedRarityId];
                $cardIndex = random_int(0, count($rarityCards) - 1);
                $drawnCards[] = $rarityCards[$cardIndex];
            }

            $statement = $this->pdo->prepare('UPDATE users SET points = points - :cost WHERE id = :id');
            $statement->execute([':cost' => $costPoints, ':id' => $userId]);

            $statement = $this->pdo->prepare('INSERT INTO pack_openings (user_id, points_spent) VALUES (:user_id, :points_spent) RETURNING id');
            $statement->execute([':user_id' => $userId, ':points_spent' => $costPoints]);
            $openingId = (int) $statement->fetchColumn();

            $slot = 1;
            $cardIds = [];

            foreach ($drawnCards as $card) {
                $statement = $this->pdo->prepare('INSERT INTO pack_opening_cards (opening_id, slot, card_id) VALUES (:opening_id, :slot, :card_id)');
                $statement->execute([':opening_id' => $openingId, ':slot' => $slot, ':card_id' => $card->getId()]);

                $statement = $this->pdo->prepare('INSERT INTO user_cards (user_id, card_id) VALUES (:user_id, :card_id) ON CONFLICT (user_id, card_id) DO UPDATE SET quantity = user_cards.quantity + 1');
                $statement->execute([':user_id' => $userId, ':card_id' => $card->getId()]);

                $cardIds[] = $card->getId();
                $slot++;
            }

            $this->logRepository->add('info', 'Pack opened', ['opening_id' => $openingId, 'points_spent' => $costPoints, 'card_ids' => $cardIds], $userId);

            $this->pdo->commit();

            return $openingId;
        } catch (\Throwable $throwable) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $throwable;
        }
    }

    public function findOpening(int $openingId): ?PackOpening
    {
        $statement = $this->pdo->prepare('
            SELECT po.id AS opening_id, po.user_id, po.points_spent, po.opened_at,
                   c.id AS card_id, c.name AS card_name, c.code AS card_code, c.drop_chance AS card_drop_chance, c.icon AS card_icon, c.description AS card_description, c.created_at AS card_created_at,
                   r.code AS rarity_code, r.name AS rarity_name
            FROM pack_openings po
            JOIN pack_opening_cards poc ON poc.opening_id = po.id
            JOIN cards c ON c.id = poc.card_id
            JOIN rarities r ON r.id = c.rarity_id
            WHERE po.id = :id
            ORDER BY poc.slot
        ');
        $statement->execute([':id' => $openingId]);
        $rows = $statement->fetchAll();

        if ($rows === false || $rows === []) {
            return null;
        }

        $firstRow = $rows[0];
        $opening = new PackOpening(
            (int) $firstRow['opening_id'],
            (int) $firstRow['user_id'],
            (int) $firstRow['points_spent'],
            new DateTimeImmutable($firstRow['opened_at'])
        );

        $cards = [];

        foreach ($rows as $row) {
            $cards[] = new Card(
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
        }

        $opening = new PackOpening(
            (int) $firstRow['opening_id'],
            (int) $firstRow['user_id'],
            (int) $firstRow['points_spent'],
            new DateTimeImmutable($firstRow['opened_at']),
            $cards
        );

        return $opening;
    }

    public function settings(): array
    {
        $statement = $this->pdo->query('SELECT cards_per_pack, cost_points FROM pack_settings WHERE id = 1');
        $row = $statement->fetch();

        if ($row === false) {
            return ['cards_per_pack' => 4, 'cost_points' => 100];
        }

        return [
            'cards_per_pack' => (int) $row['cards_per_pack'],
            'cost_points' => (int) $row['cost_points'],
        ];
    }

    public function recentByUser(int $userId, int $limit): array
    {
        $statement = $this->pdo->prepare('
            SELECT po.id AS opening_id, po.user_id, po.points_spent, po.opened_at,
                   c.id AS card_id, c.name AS card_name, c.code AS card_code, c.drop_chance AS card_drop_chance, c.icon AS card_icon, c.description AS card_description, c.created_at AS card_created_at,
                   r.code AS rarity_code, r.name AS rarity_name
            FROM (
                SELECT id FROM pack_openings
                WHERE user_id = :user_id
                ORDER BY opened_at DESC, id DESC
                LIMIT :limit
            ) recent
            JOIN pack_openings po ON po.id = recent.id
            LEFT JOIN pack_opening_cards poc ON poc.opening_id = po.id
            LEFT JOIN cards c ON c.id = poc.card_id
            LEFT JOIN rarities r ON r.id = c.rarity_id
            ORDER BY po.opened_at DESC, po.id DESC, poc.slot
        ');
        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();
        $rows = $statement->fetchAll();

        $openings = [];

        foreach ($rows as $row) {
            $openingId = (int) $row['opening_id'];

            if (! isset($openings[$openingId])) {
                $openings[$openingId] = new PackOpening(
                    $openingId,
                    (int) $row['user_id'],
                    (int) $row['points_spent'],
                    new DateTimeImmutable($row['opened_at'])
                );
            }

            if ($row['card_id'] !== null) {
                $openings[$openingId]->getCards()[] = new Card(
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
            }
        }

        return array_values($openings);
    }
}
