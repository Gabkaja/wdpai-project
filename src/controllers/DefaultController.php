<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Repository\CardRepository;
use App\Repository\RarityRepository;
use App\Service\KickClient;

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

        $kickClient = new KickClient();
        $isLive = $kickClient->isChannelLive('streamripofficial');

        $this->render('home', [
            'pageTitle' => 'StreamRip – Card Catalog',
            'stream' => [
                'isLive' => $isLive,
                'title' => $isLive ? 'HUGE TCG CARD OPENING EDITION 1! 🔥 DROPPING LEGENDARIES FOR VIEWERS' : 'Stream is offline',
                'channel' => 'kick.com/StreamRipOfficial',
                'channelUrl' => 'https://kick.com/StreamRipOfficial',
                'category' => 'TCG / Card Opening • Season 1',
                'chatStatus' => $isLive ? 'Active' : 'Offline',
                'viewers' => $isLive ? 1420 : 0,
                'server' => $isLive ? 'EU-Warsaw • Sync Pool #184' : '—',
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

    public function serverError(): void
    {
        $this->render('500', [
            'pageTitle' => 'Server error – StreamRip',
        ]);
    }

    public function forbidden(): void
    {
        $this->render('403', [
            'pageTitle' => 'Forbidden – StreamRip',
        ]);
    }
}
