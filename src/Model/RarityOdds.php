<?php

declare(strict_types=1);

namespace App\Model;

final class RarityOdds
{
    private readonly string $code;
    private readonly string $name;
    private readonly int $dropWeight;
    private readonly int $cardCount;
    private readonly int $level;
    private readonly float $percent;

    public function __construct(string $code, string $name, int $dropWeight, int $cardCount, int $level, float $percent)
    {
        $this->code = $code;
        $this->name = $name;
        $this->dropWeight = $dropWeight;
        $this->cardCount = $cardCount;
        $this->level = $level;
        $this->percent = $percent;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDropWeight(): int
    {
        return $this->dropWeight;
    }

    public function getCardCount(): int
    {
        return $this->cardCount;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getPercent(): float
    {
        return $this->percent;
    }
}