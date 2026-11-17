<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;

final class PackOpening
{
    private readonly int $id;
    private readonly int $userId;
    private readonly DateTimeImmutable $openedAt;
    private readonly array $cards;

    public function __construct(int $id, int $userId, DateTimeImmutable $openedAt, array $cards = [])
    {
        $this->id = $id;
        $this->userId = $userId;
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

    public function getOpenedAt(): DateTimeImmutable
    {
        return $this->openedAt;
    }

    public function getCards(): array
    {
        return $this->cards;
    }
}
