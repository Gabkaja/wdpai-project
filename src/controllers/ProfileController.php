<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Repository\CardRepository;
use App\Repository\CollectionRepository;
use App\Repository\KickAccountRepository;
use App\Repository\PackRepository;

final class ProfileController extends AppController
{
    public function index(): void
    {
        $user = $this->requireLogin();

        $pdo = (new Database())->connect();
        $packRepository = new PackRepository($pdo, new \App\Repository\LogRepository($pdo));
        $collectionRepository = new CollectionRepository($pdo);
        $kickAccount = (new KickAccountRepository($pdo))->findByUserId($user->getId());
        $settings = $packRepository->settings();
        $collection = $collectionRepository->forUser($user->getId());
        $recentOpenings = $packRepository->recentByUser($user->getId(), 5);

        $totalCards = 0;
        $uniqueCards = count($collection);

        foreach ($collection as $item) {
            $totalCards += $item->getQuantity();
        }

        $packsOpened = count($recentOpenings);

        $this->render('profile', [
            'pageTitle' => 'My profile – StreamRip',
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'points' => $user->getPoints(),
            'memberSince' => $user->getCreatedAt()->format('F Y'),
            'stats' => [
                'userId' => $user->getId(),
                'totalCards' => $totalCards,
                'uniqueCards' => $uniqueCards,
                'packsOpened' => $packsOpened,
                'completion' => $uniqueCards > 0 ? (int) round($uniqueCards / 8 * 100) : 0,
                'rank' => 12,
                'favoriteCard' => 'System Annihilator',
            ],
            'collection' => $collection,
            'recentOpenings' => $recentOpenings,
            'packSettings' => $settings,
            'kickAccount' => $kickAccount,
        ]);
    }
}
