<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Model\CollectionItem;
use App\Model\Rarity;
use App\Repository\CollectionRepository;
use App\Repository\RarityRepository;
use App\Repository\UserRepository;
use App\Repository\UserSettingsRepository;
use PDO;

final class CollectionApiController extends AppController
{
    private const LIMIT = 24;

    private const ALLOWED_SORTS = ['rarity', 'name', 'quantity', 'recent'];

    public function collection(): void
    {
        $user = $this->requireLoginJson();

        $pdo = (new Database())->connect();
        $data = $this->collectionData($pdo, $user->getId());

        $this->json(200, [
            'ok' => true,
            'data' => $data,
        ]);
    }

    public function userCollection(int $id): void
    {
        $pdo = (new Database())->connect();
        $owner = (new UserRepository($pdo))->findById($id);

        if ($owner === null) {
            $this->jsonError(404, 'not_found', 'User not found.');
        }

        $viewer = $this->currentUser();
        $settings = (new UserSettingsRepository($pdo))->find($id);
        $isOwner = $viewer !== null && $viewer->getId() === $id;
        $isAdmin = $viewer !== null && $viewer->getRole() === 'admin';

        if ($settings->getCollectionVisibility() !== 'public' && !$isOwner && !$isAdmin) {
            $this->jsonError(403, 'forbidden', 'This collection is private.');
        }

        $data = $this->collectionData($pdo, $id);
        $data['owner'] = [
            'id' => $owner->getId(),
            'username' => $owner->getUsername(),
        ];

        $this->json(200, [
            'ok' => true,
            'data' => $data,
        ]);
    }

    private function collectionData(PDO $pdo, int $userId): array
    {
        $fields = [];

        $sort = $_GET['sort'] ?? 'rarity';
        if (!in_array($sort, self::ALLOWED_SORTS, true)) {
            $fields['sort'] = 'Invalid sort value.';
        }

        $rarity = $_GET['rarity'] ?? null;

        $query = null;
        $queryRaw = $_GET['q'] ?? null;
        if ($queryRaw !== null) {
            if (!is_string($queryRaw) || strlen($queryRaw) > 50) {
                $fields['q'] = 'Query must be at most 50 characters.';
            } else {
                $query = $queryRaw;
            }
        }

        $offsetRaw = $_GET['offset'] ?? '0';
        if (!is_string($offsetRaw) || preg_match('/^\d+$/', $offsetRaw) !== 1) {
            $fields['offset'] = 'Offset must be a non-negative integer.';
        }
        $offset = (int) $offsetRaw;

        if ($fields !== []) {
            $this->jsonError(422, 'validation_failed', 'Validation failed.', $fields);
        }

        if ($rarity !== null) {
            $rarityCodes = array_map(
                fn (Rarity $r): string => $r->getCode(),
                (new RarityRepository($pdo))->all()
            );

            if (!in_array($rarity, $rarityCodes, true)) {
                $this->jsonError(422, 'validation_failed', 'Rarity does not exist.', ['rarity' => 'Rarity does not exist.']);
            }
        }

        if ($query !== null) {
            $query = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $query) . '%';
        }

        $repository = new CollectionRepository($pdo);
        $rows = $repository->collectionPage($userId, $rarity, $query, $sort, self::LIMIT + 1, $offset);

        $hasMore = count($rows) > self::LIMIT;
        if ($hasMore) {
            array_pop($rows);
        }

        $items = array_map(
            fn (CollectionItem $item): array => [
                'id' => $item->getCard()->getId(),
                'name' => $item->getCard()->getName(),
                'code' => $item->getCard()->getCode(),
                'icon' => $item->getCard()->getIcon(),
                'description' => $item->getCard()->getDescription(),
                'rarity_code' => $item->getCard()->getRarityCode(),
                'rarity_name' => $item->getCard()->getRarityName(),
                'quantity' => $item->getQuantity(),
            ],
            $rows
        );

        return [
            'items' => $items,
            'has_more' => $hasMore,
            'next_offset' => $offset + count($items),
            'summary' => $repository->collectionSummary($userId),
        ];
    }
}
