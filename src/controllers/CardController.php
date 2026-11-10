<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Repository\CardRepository;

final class CardController extends AppController
{
    private CardRepository $cardRepository;

    public function __construct()
    {
        $this->cardRepository = new CardRepository((new Database())->connect());
    }

    public function index(): void
    {
        $this->render('cards', [
            'pageTitle' => 'All cards – StreamRip',
            'cards' => $this->cardRepository->all(),
        ]);
    }

    public function show(int $id): void
    {
        $card = $this->cardRepository->find($id);

        if ($card === null) {
            http_response_code(404);
            $this->render('not-found', [
                'pageTitle' => 'Card not found – StreamRip',
            ]);

            return;
        }

        $this->render('card', [
            'pageTitle' => $card->getName() . ' – StreamRip',
            'card' => $card,
        ]);
    }
}
