<?php

declare(strict_types=1);

namespace App;

final class Session
{
    public static function start(): void
    {
        if (session_status() !== PHP_SESSION_NONE) {
            return;
        }

        $secure = false;

        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            $secure = true;
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
            $secure = true;
        }

        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');

        session_start();
    }

    public static function userId(): ?int
    {
        $value = $_SESSION['user_id'] ?? null;

        if ($value === null) {
            return null;
        }

        return (int) $value;
    }

    public static function login(int $userId): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
    }

    public static function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies') === '1' || ini_get('session.use_cookies') === 'On') {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                true
            );
        }

        session_destroy();
    }

    public static function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrf(?string $token): bool
    {
        $stored = $_SESSION['csrf_token'] ?? null;

        if ($stored === null || $token === null) {
            return false;
        }

        return hash_equals($stored, $token);
    }

    public static function storeKickOAuth(string $state, string $verifier, string $purpose, ?int $userId): void
    {
        $_SESSION['kick_oauth'] = [
            'state' => $state,
            'verifier' => $verifier,
            'purpose' => $purpose,
            'user_id' => $userId,
        ];
    }

    public static function consumeKickOAuth(): ?array
    {
        $data = $_SESSION['kick_oauth'] ?? null;

        if ($data === null) {
            return null;
        }

        unset($_SESSION['kick_oauth']);

        return $data;
    }

    public static function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    public static function consumeFlash(): ?array
    {
        $data = $_SESSION['flash'] ?? null;

        if ($data === null) {
            return null;
        }

        unset($_SESSION['flash']);

        return $data;
    }
}
