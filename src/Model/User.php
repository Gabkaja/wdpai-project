<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;

final class User
{
    private readonly int $id;
    private readonly string $username;
    private readonly string $email;
    private readonly string $role;
    private readonly int $points;
    private readonly int $unopenedPacks;
    private readonly DateTimeImmutable $createdAt;

    public function __construct(int $id, string $username, string $email, string $role, int $points, int $unopenedPacks, DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->role = $role;
        $this->points = $points;
        $this->unopenedPacks = $unopenedPacks;
        $this->createdAt = $createdAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function getUnopenedPacks(): int
    {
        return $this->unopenedPacks;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
