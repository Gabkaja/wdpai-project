<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;

final class LeaderboardEntry
{
    private readonly int $userId;
    private readonly string $username;
    private readonly int $totalCards;
    private readonly int $uniqueCards;
    private readonly int $rankPosition;
    private readonly DateTimeImmutable $joinedAt;
    private readonly ?string $rarestCardName;
    private readonly int $packsOpened;

    public function __construct(int $userId, string $username, int $totalCards, int $uniqueCards, int $rankPosition, DateTimeImmutable $joinedAt, ?string $rarestCardName, int $packsOpened)
    {
        $this->userId = $userId;
        $this->username = $username;
        $this->totalCards = $totalCards;
        $this->uniqueCards = $uniqueCards;
        $this->rankPosition = $rankPosition;
        $this->joinedAt = $joinedAt;
        $this->rarestCardName = $rarestCardName;
        $this->packsOpened = $packsOpened;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getTotalCards(): int
    {
        return $this->totalCards;
    }

    public function getUniqueCards(): int
    {
        return $this->uniqueCards;
    }

    public function getRankPosition(): int
    {
        return $this->rankPosition;
    }

    public function getJoinedAt(): DateTimeImmutable
    {
        return $this->joinedAt;
    }

    public function getRarestCardName(): ?string
    {
        return $this->rarestCardName;
    }

    public function getPacksOpened(): int
    {
        return $this->packsOpened;
    }
}
