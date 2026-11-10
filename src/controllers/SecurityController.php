<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Repository\UserRepository;
use App\Session;

final class SecurityController extends AppController
{
    private const DUMMY_HASH = '$2y$10$abcdefghijklmnopqrstuvABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    private UserRepository $userRepository;

    public function __construct()
    {
        $pdo = (new Database())->connect();
        $this->userRepository = new UserRepository($pdo);
    }

    public function login(): void
    {
        if ($this->currentUser() !== null) {
            $this->redirect('/profile');
        }

        $this->noCache();
        $this->render('login', [
            'pageTitle' => 'Log in – StreamRip',
        ]);
    }

    public function register(): void
    {
        if ($this->currentUser() !== null) {
            $this->redirect('/profile');
        }

        $this->noCache();
        $this->render('register', [
            'pageTitle' => 'Create account – StreamRip',
        ]);
    }

    public function registerSubmit(): void
    {
        $this->verifyCsrfOrAbort();

        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordRepeat = $_POST['password_repeat'] ?? '';

        $errors = [];

        if (!preg_match('/^[A-Za-z0-9_]{3,50}$/', $username)) {
            $errors['username'] = 'Username must be 3 to 50 characters and contain only letters, numbers and underscores.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) {
            $errors['email'] = 'Enter a valid e-mail address.';
        }

        if (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters.';
        }

        if ($password !== $passwordRepeat) {
            $errors['password_repeat'] = 'Passwords do not match.';
        }

        if (empty($errors)) {
            if ($this->userRepository->existsByUsername($username) || $this->userRepository->existsByEmail($email)) {
                $errors['__global'] = 'This username or e-mail is already registered.';
            } else {
                try {
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                    $user = $this->userRepository->create($username, $email, $passwordHash);
                    Session::login($user->getId());
                    $this->redirect('/profile');
                } catch (\PDOException $e) {
                    if ($e->getCode() === '23505') {
                        $errors['__global'] = 'This username or e-mail is already registered.';
                    } else {
                        throw $e;
                    }
                }
            }
        }

        if (!empty($errors)) {
            http_response_code(422);
            $this->render('register', [
                'pageTitle' => 'Create account – StreamRip',
                'errors' => $errors,
                'old' => ['username' => $username, 'email' => $email],
            ]);
        }
    }

    public function loginSubmit(): void
    {
        $this->verifyCsrfOrAbort();

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $found = $this->userRepository->findByEmailWithHash($email);

        if ($found === null) {
            password_verify($password, self::DUMMY_HASH);
            $this->loginError('Invalid email or password.', $email);
        }

        $user = $found['user'];
        $passwordHash = $found['password_hash'];

        if (!password_verify($password, $passwordHash)) {
            $this->loginError('Invalid email or password.', $email);
        }

        if (password_needs_rehash($passwordHash, PASSWORD_DEFAULT)) {
            $this->userRepository->updatePasswordHash($user->getId(), password_hash($password, PASSWORD_DEFAULT));
        }

        Session::login($user->getId());
        $this->redirect('/profile');
    }

    public function logout(): void
    {
        $this->verifyCsrfOrAbort();
        Session::logout();
        $this->redirect('/login');
    }

    private function loginError(string $message, string $email): void
    {
        http_response_code(422);
        $this->noCache();
        $this->render('login', [
            'pageTitle' => 'Log in – StreamRip',
            'error' => $message,
            'old' => ['email' => $email],
        ]);
        exit;
    }
}
