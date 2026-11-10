<?php

declare(strict_types=1);

namespace App\Model;

final class LeaderboardEntry
{
    private readonly int $userId;
    private readonly string $username;
    private readonly int $totalCards;
    private readonly int $uniqueCards;
    private readonly int $rankPosition;

    public function __construct(int $userId, string $username, int $totalCards, int $uniqueCards, int $rankPosition)
    {
        $this->userId = $userId;
        $this->username = $username;
        $this->totalCards = $totalCards;
        $this->uniqueCards = $uniqueCards;
        $this->rankPosition = $rankPosition;
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
}
