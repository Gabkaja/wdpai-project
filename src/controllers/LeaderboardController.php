<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Repository\LeaderboardRepository;

final class LeaderboardController extends AppController
{
    private LeaderboardRepository $leaderboardRepository;

    public function __construct()
    {
        $this->leaderboardRepository = new LeaderboardRepository((new Database())->connect());
    }

    public function index(): void
    {
        $entries = $this->leaderboardRepository->all();

        $this->render('leaderboard', [
            'pageTitle' => 'Leaderboard – StreamRip',
            'podium' => array_slice($entries, 0, 3),
            'players' => $entries,
        ]);
    }
}
