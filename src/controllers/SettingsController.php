<?php

declare(strict_types=1);

namespace App\Controller;

final class SettingsController extends AppController
{
    public function index(): void
    {
        $this->requireLogin();
        $this->redirect('/settings/account');
    }

    public function account(): void
    {
        $this->requireLogin();
        $this->render('settings/account', [
            'pageTitle' => 'Account settings – StreamRip',
            'section' => 'account',
        ]);
    }

    public function packs(): void
    {
        $this->requireLogin();
        $this->render('settings/packs', [
            'pageTitle' => 'Pack settings – StreamRip',
            'section' => 'packs',
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
