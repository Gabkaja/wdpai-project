<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Repository\KickAccountRepository;
use App\Repository\UserSettingsRepository;

final class SettingsController extends AppController
{
    public function index(): void
    {
        $this->requireLogin();
        $this->redirect('/settings/account');
    }

    public function account(): void
    {
        $user = $this->requireLogin();
        $pdo = (new Database())->connect();
        $kickAccount = (new KickAccountRepository($pdo))->findByUserId($user->getId());

        $this->render('settings/account', [
            'pageTitle' => 'Account settings – StreamRip',
            'section' => 'account',
            'kickAccount' => $kickAccount,
            'avatarUrl' => $this->avatarUrl($user->getAvatarPath()),
        ]);
    }

    public function packs(): void
    {
        $user = $this->requireLogin();
        $pdo = (new Database())->connect();
        $userSettings = (new UserSettingsRepository($pdo))->find($user->getId());

        $this->render('settings/packs', [
            'pageTitle' => 'Pack settings – StreamRip',
            'section' => 'packs',
            'userSettings' => $userSettings,
        ]);
    }

    public function privacy(): void
    {
        $this->requireLogin();
        $this->render('settings/privacy', [
            'pageTitle' => 'Privacy settings – StreamRip',
            'section' => 'privacy',
        ]);
    }

    public function performance(): void
    {
        $this->requireLogin();
        $this->render('settings/performance', [
            'pageTitle' => 'Performance settings – StreamRip',
            'section' => 'performance',
        ]);
    }
}
