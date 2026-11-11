<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Repository\PackRepository;
use App\Repository\LogRepository;
use App\Session;

final class PackController extends AppController
{
    public function open(): void
    {
        $user = $this->requireLogin();
        $this->verifyCsrfOrAbort();

        $pdo = (new Database())->connect();
        $packRepository = new PackRepository($pdo, new LogRepository($pdo));

        try {
            $openingId = $packRepository->open($user->getId());
            $this->redirect('/packs/' . (int) $openingId);
        } catch (\DomainException $exception) {
            $message = $exception->getMessage();

            if ($message === 'insufficient_points') {
                Session::flash('error', 'Not enough points to open a pack.');
            } else {
                Session::flash('error', 'Could not open pack.');
            }

            $this->redirect('/profile');
        }
    }

    public function show(int $id): void
    {
        $user = $this->requireLogin();
        $this->noCache();

        $openingId = (int) $id;

        $pdo = (new Database())->connect();
        $packRepository = new PackRepository($pdo, new LogRepository($pdo));

        $opening = $packRepository->findOpening($openingId);

        if ($opening === null || $opening->getUserId() !== $user->getId()) {
            http_response_code(404);
            $this->noCache();
            (new DefaultController())->notFound();
            return;
        }

        $settings = $packRepository->settings();

        $this->render('pack', [
            'pageTitle' => 'Pack opening – StreamRip',
            'opening' => $opening,
            'cards' => $opening->getCards(),
            'pointsSpent' => $opening->getPointsSpent(),
            'openedAt' => $opening->getOpenedAt()->format('Y-m-d H:i:s'),
            'cardsPerPack' => $settings['cards_per_pack'],
        ]);
    }
}
