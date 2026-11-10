<?php

declare(strict_types=1);

namespace App\Model;

final class Rarity
{
    private readonly int $id;
    private readonly string $code;
    private readonly string $name;
    private readonly int $sortOrder;

    public function __construct(int $id, string $code, string $name, int $sortOrder)
    {
        $this->id = $id;
        $this->code = $code;
        $this->name = $name;
        $this->sortOrder = $sortOrder;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }
}
