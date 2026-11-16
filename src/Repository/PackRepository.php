<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Card;
use App\Model\PackOpening;
use App\Model\PackOpeningCard;
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

    public function settings(): array
    {
        return $this->loadSettings();
    }

    public function buy(int $userId, int $quantity): array
    {
        $this->pdo->beginTransaction();

        try {
            $statement = $this->pdo->prepare('SELECT points, unopened_packs FROM users WHERE id = :id FOR UPDATE');
            $statement->bindValue(':id', $userId, PDO::PARAM_INT);
            $statement->execute();
            $row = $statement->fetch();

            if ($row === false) {
                throw new \DomainException('user_not_found');
            }

            $settings = $this->loadSettings();

            if ($quantity < 1 || $quantity > $settings['max_bulk_packs']) {
                throw new \DomainException('invalid_quantity');
            }

            $points = (int) $row['points'];
            $unopenedPacks = (int) $row['unopened_packs'];
            $total = $settings['cost_points'] * $quantity;

            if ($points < $total) {
                throw new \DomainException('insufficient_points');
            }

            $statement = $this->pdo->prepare('UPDATE users SET points = points - :total, unopened_packs = unopened_packs + :quantity WHERE id = :id');
            $statement->bindValue(':total', $total, PDO::PARAM_INT);
            $statement->bindValue(':quantity', $quantity, PDO::PARAM_INT);
            $statement->bindValue(':id', $userId, PDO::PARAM_INT);
            $statement->execute();

            $statement = $this->pdo->prepare('INSERT INTO pack_purchases (user_id, quantity, points_spent) VALUES (:user_id, :quantity, :points_spent)');
            $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $statement->bindValue(':quantity', $quantity, PDO::PARAM_INT);
            $statement->bindValue(':points_spent', $total, PDO::PARAM_INT);
            $statement->execute();

            $this->logRepository->add('info', 'Packs purchased', ['quantity' => $quantity, 'points_spent' => $total], $userId);

            $this->pdo->commit();

            return [
                'points' => $points - $total,
                'unopened_packs' => $unopenedPacks + $quantity,
                'points_spent' => $total,
            ];
        } catch (\Throwable $throwable) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $throwable;
        }
    }

    public function openFromStock(int $userId, int $count): array
    {
        $this->pdo->beginTransaction();

        try {
            $statement = $this->pdo->prepare('SELECT unopened_packs FROM users WHERE id = :id FOR UPDATE');
            $statement->bindValue(':id', $userId, PDO::PARAM_INT);
            $statement->execute();
            $row = $statement->fetch();

            if ($row === false) {
                throw new \DomainException('user_not_found');
            }

            $settings = $this->loadSettings();

            if ($count < 1 || $count > $settings['max_bulk_packs']) {
                throw new \DomainException('invalid_quantity');
            }

            if ((int) $row['unopened_packs'] < $count) {
                throw new \DomainException('no_unopened_packs');
            }

            $statement = $this->pdo->prepare('UPDATE users SET unopened_packs = unopened_packs - :count WHERE id = :id');
            $statement->bindValue(':count', $count, PDO::PARAM_INT);
            $statement->bindValue(':id', $userId, PDO::PARAM_INT);
            $statement->execute();

            $pool = $this->loadDrawPool();

            $insertOpening = $this->pdo->prepare('INSERT INTO pack_openings (user_id) VALUES (:user_id) RETURNING id');
            $insertUserCard = $this->pdo->prepare('INSERT INTO user_cards (user_id, card_id) VALUES (:user_id, :card_id) ON CONFLICT (user_id, card_id) DO UPDATE SET quantity = user_cards.quantity + 1 RETURNING quantity');
            $insertOpeningCard = $this->pdo->prepare('INSERT INTO pack_opening_cards (opening_id, slot, card_id, is_new) VALUES (:opening_id, :slot, :card_id, :is_new)');

            $openingIds = [];

            for ($i = 0; $i < $count; $i++) {
                $insertOpening->bindValue(':user_id', $userId, PDO::PARAM_INT);
                $insertOpening->execute();
                $openingId = (int) $insertOpening->fetchColumn();

                $cards = $this->drawCards($pool, $settings['cards_per_pack']);
                $slot = 1;

                foreach ($cards as $card) {
                    $insertUserCard->bindValue(':user_id', $userId, PDO::PARAM_INT);
                    $insertUserCard->bindValue(':card_id', $card->getId(), PDO::PARAM_INT);
                    $insertUserCard->execute();
                    $quantity = (int) $insertUserCard->fetchColumn();

                    $insertOpeningCard->bindValue(':opening_id', $openingId, PDO::PARAM_INT);
                    $insertOpeningCard->bindValue(':slot', $slot, PDO::PARAM_INT);
                    $insertOpeningCard->bindValue(':card_id', $card->getId(), PDO::PARAM_INT);
                    $insertOpeningCard->bindValue(':is_new', $quantity === 1, PDO::PARAM_BOOL);
                    $insertOpeningCard->execute();

                    $slot++;
                }

                $openingIds[] = $openingId;
            }

            $this->logRepository->add('info', 'Packs opened', ['count' => $count, 'opening_ids' => $openingIds], $userId);

            $this->pdo->commit();

            return $openingIds;
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
            SELECT po.id AS opening_id, po.user_id, po.opened_at,
                   poc.slot, poc.is_new,
                   c.id AS card_id, c.name AS card_name, c.code AS card_code, c.drop_chance AS card_drop_chance, c.icon AS card_icon, c.description AS card_description, c.created_at AS card_created_at,
                   r.code AS rarity_code, r.name AS rarity_name
            FROM pack_openings po
            LEFT JOIN pack_opening_cards poc ON poc.opening_id = po.id
            LEFT JOIN cards c ON c.id = poc.card_id
            LEFT JOIN rarities r ON r.id = c.rarity_id
            WHERE po.id = :id
            ORDER BY poc.slot
        ');
        $statement->bindValue(':id', $openingId, PDO::PARAM_INT);
        $statement->execute();
        $rows = $statement->fetchAll();

        if ($rows === false || $rows === []) {
            return null;
        }

        $openings = $this->buildOpenings($rows);

        return reset($openings) ?: null;
    }

    public function findOpeningsByIds(int $userId, array $ids): array
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));

        if ($ids === []) {
            return [];
        }

        $placeholders = [];

        foreach ($ids as $index => $id) {
            $placeholders[] = ':id' . $index;
        }

        $statement = $this->pdo->prepare('
            SELECT po.id AS opening_id, po.user_id, po.opened_at,
                   poc.slot, poc.is_new,
                   c.id AS card_id, c.name AS card_name, c.code AS card_code, c.drop_chance AS card_drop_chance, c.icon AS card_icon, c.description AS card_description, c.created_at AS card_created_at,
                   r.code AS rarity_code, r.name AS rarity_name
            FROM pack_openings po
            LEFT JOIN pack_opening_cards poc ON poc.opening_id = po.id
            LEFT JOIN cards c ON c.id = poc.card_id
            LEFT JOIN rarities r ON r.id = c.rarity_id
            WHERE po.user_id = :user_id AND po.id IN (' . implode(', ', $placeholders) . ')
            ORDER BY po.id, poc.slot
        ');
        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);

        foreach ($ids as $index => $id) {
            $statement->bindValue(':id' . $index, $id, PDO::PARAM_INT);
        }

        $statement->execute();
        $openings = $this->buildOpenings($statement->fetchAll());
        ksort($openings, SORT_NUMERIC);

        return array_values($openings);
    }

    public function historyByUser(int $userId, int $offset, int $limit): array
    {
        $limit = max(1, min(50, $limit));
        $offset = max(0, $offset);

        $statement = $this->pdo->prepare('SELECT id FROM pack_openings WHERE user_id = :user_id ORDER BY opened_at DESC, id DESC OFFSET :offset LIMIT :limit');
        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit + 1, PDO::PARAM_INT);
        $statement->execute();
        $rows = $statement->fetchAll();

        $hasMore = count($rows) > $limit;
        $ids = [];

        foreach (array_slice($rows, 0, $limit) as $row) {
            $ids[] = (int) $row['id'];
        }

        if ($ids === []) {
            return ['items' => [], 'hasMore' => false];
        }

        $found = $this->findOpeningsByIds($userId, $ids);
        $ordered = [];

        foreach ($found as $opening) {
            $ordered[$opening->getId()] = $opening;
        }

        $items = [];

        foreach ($ids as $id) {
            if (isset($ordered[$id])) {
                $items[] = $ordered[$id];
            }
        }

        return ['items' => $items, 'hasMore' => $hasMore];
    }

    public function countByUser(int $userId): int
    {
        $statement = $this->pdo->prepare('SELECT COUNT(*) FROM pack_openings WHERE user_id = :user_id');
        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->execute();

        return (int) $statement->fetchColumn();
    }

    public function recentByUser(int $userId, int $limit): array
    {
        $statement = $this->pdo->prepare('
            SELECT po.id AS opening_id, po.user_id, po.opened_at,
                   poc.slot, poc.is_new,
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

        return array_values($this->buildOpenings($statement->fetchAll()));
    }

    private function loadSettings(): array
    {
        $statement = $this->pdo->query('SELECT cards_per_pack, cost_points, max_bulk_packs FROM pack_settings WHERE id = 1');
        $row = $statement->fetch();

        if ($row === false) {
            throw new \DomainException('pack_not_configured');
        }

        return [
            'cards_per_pack' => (int) $row['cards_per_pack'],
            'cost_points' => (int) $row['cost_points'],
            'max_bulk_packs' => (int) $row['max_bulk_packs'],
        ];
    }

    private function loadDrawPool(): array
    {
        $statement = $this->pdo->query('
            SELECT r.id AS rarity_id, r.code AS rarity_code, r.name AS rarity_name, r.sort_order AS rarity_sort_order, r.drop_weight AS rarity_drop_weight,
                   c.id AS card_id, c.name AS card_name, c.code AS card_code, c.drop_chance AS card_drop_chance, c.icon AS card_icon, c.description AS card_description, c.created_at AS card_created_at
            FROM rarities r
            JOIN cards c ON c.rarity_id = r.id
            WHERE r.drop_weight > 0
            ORDER BY r.sort_order, c.id
        ');
        $rows = $statement->fetchAll();

        if ($rows === [] || $rows === false) {
            throw new \DomainException('pack_not_configured');
        }

        $rarities = [];
        $cardsByRarity = [];
        $totalWeight = 0;

        foreach ($rows as $row) {
            $rarityId = (int) $row['rarity_id'];

            if (! isset($rarities[$rarityId])) {
                $rarities[$rarityId] = [
                    'dropWeight' => (int) $row['rarity_drop_weight'],
                ];
                $totalWeight += (int) $row['rarity_drop_weight'];
                $cardsByRarity[$rarityId] = [];
            }

            $cardsByRarity[$rarityId][] = $this->mapRowToCard($row);
        }

        if ($totalWeight <= 0) {
            throw new \DomainException('pack_not_configured');
        }

        return [
            'rarities' => $rarities,
            'cardsByRarity' => $cardsByRarity,
            'totalWeight' => $totalWeight,
        ];
    }

    private function drawCards(array $pool, int $count): array
    {
        $drawnCards = [];

        for ($i = 0; $i < $count; $i++) {
            $roll = random_int(1, $pool['totalWeight']);
            $accumulated = 0;
            $selectedRarityId = null;

            foreach ($pool['rarities'] as $rarityId => $rarity) {
                $accumulated += $rarity['dropWeight'];

                if ($roll <= $accumulated) {
                    $selectedRarityId = $rarityId;
                    break;
                }
            }

            $rarityCards = $pool['cardsByRarity'][$selectedRarityId];
            $cardIndex = random_int(0, count($rarityCards) - 1);
            $drawnCards[] = $rarityCards[$cardIndex];
        }

        return $drawnCards;
    }

    private function buildOpenings(array $rows): array
    {
        $openingRows = [];
        $cards = [];

        foreach ($rows as $row) {
            $openingId = (int) $row['opening_id'];

            if (! isset($openingRows[$openingId])) {
                $openingRows[$openingId] = [
                    'user_id' => (int) $row['user_id'],
                    'opened_at' => $row['opened_at'],
                ];
                $cards[$openingId] = [];
            }

            if ($row['card_id'] !== null) {
                $cards[$openingId][] = new PackOpeningCard(
                    (int) $row['slot'],
                    $this->mapRowToCard($row),
                    (bool) $row['is_new']
                );
            }
        }

        $openings = [];

        foreach ($openingRows as $openingId => $row) {
            $openings[$openingId] = new PackOpening(
                $openingId,
                $row['user_id'],
                new DateTimeImmutable($row['opened_at']),
                $cards[$openingId]
            );
        }

        return $openings;
    }

    private function mapRowToCard(array $row): Card
    {
        return new Card(
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
