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

    protected function avatarUrl(?string $avatarPath): ?string
    {
        if ($avatarPath === null || $avatarPath === '') {
            return null;
        }

        $path = ltrim($avatarPath, '/');
        $file = dirname(__DIR__, 2) . '/public/' . $path;
        $version = is_file($file) ? (int) filemtime($file) : time();

        return '/' . $path . '?v=' . $version;
    }

    protected function verifyCsrfOrAbort(): void
    {
        $token = $_POST['csrf_token'] ?? null;

        if (!Session::verifyCsrf($token)) {
            http_response_code(403);
            exit;
        }
    }

    protected function json(int $status, array $payload): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        echo json_encode($payload, JSON_THROW_ON_ERROR);
        exit;
    }

    protected function jsonError(int $status, string $code, string $message, array $fields = []): never
    {
        $error = [
            'code' => $code,
            'message' => $message,
        ];

        if ($fields !== []) {
            $error['fields'] = $fields;
        }

        $this->json($status, ['ok' => false, 'error' => $error]);
    }

    protected function jsonBody(): array
    {
        $raw = file_get_contents('php://input');

        if ($raw === false) {
            $raw = '';
        }

        try {
            $decoded = json_decode($raw, false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            $this->jsonError(400, 'invalid_json', 'Request body must be valid JSON.');
        }

        if (!is_object($decoded)) {
            $this->jsonError(400, 'invalid_json', 'Request body must be a JSON object.');
        }

        return (array) $decoded;
    }

    protected function requireLoginJson(): User
    {
        $user = $this->currentUser();

        if ($user === null) {
            $this->jsonError(401, 'unauthenticated', 'Authentication required.');
        }

        return $user;
    }

    protected function verifyCsrfJson(): void
    {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

        if (!Session::verifyCsrf(is_string($token) ? $token : null)) {
            $this->jsonError(403, 'csrf_failed', 'Invalid or missing CSRF token.');
        }
    }

    protected function render(string $template, array $variables = [], ?string $layout = 'layout'): void
    {
        if ($layout !== null && !isset($variables['currentUser'])) {
            $variables['currentUser'] = $this->currentUser();
        }

        if ($layout !== null && !isset($variables['flash'])) {
            $variables['flash'] = Session::consumeFlash();
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