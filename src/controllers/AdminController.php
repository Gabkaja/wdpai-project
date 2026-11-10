<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Repository\CardRepository;
use App\Repository\LogRepository;
use App\Repository\UserRepository;

final class AdminController extends AppController
{
    private CardRepository $cardRepository;
    private UserRepository $userRepository;
    private LogRepository $logRepository;

    public function __construct()
    {
        $pdo = (new Database())->connect();
        $this->cardRepository = new CardRepository($pdo);
        $this->userRepository = new UserRepository($pdo);
        $this->logRepository = new LogRepository($pdo);
    }

    public function index(): void
    {
        $this->render('admin', [
            'pageTitle' => 'Admin panel – StreamRip',
            'cards' => $this->cardRepository->all(),
            'rarities' => ['common', 'rare', 'epic', 'legendary'],
            'roles' => ['viewer', 'admin'],
            'users' => $this->userRepository->all(),
            'logs' => $this->logRepository->latest(50),
            'cleanup' => [
                'lastRun' => '2026-10-26 03:00',
                'removed' => 14,
                'nextRun' => '2026-10-27 03:00',
            ],
        ]);
    }
}
