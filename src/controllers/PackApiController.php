<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Model\PackOpening;
use App\Model\UserSettings;
use App\Repository\LogRepository;
use App\Repository\PackRepository;
use App\Repository\UserRepository;
use App\Repository\UserSettingsRepository;

final class PackApiController extends AppController
{
    public function buy(): void
    {
        $user = $this->requireLoginJson();
        $this->verifyCsrfJson();

        $body = $this->jsonBody();
        $this->rejectUnknownFields($body, ['quantity']);

        $fields = [];

        if (!array_key_exists('quantity', $body)) {
            $fields['quantity'] = 'This field is required.';
        } elseif (!is_int($body['quantity'])) {
            $fields['quantity'] = 'This field must be an integer.';
        }

        if ($fields !== []) {
            $this->jsonError(422, 'validation_failed', 'Validation failed.', $fields);
        }

        $quantity = $body['quantity'];

        $pdo = (new Database())->connect();
        $repository = new PackRepository($pdo, new LogRepository($pdo));

        try {
            $result = $repository->buy($user->getId(), $quantity);
        } catch (\DomainException $exception) {
            $this->handleDomainException($exception, 'quantity');
        }

        $this->json(200, [
            'ok' => true,
            'data' => [
                'points' => $result['points'],
                'unopened_packs' => $result['unopened_packs'],
                'purchased' => $quantity,
                'points_spent' => $result['points_spent'],
            ],
        ]);
    }

    public function open(): void
    {
        $user = $this->requireLoginJson();
        $this->verifyCsrfJson();

        $body = $this->jsonBody();
        $this->rejectUnknownFields($body, ['count']);

        $fields = [];

        if (!array_key_exists('count', $body)) {
            $fields['count'] = 'This field is required.';
        } elseif (!is_int($body['count'])) {
            $fields['count'] = 'This field must be an integer.';
        }

        if ($fields !== []) {
            $this->jsonError(422, 'validation_failed', 'Validation failed.', $fields);
        }

        $count = $body['count'];

        $pdo = (new Database())->connect();
        $repository = new PackRepository($pdo, new LogRepository($pdo));

        try {
            $openingIds = $repository->openFromStock($user->getId(), $count);
        } catch (\DomainException $exception) {
            $this->handleDomainException($exception, 'count');
        }

        $openings = $repository->findOpeningsByIds($user->getId(), $openingIds);
        $freshUser = (new UserRepository($pdo))->findById($user->getId());

        $this->json(200, [
            'ok' => true,
            'data' => [
                'unopened_packs' => $freshUser?->getUnopenedPacks() ?? 0,
                'openings' => array_map(fn (PackOpening $opening): array => $this->serializeOpening($opening), $openings),
            ],
        ]);
    }

    public function saveSettings(): void
    {
        $user = $this->requireLoginJson();
        $this->verifyCsrfJson();

        $body = $this->jsonBody();
        $this->rejectUnknownFields($body, ['reveal_mode', 'multi_open_mode', 'sound_enabled', 'sound_volume']);

        $fields = [];

        if (!array_key_exists('reveal_mode', $body) || !is_string($body['reveal_mode']) || !in_array($body['reveal_mode'], ['auto', 'manual'], true)) {
            $fields['reveal_mode'] = 'This field must be "auto" or "manual".';
        }

        if (!array_key_exists('multi_open_mode', $body) || !is_string($body['multi_open_mode']) || !in_array($body['multi_open_mode'], ['queue', 'all_at_once'], true)) {
            $fields['multi_open_mode'] = 'This field must be "queue" or "all_at_once".';
        }

        if (!array_key_exists('sound_enabled', $body) || !is_bool($body['sound_enabled'])) {
            $fields['sound_enabled'] = 'This field must be a boolean.';
        }

        if (!array_key_exists('sound_volume', $body) || !is_int($body['sound_volume']) || $body['sound_volume'] < 0 || $body['sound_volume'] > 100) {
            $fields['sound_volume'] = 'This field must be an integer between 0 and 100.';
        }

        if ($fields !== []) {
            $this->jsonError(422, 'validation_failed', 'Validation failed.', $fields);
        }

        $pdo = (new Database())->connect();
        $repository = new UserSettingsRepository($pdo);
        $current = $repository->find($user->getId());

        $settings = new UserSettings(
            $body['reveal_mode'],
            $body['multi_open_mode'],
            $body['sound_enabled'],
            $body['sound_volume'],
            $current->getCollectionVisibility(),
            $current->isLiteMode()
        );

        $repository->save($user->getId(), $settings);

        $this->json(200, [
            'ok' => true,
            'data' => [
                'reveal_mode' => $settings->getRevealMode(),
                'multi_open_mode' => $settings->getMultiOpenMode(),
                'sound_enabled' => $settings->isSoundEnabled(),
                'sound_volume' => $settings->getSoundVolume(),
            ],
        ]);
    }

