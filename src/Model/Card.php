<?php

declare(strict_types=1);

namespace App\Model;

final class Card
{
    private readonly int $id;
    private readonly string $name;
    private readonly string $rarityCode;
    private readonly string $rarityName;
    private readonly string $code;
    private readonly float $dropChance;
    private readonly string $icon;
    private readonly string $description;
    private readonly string $createdAt;

    public function __construct(
        int $id,
        string $name,
        string $rarityCode,
        string $rarityName,
        string $code,
        float $dropChance,
        string $icon,
        string $description,
        string $createdAt
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->rarityCode = $rarityCode;
        $this->rarityName = $rarityName;
        $this->code = $code;
        $this->dropChance = $dropChance;
        $this->icon = $icon;
        $this->description = $description;
        $this->createdAt = $createdAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRarityCode(): string
    {
        return $this->rarityCode;
    }

    public function getRarityName(): string
    {
        return $this->rarityName;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDropChance(): float
    {
        return $this->dropChance;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}
