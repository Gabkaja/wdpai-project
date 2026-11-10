<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Model\User;
use App\Repository\UserRepository;
use App\Session;

abstract class AppController
{
    protected function currentUser(): ?User
    {
        $userId = Session::userId();

        if ($userId === null) {
            return null;
        }

        $pdo = (new Database())->connect();
        $userRepository = new UserRepository($pdo);
        $user = $userRepository->findById($userId);

        if ($user === null) {
            Session::logout();

            return null;
        }

        return $user;
    }

    protected function requireLogin(): User
    {
        $user = $this->currentUser();

        if ($user === null) {
            http_response_code(302);
            $this->noCache();
            header('Location: /login');
            exit;
        }

        return $user;
    }

    protected function requireRole(string $role): User
    {
        $user = $this->requireLogin();

        if ($user->getRole() !== $role) {
            http_response_code(403);
            $this->noCache();
            (new DefaultController())->forbidden();
            exit;
        }

        return $user;
    }

    protected function redirect(string $path): never
    {
        http_response_code(302);
        header('Location: ' . $path);
        exit;
    }

    protected function noCache(): void
    {
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
    }

    protected function verifyCsrfOrAbort(): void
    {
        $token = $_POST['csrf_token'] ?? null;

        if (!Session::verifyCsrf($token)) {
            http_response_code(403);
            exit;
        }
    }

    protected function render(string $template, array $variables = [], ?string $layout = 'layout'): void
    {
        if ($layout !== null && !isset($variables['currentUser'])) {
            $variables['currentUser'] = $this->currentUser();
        }

        $templatePath = $this->viewPath($template);

        extract($variables, EXTR_SKIP);

        if ($layout === null) {
            require $templatePath;

            return;
        }

        ob_start();
        require $templatePath;
        $content = (string) ob_get_clean();

        require $this->viewPath($layout);
    }

    protected function partial(string $name, array $variables = []): void
    {
        $partialPath = $this->viewPath('partials/' . $name);

        extract($variables, EXTR_SKIP);
        require $partialPath;
    }

    private function viewPath(string $name): string
    {
        $path = dirname(__DIR__, 2) . '/public/views/' . $name . '.html.php';

        if (!is_file($path)) {
            throw new \RuntimeException('View not found: ' . $name);
        }

        return $path;
    }
}