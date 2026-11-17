<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Repository\CollectionRepository;
use App\Repository\KickAccountRepository;
use App\Repository\LogRepository;
use App\Repository\PackRepository;

final class ProfileController extends AppController
{
    public function index(): void
    {
        $user = $this->requireLogin();

        $pdo = (new Database())->connect();
        $collectionRepository = new CollectionRepository($pdo);
        $kickAccount = (new KickAccountRepository($pdo))->findByUserId($user->getId());
        $collection = $collectionRepository->forUser($user->getId());

        $totalCards = 0;
        $uniqueCards = count($collection);

        foreach ($collection as $item) {
            $totalCards += $item->getQuantity();
        }

        $packsOpened = (new PackRepository($pdo, new LogRepository($pdo)))->countByUser($user->getId());

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
            'unopenedPacks' => $user->getUnopenedPacks(),
            'collection' => $collection,
            'kickAccount' => $kickAccount,
        ]);
    }
}
