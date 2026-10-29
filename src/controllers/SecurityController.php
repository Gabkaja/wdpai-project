<?php

declare(strict_types=1);

namespace App\Controller;

final class SecurityController extends AppController
{
    public function login(): void
    {
        $this->render('login', [
            'pageTitle' => 'Log in – StreamRip',
        ]);
    }

    public function register(): void
    {
        $this->render('register', [
            'pageTitle' => 'Create account – StreamRip',
        ]);
    }
}