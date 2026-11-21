<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\User;

final class CollectionAccess
{
    public static function canView(string $collectionVisibility, int $ownerId, ?User $viewer): bool
    {
        if ($collectionVisibility === 'public') {
            return true;
        }

        if ($viewer === null) {
            return false;
        }

        return $viewer->getId() === $ownerId || $viewer->getRole() === 'admin';
    }
}
