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
            'cards' => (new CardRepository())->all(),
            'isLive' => false,
            'channelUrl' => 'https://kick.com/ripify68',
        ]);
    }

    public function notFound(): void
    {
        $this->render('not-found', [
            'pageTitle' => 'Page not found – StreamRip',
        ]);
    }
}