<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CardRepository;

final class ProfileController extends AppController
{
    private const OWNED_CARD_IDS = [1, 3, 5, 7];

    public function index(): void
    {
        $cards = (new CardRepository())->all();
        $ownedIds = self::OWNED_CARD_IDS;

        $this->render('profile', [
            'pageTitle' => 'My profile – StreamRip',
            'username' => 'ViewerOne',
            'stats' => [
                'totalCards' => count($ownedIds),
                'completion' => (int) round(count($ownedIds) / count($cards) * 100),
                'rank' => 12,
                'favoriteCard' => 'Mighty Pog',
            ],
            'cards' => $cards,
            'ownedIds' => $ownedIds,
        ]);
    }
}