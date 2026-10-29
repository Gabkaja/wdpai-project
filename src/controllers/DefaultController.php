<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CardRepository;

final class DefaultController extends AppController
{
    public function index(): void
    {
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
            'cards' => (new CardRepository())->all(),
        ]);
    }

    public function notFound(): void
    {
        $this->render('not-found', [
            'pageTitle' => 'Page not found – StreamRip',
        ]);
    }
}