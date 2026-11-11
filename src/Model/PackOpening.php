<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;

final class PackOpening
{
    private readonly int $id;
    private readonly int $userId;
    private readonly int $pointsSpent;
    private readonly DateTimeImmutable $openedAt;
    private array $cards;

    public function __construct(int $id, int $userId, int $pointsSpent, DateTimeImmutable $openedAt, array $cards = [])
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->pointsSpent = $pointsSpent;
        $this->openedAt = $openedAt;
        $this->cards = $cards;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getPointsSpent(): int
    {
        return $this->pointsSpent;
    }

    public function getOpenedAt(): DateTimeImmutable
    {
        return $this->openedAt;
    }

    public function getCards(): array
    {
        return $this->cards;
    }
}
