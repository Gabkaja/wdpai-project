<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CardRepository;

final class CardController extends AppController
{
    public function index(): void
    {
        $this->render('cards', [
            'pageTitle' => 'All cards – StreamRip',
            'cards' => (new CardRepository())->all(),
        ]);
    }

    public function show(int $id): void
    {
        $card = (new CardRepository())->find($id);

        if ($card === null) {
            http_response_code(404);
            $this->render('not-found', [
                'pageTitle' => 'Card not found – StreamRip',
            ]);

            return;
        }

        $this->render('card', [
            'pageTitle' => $card['name'] . ' – StreamRip',
            'card' => $card,
        ]);
    }
}