<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Repository\CardRepository;
use App\Repository\RarityRepository;

final class DefaultController extends AppController
{
    private CardRepository $cardRepository;
    private RarityRepository $rarityRepository;

    public function __construct()
    {
        $pdo = (new Database())->connect();
        $this->cardRepository = new CardRepository($pdo);
        $this->rarityRepository = new RarityRepository($pdo);
    }

    public function index(): void
    {
        $cards = $this->cardRepository->all();
        $rarities = $this->rarityRepository->all();

        $rarityCounts = [];

        foreach ($cards as $card) {
            $code = $card->getRarityCode();
            $rarityCounts[$code] = ($rarityCounts[$code] ?? 0) + 1;
        }

        $this->render('home', [
            'pageTitle' => 'StreamRip – Card Catalog',
            'stream' => [
                'isLive' => true,
                'title' => 'HUGE TCG CARD OPENING EDITION 1! 🔥 DROPPING LEGENDARIES FOR VIEWERS',
                'channel' => 'kick.com/StreamRipOfficial',
                'channelUrl' => 'https://kick.com/StreamRipOfficial',
                'category' => 'TCG / Card Opening • Season 1',
                'chatStatus' => 'Active',
                'viewers' => 1420,
                'server' => 'EU-Warsaw • Sync Pool #184',
            ],
            'cards' => $cards,
            'rarities' => $rarities,
            'rarityCounts' => $rarityCounts,
        ]);
    }

    public function notFound(): void
    {
        $this->render('not-found', [
            'pageTitle' => 'Page not found – StreamRip',
        ]);
    }
}
