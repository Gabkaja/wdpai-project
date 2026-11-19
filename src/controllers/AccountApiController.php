<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Repository\KickAccountRepository;
use App\Repository\LogRepository;
use App\Repository\UserRepository;
use App\Session;

final class AccountApiController extends AppController
{
    public function changePassword(): void
    {
        $user = $this->requireLoginJson();
        $this->verifyCsrfJson();

        $body = $this->jsonBody();
        $this->rejectUnknownFields($body, ['current_password', 'new_password', 'new_password_confirm']);

        $fields = [];

        $currentPassword = $body['current_password'] ?? null;
        $newPassword = $body['new_password'] ?? null;
        $newPasswordConfirm = $body['new_password_confirm'] ?? null;

        if (!array_key_exists('current_password', $body) || !is_string($currentPassword) || $currentPassword === '') {
            $fields['current_password'] = 'Current password is required.';
        }

        if (!array_key_exists('new_password', $body) || !is_string($newPassword) || $newPassword === '') {
            $fields['new_password'] = 'New password is required.';
        } elseif (strlen($newPassword) < 8) {
            $fields['new_password'] = 'New password must be at least 8 characters.';
        }

        if (!array_key_exists('new_password_confirm', $body) || !is_string($newPasswordConfirm) || $newPasswordConfirm === '') {
            $fields['new_password_confirm'] = 'Password confirmation is required.';
        } elseif (is_string($newPassword) && $newPassword !== $newPasswordConfirm) {
            $fields['new_password_confirm'] = 'Passwords do not match.';
        }

        if ($fields !== []) {
            $this->jsonError(422, 'validation_failed', 'Validation failed.', $fields);
        }

        $pdo = (new Database())->connect();
        $userRepository = new UserRepository($pdo);
        $logRepository = new LogRepository($pdo);

        $passwordHash = $userRepository->findPasswordHashById($user->getId());

        if ($passwordHash === null) {
            $this->jsonError(422, 'validation_failed', 'Validation failed.', [
                'current_password' => 'Current password is incorrect.',
            ]);
        }

        if (!password_verify($currentPassword, $passwordHash)) {
            $logRepository->add('warning', 'Password change failed', ['user_id' => $user->getId()], $user->getId());

            $this->jsonError(422, 'validation_failed', 'Validation failed.', [
                'current_password' => 'Current password is incorrect.',
            ]);
        }

        if (password_verify($newPassword, $passwordHash)) {
            $this->jsonError(422, 'validation_failed', 'Validation failed.', [
                'new_password' => 'New password must be different from the current password.',
            ]);
        }

        $userRepository->updatePasswordHash($user->getId(), password_hash($newPassword, PASSWORD_DEFAULT));
        Session::regenerate();
        $logRepository->add('info', 'Password changed', ['user_id' => $user->getId()], $user->getId());

        $this->json(200, [
            'ok' => true,
            'data' => [
                'changed' => true,
            ],
        ]);
    }

    public function disconnectKick(): void
    {
        $user = $this->requireLoginJson();
        $this->verifyCsrfJson();

        $pdo = (new Database())->connect();
        $kickAccountRepository = new KickAccountRepository($pdo);
        $userRepository = new UserRepository($pdo);
        $logRepository = new LogRepository($pdo);

        if ($kickAccountRepository->findByUserId($user->getId()) === null) {
            $this->jsonError(409, 'kick_not_connected', 'This account is not connected to Kick.');
        }

        $passwordHash = $userRepository->findPasswordHashById($user->getId());

        if ($passwordHash === null || $passwordHash === '') {
            $this->jsonError(409, 'password_required', 'Set a password before disconnecting Kick.');
        }

        $kickAccountRepository->unlink($user->getId());
        $logRepository->add('info', 'Kick account disconnected', ['user_id' => $user->getId()], $user->getId());

        $this->json(200, [
            'ok' => true,
            'data' => [
                'connected' => false,
            ],
        ]);
    }

    private function rejectUnknownFields(array $body, array $allowed): void
    {
        $fields = [];

        foreach (array_keys($body) as $key) {
            if (!in_array($key, $allowed, true)) {
                $fields[(string) $key] = 'This field is not allowed.';
            }
        }

        if ($fields !== []) {
            $this->jsonError(422, 'validation_failed', 'Validation failed.', $fields);
        }
    }
}
