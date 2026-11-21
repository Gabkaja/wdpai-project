<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Repository\LeaderboardRepository;
use App\Repository\RarityRepository;
use App\Repository\UserRepository;
use App\Repository\UserSettingsRepository;
use App\Service\CollectionAccess;

final class UserProfileController extends AppController
{
    public function show(int $id): void
    {
        $pdo = (new Database())->connect();
        $owner = (new UserRepository($pdo))->findById($id);

        if ($owner === null) {
            http_response_code(404);
            $this->noCache();
            (new DefaultController())->notFound();
            return;
        }

        $this->noCache();

        $entry = (new LeaderboardRepository($pdo))->findByUserId($id);
        $settings = (new UserSettingsRepository($pdo))->find($id);
        $viewer = $this->currentUser();

        $isOwner = $viewer !== null && $viewer->getId() === $id;
        $isAdmin = $viewer !== null && $viewer->getRole() === 'admin';
        $isPrivate = $settings->getCollectionVisibility() !== 'public';
        $canViewCollection = CollectionAccess::canView($settings->getCollectionVisibility(), $id, $viewer);

        $collectionNotice = null;

        if ($isPrivate && $isOwner) {
            $collectionNotice = 'Only you can see this';
        } elseif ($isPrivate && $isAdmin) {
            $collectionNotice = 'Hidden from other users (visible to admins)';
        }

        $this->render('user-profile', [
            'pageTitle' => $owner->getUsername() . ' – StreamRip',
            'username' => $owner->getUsername(),
            'role' => $owner->getRole(),
            'joinedAt' => $entry?->getJoinedAt() ?? $owner->getCreatedAt(),
            'rank' => $entry?->getRankPosition() ?? 0,
            'totalCards' => $entry?->getTotalCards() ?? 0,
            'uniqueCards' => $entry?->getUniqueCards() ?? 0,
            'packsOpened' => $entry?->getPacksOpened() ?? 0,
            'rarestCard' => $entry?->getRarestCardName(),
            'avatarPath' => $owner->getAvatarPath(),
            'avatarUrl' => $this->avatarUrl($owner->getAvatarPath()),
            'userId' => $owner->getId(),
            'isOwner' => $isOwner,
            'canViewCollection' => $canViewCollection,
            'collectionNotice' => $collectionNotice,
            'rarities' => (new RarityRepository($pdo))->odds(),
        ]);
    }
}
