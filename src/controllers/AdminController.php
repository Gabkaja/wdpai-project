<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Model\User;
use App\Repository\CardRepository;
use App\Repository\LogRepository;
use App\Repository\RarityRepository;
use App\Repository\UserRepository;
use App\Session;

final class AdminController extends AppController
{
    private CardRepository $cardRepository;
    private UserRepository $userRepository;
    private LogRepository $logRepository;
    private RarityRepository $rarityRepository;

    public function __construct()
    {
        $pdo = (new Database())->connect();
        $this->cardRepository = new CardRepository($pdo);
        $this->userRepository = new UserRepository($pdo);
        $this->logRepository = new LogRepository($pdo);
        $this->rarityRepository = new RarityRepository($pdo);
    }

    public function index(): void
    {
        $user = $this->requireRole('admin');

        $this->render('admin', [
            'pageTitle' => 'Admin panel – StreamRip',
            'cards' => $this->cardRepository->all(),
            'rarities' => $this->rarityRepository->all(),
            'roles' => ['viewer', 'admin'],
            'users' => $this->userRepository->all(),
            'logs' => $this->logRepository->latest(50),
            'cleanup' => [
                'lastRun' => '2026-10-26 03:00',
                'removed' => 14,
                'nextRun' => '2026-10-27 03:00',
            ],
            'currentUser' => $user,
        ]);
    }

    public function grantPoints(int $id): void
    {
        $this->requireRole('admin');
        $this->verifyCsrfOrAbort();

        $amount = filter_var($_POST['amount'] ?? null, FILTER_VALIDATE_INT);

        if ($amount === false || $amount === null || $amount === 0 || $amount < -100000 || $amount > 100000) {
            Session::flash('error', 'Invalid points amount.');
            $this->redirect('/admin');
        }

        $admin = $this->currentUser();

        try {
            $this->userRepository->addPoints($id, $amount);
            $this->logRepository->add('info', 'Admin granted points', [
                'admin_id' => $admin->getId(),
                'target_user_id' => $id,
                'amount' => $amount,
            ], $admin->getId());
            Session::flash('success', 'Points updated.');
        } catch (\DomainException $exception) {
            Session::flash('error', $exception->getMessage() === 'points_out_of_range' ? 'Points out of range.' : 'User not found.');
        }

        $this->redirect('/admin');
    }
}
