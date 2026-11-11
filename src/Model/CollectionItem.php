<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;

final class CollectionItem
{
    private readonly Card $card;
    private readonly int $quantity;
    private readonly DateTimeImmutable $acquiredAt;

    public function __construct(Card $card, int $quantity, DateTimeImmutable $acquiredAt)
    {
        $this->card = $card;
        $this->quantity = $quantity;
        $this->acquiredAt = $acquiredAt;
    }

    public function getCard(): Card
    {
        return $this->card;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getAcquiredAt(): DateTimeImmutable
    {
        return $this->acquiredAt;
    }
}
