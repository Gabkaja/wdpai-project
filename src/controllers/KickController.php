<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Repository\KickAccountRepository;
use App\Repository\LogRepository;
use App\Service\KickClient;
use App\Session;

final class KickController extends AppController
{
    private KickClient $kickClient;

    private KickAccountRepository $kickAccountRepository;

    private LogRepository $logRepository;

    public function __construct()
    {
        $pdo = (new Database())->connect();
        $this->kickClient = new KickClient();
        $this->kickAccountRepository = new KickAccountRepository($pdo);
        $this->logRepository = new LogRepository($pdo);
    }

    public function login(): void
    {
        if ($this->currentUser() !== null) {
            $this->redirect('/profile');
        }

        $verifier = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        $challenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
        $state = bin2hex(random_bytes(32));

        Session::storeKickOAuth($state, $verifier, 'login', null);

        $this->redirect($this->kickClient->buildAuthorizationUrl($state, $challenge));
    }

    public function connect(): void
    {
        $user = $this->requireLogin();

        $verifier = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        $challenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
        $state = bin2hex(random_bytes(32));

        Session::storeKickOAuth($state, $verifier, 'link', $user->getId());

        $this->redirect($this->kickClient->buildAuthorizationUrl($state, $challenge));
    }

    public function callback(): void
    {
        $this->noCache();

        $flashTarget = '/login';
        if ($this->currentUser() !== null) {
            $flashTarget = '/profile';
        }

        $oauth = Session::consumeKickOAuth();

        if ($oauth === null) {
            Session::flash('error', 'Kick login failed. Please try again.');
            $this->redirect($flashTarget);
        }

        $state = $_GET['state'] ?? null;

        if ($state === null || !hash_equals($oauth['state'], $state)) {
            Session::flash('error', 'Kick login failed. Please try again.');
            $this->redirect($flashTarget);
        }

        if (isset($_GET['error'])) {
            Session::flash('error', 'Kick login failed. Please try again.');
            $this->redirect($flashTarget);
        }

        $code = $_GET['code'] ?? null;

        if ($code === null) {
            Session::flash('error', 'Kick login failed. Please try again.');
            $this->redirect($flashTarget);
        }

        try {
            $accessToken = $this->kickClient->exchangeCode($code, $oauth['verifier']);
            $kickUser = $this->kickClient->fetchCurrentUser($accessToken);
        } catch (\Throwable $exception) {
            $this->logRepository->add('error', 'Kick OAuth callback failed', [
                'class' => get_class($exception),
                'message' => $exception->getMessage(),
            ]);
            Session::flash('error', 'Kick login failed. Please try again.');
            $this->redirect($flashTarget);
        }

        if ($oauth['purpose'] === 'login') {
            $linkedUserId = $this->kickAccountRepository->findUserIdByKickId($kickUser['id']);

            if ($linkedUserId === null) {
                Session::flash('error', 'No StreamRip account is linked to this Kick account. Log in with your email and connect Kick from your profile.');
                $this->redirect('/login');
            }

            Session::login($linkedUserId);
            $this->redirect('/profile');
        }

        if ($oauth['user_id'] === null || $oauth['user_id'] !== $this->currentUser()->getId()) {
            Session::flash('error', 'Kick login failed. Please try again.');
            $this->redirect('/profile');
        }

        try {
            $this->kickAccountRepository->link($this->currentUser()->getId(), $kickUser['id'], $kickUser['username']);
            $this->logRepository->add('info', 'Kick account linked', [
                'user_id' => $this->currentUser()->getId(),
                'kick_user_id' => $kickUser['id'],
            ]);
            Session::flash('success', 'Kick account connected.');
        } catch (\PDOException $exception) {
            if ($exception->getCode() === '23505') {
                Session::flash('error', 'This Kick account is already linked to another StreamRip account.');
            } else {
                $this->logRepository->add('error', 'Kick account link failed', [
                    'class' => get_class($exception),
                    'message' => $exception->getMessage(),
                ]);
                Session::flash('error', 'Kick login failed. Please try again.');
            }
        }

        $this->redirect('/profile');
    }

    public function unlink(): void
    {
        $this->verifyCsrfOrAbort();

        $user = $this->requireLogin();

        $this->kickAccountRepository->unlink($user->getId());
        $this->logRepository->add('info', 'Kick account unlinked', [
            'user_id' => $user->getId(),
        ]);
        Session::flash('success', 'Kick account disconnected.');

        $this->redirect('/profile');
    }
}
