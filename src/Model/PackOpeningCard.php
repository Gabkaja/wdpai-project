<?php

declare(strict_types=1);

namespace App\Model;

final class PackOpeningCard
{
    private readonly int $slot;
    private readonly Card $card;
    private readonly bool $isNew;

    public function __construct(int $slot, Card $card, bool $isNew)
    {
        $this->slot = $slot;
        $this->card = $card;
        $this->isNew = $isNew;
    }

    public function getSlot(): int
    {
        return $this->slot;
    }

    public function getCard(): Card
    {
        return $this->card;
    }

    public function isNew(): bool
    {
        return $this->isNew;
    }
}
