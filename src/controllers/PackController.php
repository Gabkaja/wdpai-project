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

        $this->render('packs', [
            'pageTitle' => 'Packs – StreamRip',
            'points' => $user->getPoints(),
            'unopenedPacks' => $user->getUnopenedPacks(),
            'packSettings' => $packSettings,
            'userSettings' => $userSettings,
            'rarities' => $rarities,
            'rarityLevels' => $this->rarityLevels($rarities),
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

        $rarities = (new RarityRepository($pdo))->odds();

        $this->render('pack', [
            'pageTitle' => 'Pack opening – StreamRip',
            'opening' => $opening,
            'openingCards' => $opening->getCards(),
            'rarityLevels' => $this->rarityLevels($rarities),
        ]);
    }

    private function rarityLevels(array $rarities): array
    {
        $levels = [];

        foreach ($rarities as $rarity) {
            $levels[$rarity->getCode()] = $rarity->getLevel();
        }

        return $levels;
    }
}
