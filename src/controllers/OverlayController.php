<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Repository\CardRepository;

final class OverlayController extends AppController
{
    public function index(): void
    {
        $pdo = (new Database())->connect();
        $repository = new CardRepository($pdo);

        $dropSeeds = [
            ['cardId' => 8, 'username' => 'ClipMaster', 'secondsAgo' => 12],
            ['cardId' => 7, 'username' => 'ViewerOne', 'secondsAgo' => 48],
            ['cardId' => 6, 'username' => 'EmoteGoblin', 'secondsAgo' => 132],
            ['cardId' => 5, 'username' => 'HypeConductor', 'secondsAgo' => 245],
            ['cardId' => 3, 'username' => 'LurkerLarry99', 'secondsAgo' => 420],
        ];

        $recentDrops = [];

        foreach ($dropSeeds as $seed) {
            $card = $repository->find($seed['cardId']);

            if ($card === null) {
                continue;
            }

            $recentDrops[] = [
                'card' => $card,
                'username' => $seed['username'],
                'secondsAgo' => $seed['secondsAgo'],
            ];
        }

        $players = [
            ['rank' => 1, 'username' => 'ClipMaster', 'uniqueCards' => 8, 'packsOpened' => 54],
            ['rank' => 2, 'username' => 'HypeConductor', 'uniqueCards' => 7, 'packsOpened' => 47],
            ['rank' => 3, 'username' => 'LurkerLarry99', 'uniqueCards' => 7, 'packsOpened' => 39],
            ['rank' => 4, 'username' => 'EmoteGoblin', 'uniqueCards' => 6, 'packsOpened' => 33],
            ['rank' => 5, 'username' => 'RageQuitRhino', 'uniqueCards' => 5, 'packsOpened' => 28],
        ];

        $this->render('overlay', [
            'pageTitle' => 'StreamRip – Live overlay',
            'recentDrops' => $recentDrops,
            'players' => $players,
            'stats' => [
                'viewers' => 1248,
                'activeCards' => count($repository->all()),
                'packsOpened' => 342,
                'legendaries' => 6,
            ],
            'channel' => 'kick.com/streamrip',
        ], null);
    }
}
