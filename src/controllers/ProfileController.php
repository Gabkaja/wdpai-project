<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Repository\CollectionRepository;
use App\Repository\LeaderboardRepository;

final class ProfileController extends AppController
{
    public function index(): void
    {
        $user = $this->requireLogin();
        $this->noCache();

        $pdo = (new Database())->connect();
        $collectionRepository = new CollectionRepository($pdo);
        $collection = $collectionRepository->forUser($user->getId());
        $entry = (new LeaderboardRepository($pdo))->findByUserId($user->getId());

        $totalCards = 0;

        foreach ($collection as $item) {
            $totalCards += $item->getQuantity();
        }

        $this->render('profile', [
            'pageTitle' => 'My profile – StreamRip',
            'username' => $user->getUsername(),
            'role' => $user->getRole(),
            'points' => $user->getPoints(),
            'unopenedPacks' => $user->getUnopenedPacks(),
            'joinedAt' => $entry?->getJoinedAt() ?? $user->getCreatedAt(),
            'rank' => $entry?->getRankPosition() ?? 0,
            'totalCards' => $entry?->getTotalCards() ?? $totalCards,
            'uniqueCards' => $entry?->getUniqueCards() ?? count($collection),
            'packsOpened' => $entry?->getPacksOpened() ?? 0,
            'rarestCard' => $entry?->getRarestCardName(),
            'collection' => $collection,
            'avatarPath' => $user->getAvatarPath(),
            'avatarUrl' => $this->avatarUrl($user->getAvatarPath()),
        ]);
    }
}
