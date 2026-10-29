<?php

declare(strict_types=1);

namespace App\Controller;

final class LeaderboardController extends AppController
{
    public function index(): void
    {
        $players = [
            ['rank' => 1, 'username' => 'ClipMaster', 'uniqueCards' => 8, 'packsOpened' => 54, 'joined' => '2026-01-12', 'rarestCard' => 'Source Code Dragon'],
            ['rank' => 2, 'username' => 'HypeConductor', 'uniqueCards' => 7, 'packsOpened' => 47, 'joined' => '2026-02-03', 'rarestCard' => 'System Annihilator'],
            ['rank' => 3, 'username' => 'LurkerLarry99', 'uniqueCards' => 7, 'packsOpened' => 39, 'joined' => '2026-02-20', 'rarestCard' => 'System Annihilator'],
            ['rank' => 4, 'username' => 'EmoteGoblin', 'uniqueCards' => 6, 'packsOpened' => 33, 'joined' => '2026-03-08', 'rarestCard' => 'Time Glitch'],
            ['rank' => 5, 'username' => 'RageQuitRhino', 'uniqueCards' => 5, 'packsOpened' => 28, 'joined' => '2026-04-15', 'rarestCard' => 'Rage Quit Rhino'],
            ['rank' => 6, 'username' => 'ViewerOne', 'uniqueCards' => 4, 'packsOpened' => 21, 'joined' => '2026-05-01', 'rarestCard' => 'System Annihilator'],
        ];

        $this->render('leaderboard', [
            'pageTitle' => 'Leaderboard – StreamRip',
            'podium' => array_slice($players, 0, 3),
            'players' => $players,
            'currentPage' => 1,
            'totalPages' => 3,
        ]);
    }
}