    public function getSettings(): void
    {
        $user = $this->requireLoginJson();

        $pdo = (new Database())->connect();
        $settings = (new UserSettingsRepository($pdo))->find($user->getId());

        $this->json(200, [
            'ok' => true,
            'data' => [
                'reveal_mode' => $settings->getRevealMode(),
                'multi_open_mode' => $settings->getMultiOpenMode(),
                'sound_enabled' => $settings->isSoundEnabled(),
                'sound_volume' => $settings->getSoundVolume(),
            ],
        ]);
    }

    public function history(): void
    {
        $user = $this->requireLoginJson();

        $fields = [];
        $offset = 0;
        $limit = 10;

        $offsetRaw = $_GET['offset'] ?? null;
        $limitRaw = $_GET['limit'] ?? null;

        if ($offsetRaw !== null) {
            if (!is_string($offsetRaw) || preg_match('/^\d+$/', $offsetRaw) !== 1) {
                $fields['offset'] = 'This field must be a non-negative integer.';
            } else {
                $offset = (int) $offsetRaw;
            }
        }

        if ($limitRaw !== null) {
            if (!is_string($limitRaw) || preg_match('/^\d+$/', $limitRaw) !== 1) {
                $fields['limit'] = 'This field must be an integer between 1 and 50.';
            } else {
                $limit = (int) $limitRaw;

                if ($limit < 1 || $limit > 50) {
                    $fields['limit'] = 'This field must be an integer between 1 and 50.';
                }
            }
        }

        if ($fields !== []) {
            $this->jsonError(422, 'validation_failed', 'Validation failed.', $fields);
        }

        $pdo = (new Database())->connect();
        $repository = new PackRepository($pdo, new LogRepository($pdo));
        $history = $repository->historyByUser($user->getId(), $offset, $limit);

        $items = array_map(fn (PackOpening $opening): array => $this->serializeOpening($opening), $history['items']);

        $this->json(200, [
            'ok' => true,
            'data' => [
                'items' => $items,
                'has_more' => $history['hasMore'],
                'next_offset' => $offset + count($history['items']),
            ],
        ]);
    }

    private function serializeOpening(PackOpening $opening): array
    {
        $cards = [];

        foreach ($opening->getCards() as $openingCard) {
            $card = $openingCard->getCard();
            $cards[] = [
                'slot' => $openingCard->getSlot(),
                'is_new' => $openingCard->isNew(),
                'card' => [
                    'id' => $card->getId(),
                    'name' => $card->getName(),
                    'icon' => $card->getIcon(),
                    'description' => $card->getDescription(),
                    'rarity_code' => $card->getRarityCode(),
                    'rarity_name' => $card->getRarityName(),
                ],
            ];
        }

        return [
            'id' => $opening->getId(),
            'opened_at' => $opening->getOpenedAt()->format(DATE_ATOM),
            'cards' => $cards,
        ];
    }

    private function rejectUnknownFields(array $body, array $allowed): void
    {
        $fields = [];

        foreach (array_keys($body) as $key) {
            if (!in_array($key, $allowed, true)) {
                $fields[(string) $key] = 'This field is not allowed.';
            }
        }

        if ($fields !== []) {
            $this->jsonError(422, 'validation_failed', 'Validation failed.', $fields);
        }
    }

    private function handleDomainException(\DomainException $exception, string $field): never
    {
        $message = $exception->getMessage();

        if ($message === 'insufficient_points') {
            $this->jsonError(409, 'insufficient_points', 'Not enough points to buy packs.');
        }

        if ($message === 'no_unopened_packs') {
            $this->jsonError(409, 'no_unopened_packs', 'There are no unopened packs available.');
        }

        if ($message === 'invalid_quantity') {
            $this->jsonError(422, 'invalid_quantity', 'Quantity is out of the allowed range.', [$field => 'Quantity is out of the allowed range.']);
        }

        throw $exception;
    }
}
