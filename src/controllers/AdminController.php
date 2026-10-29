<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CardRepository;

final class AdminController extends AppController
{
    public function index(): void
    {
        $this->render('admin', [
            'pageTitle' => 'Admin panel – StreamRip',
            'cards' => (new CardRepository())->all(),
            'rarities' => ['common', 'rare', 'epic', 'legendary'],
            'roles' => ['user', 'moderator', 'admin'],
            'users' => [
                ['id' => 1, 'username' => 'ViewerOne', 'email' => 'viewer@example.com', 'role' => 'user'],
                ['id' => 2, 'username' => 'ModMaria', 'email' => 'maria@example.com', 'role' => 'moderator'],
                ['id' => 3, 'username' => 'StreamerBoss', 'email' => 'boss@example.com', 'role' => 'admin'],
            ],
            'logs' => [
                ['time' => '2026-10-26 20:14', 'level' => 'info', 'message' => 'Pack opened for ViewerOne (3 cards).'],
                ['time' => '2026-10-26 20:09', 'level' => 'warning', 'message' => 'Kick chat bot reconnected after timeout.'],
                ['time' => '2026-10-26 19:58', 'level' => 'error', 'message' => 'Failed to deliver overlay event to OBS.'],
            ],
            'cleanup' => [
                'lastRun' => '2026-10-26 03:00',
                'removed' => 14,
                'nextRun' => '2026-10-27 03:00',
            ],
        ]);
    }
}