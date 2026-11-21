<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Repository\KickAccountRepository;
use App\Repository\LogRepository;
use App\Repository\UserRepository;
use App\Repository\UserSettingsRepository;
use App\Service\AvatarProcessor;
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

    public function getPrivacy(): void
    {
        $user = $this->requireLoginJson();

        $pdo = (new Database())->connect();
        $settings = (new UserSettingsRepository($pdo))->find($user->getId());

        $this->json(200, [
            'ok' => true,
            'data' => [
                'collection_visibility' => $settings->getCollectionVisibility(),
            ],
        ]);
    }

    public function savePrivacy(): void
    {
        $user = $this->requireLoginJson();
        $this->verifyCsrfJson();

        $body = $this->jsonBody();
        $this->rejectUnknownFields($body, ['collection_visibility']);

        $fields = [];

        if (!array_key_exists('collection_visibility', $body)) {
            $fields['collection_visibility'] = 'This field is required.';
        } elseif (!is_string($body['collection_visibility']) || !in_array($body['collection_visibility'], ['public', 'private'], true)) {
            $fields['collection_visibility'] = 'This field must be "public" or "private".';
        }

        if ($fields !== []) {
            $this->jsonError(422, 'validation_failed', 'Validation failed.', $fields);
        }

        $collectionVisibility = $body['collection_visibility'];

        $pdo = (new Database())->connect();
        $repository = new UserSettingsRepository($pdo);
        $current = $repository->find($user->getId());

        if ($current->getCollectionVisibility() !== $collectionVisibility) {
            $repository->saveCollectionVisibility($user->getId(), $collectionVisibility);
            (new LogRepository($pdo))->add('info', 'Collection visibility changed', [
                'user_id' => $user->getId(),
                'collection_visibility' => $collectionVisibility,
            ], $user->getId());
        }

        $this->json(200, [
            'ok' => true,
            'data' => [
                'collection_visibility' => $collectionVisibility,
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

    public function uploadAvatar(): void
    {
        $user = $this->requireLoginJson();
        $this->verifyCsrfJson();

        $file = $_FILES['avatar'] ?? null;

        if (!is_array($file)) {
            $this->jsonError(422, 'avatar_missing', 'Avatar file is required.', [
                'avatar' => 'Avatar file is required.',
            ]);
        }

        $tmpPath = $file['tmp_name'] ?? '';
        $uploadError = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        $size = $file['size'] ?? 0;

        if (!is_string($tmpPath) || !is_int($uploadError) || !is_int($size)) {
            $this->jsonError(422, 'avatar_invalid_image', 'The uploaded file is not a valid image.', [
                'avatar' => 'The uploaded file is not a valid image.',
            ]);
        }

        if ($uploadError === UPLOAD_ERR_OK && !is_uploaded_file($tmpPath)) {
            $this->jsonError(422, 'avatar_invalid_image', 'The uploaded file is not a valid image.', [
                'avatar' => 'The uploaded file is not a valid image.',
            ]);
        }

        try {
            $contents = (new AvatarProcessor())->process($tmpPath, $uploadError, $size);
        } catch (\DomainException $exception) {
            $code = $exception->getMessage();
            $message = $this->avatarErrorMessage($code);

            $this->jsonError(422, $code, $message, [
                'avatar' => $message,
            ]);
        }

        $directory = dirname(__DIR__, 2) . '/public/uploads/avatars';

        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new \RuntimeException('Avatar directory could not be created.');
        }

        $target = $directory . '/' . $user->getId() . '.webp';
        $temporary = tempnam($directory, 'avatar-');

        if ($temporary === false) {
            throw new \RuntimeException('Avatar file could not be created.');
        }

        try {
            $written = file_put_contents($temporary, $contents, LOCK_EX);

            if ($written !== strlen($contents)) {
                throw new \RuntimeException('Avatar file could not be written.');
            }

            chmod($temporary, 0644);

            if (!rename($temporary, $target)) {
                throw new \RuntimeException('Avatar file could not be stored.');
            }
        } finally {
            if (is_file($temporary)) {
                @unlink($temporary);
            }
        }

        $pdo = (new Database())->connect();
        (new UserRepository($pdo))->updateAvatarPath($user->getId(), 'uploads/avatars/' . $user->getId() . '.webp');
        (new LogRepository($pdo))->add('info', 'Avatar updated', ['user_id' => $user->getId()], $user->getId());

        clearstatcache(true, $target);
        $version = filemtime($target) ?: time();

        $this->json(200, [
            'ok' => true,
            'data' => [
                'avatar_url' => '/uploads/avatars/' . $user->getId() . '.webp?v=' . $version,
            ],
        ]);
    }

    public function deleteAvatar(): void
    {
        $user = $this->requireLoginJson();
        $this->verifyCsrfJson();

        if ($user->getAvatarPath() === null) {
            $this->jsonError(409, 'avatar_not_found', 'This account has no avatar.');
        }

        $target = dirname(__DIR__, 2) . '/public/uploads/avatars/' . $user->getId() . '.webp';

        if (is_file($target)) {
            @unlink($target);
        }

        $pdo = (new Database())->connect();
        (new UserRepository($pdo))->updateAvatarPath($user->getId(), null);
        (new LogRepository($pdo))->add('info', 'Avatar removed', ['user_id' => $user->getId()], $user->getId());

        $this->json(200, [
            'ok' => true,
            'data' => [
                'avatar_url' => null,
            ],
        ]);
    }

    private function avatarErrorMessage(string $code): string
    {
        return match ($code) {
            'avatar_missing' => 'Avatar file is required.',
            'avatar_too_large' => 'Avatar must be 2 MB or smaller.',
            'avatar_invalid_type' => 'Avatar must be a JPG, PNG or WebP image.',
            'avatar_invalid_image' => 'The uploaded file is not a valid image.',
            'avatar_dimensions_too_large' => 'Avatar dimensions must not exceed 4000x4000 pixels.',
            'avatar_upload_failed' => 'Avatar upload failed.',
            default => 'Avatar could not be processed.',
        };
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
