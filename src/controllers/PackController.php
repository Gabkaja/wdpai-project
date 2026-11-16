<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Repository\LogRepository;
use App\Repository\PackRepository;
use App\Repository\RarityRepository;
use App\Repository\UserSettingsRepository;

final class PackController extends AppController
{
    public function index(): void
    {
        $user = $this->requireLogin();
        $this->noCache();

        $pdo = (new Database())->connect();
        $packRepository = new PackRepository($pdo, new LogRepository($pdo));
        $rarityRepository = new RarityRepository($pdo);
        $userSettingsRepository = new UserSettingsRepository($pdo);

        $packSettings = $packRepository->settings();
        $userSettings = $userSettingsRepository->find($user->getId());
        $rarities = $rarityRepository->odds();
        $history = $packRepository->historyByUser($user->getId(), 0, 10);

        $rarityLevels = [];

        foreach ($rarities as $rarity) {
            $rarityLevels[$rarity->getCode()] = $rarity->getLevel();
        }

        $this->render('packs', [
            'pageTitle' => 'Packs – StreamRip',
            'points' => $user->getPoints(),
            'unopenedPacks' => $user->getUnopenedPacks(),
            'packSettings' => $packSettings,
            'userSettings' => $userSettings,
            'rarities' => $rarities,
            'rarityLevels' => $rarityLevels,
            'history' => $history['items'],
            'historyHasMore' => $history['hasMore'],
            'historyNextOffset' => count($history['items']),
        ]);
    }

    public function show(int $id): void
    {
        $user = $this->requireLogin();
        $this->noCache();

        $openingId = (int) $id;

        $pdo = (new Database())->connect();
        $packRepository = new PackRepository($pdo, new LogRepository($pdo));

        $opening = $packRepository->findOpening($openingId);

        if ($opening === null || $opening->getUserId() !== $user->getId()) {
            http_response_code(404);
            $this->noCache();
            (new DefaultController())->notFound();
            return;
        }

        $settings = $packRepository->settings();
        $cards = [];

        foreach ($opening->getCards() as $openingCard) {
            $cards[] = $openingCard->getCard();
        }

        $this->render('pack', [
            'pageTitle' => 'Pack opening – StreamRip',
            'opening' => $opening,
            'cards' => $cards,
            'openedAt' => $opening->getOpenedAt()->format('Y-m-d H:i:s'),
            'cardsPerPack' => $settings['cards_per_pack'],
        ]);
    }
}
