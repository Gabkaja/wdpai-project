<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CardRepository;

final class AboutController extends AppController
{
    public function index(): void
    {
        $this->render('about', [
            'pageTitle' => 'About – StreamRip',
            'edition' => 'Season 1 • EU-Warsaw sync pool',
            'cardCount' => count((new CardRepository())->all()),
            'features' => [
                [
                    'icon' => 'sensors',
                    'rarity' => 'common',
                    'title' => 'Watch the stream',
                    'description' => 'Link your Kick account and stay in the chat while the broadcast is live. Every minute you watch adds activity to your drop pool.',
                ],
                [
                    'icon' => 'inventory_2',
                    'rarity' => 'rare',
                    'title' => 'Collect drops',
                    'description' => 'Cards drop automatically during the stream. Common pulls are frequent, while legendary editions are limited to a fraction of a percent.',
                ],
                [
                    'icon' => 'military_tech',
                    'rarity' => 'epic',
                    'title' => 'Build the collection',
                    'description' => 'Track your completion, climb the community leaderboard and redeem promo codes for exclusive cards from each edition.',
                ],
            ],
        ]);
    }
}
