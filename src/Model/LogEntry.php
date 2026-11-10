<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;

final class LogEntry
{
    private readonly int $id;
    private readonly string $level;
    private readonly string $message;
    private readonly ?string $username;
    private readonly DateTimeImmutable $createdAt;

    public function __construct(int $id, string $level, string $message, ?string $username, DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->level = $level;
        $this->message = $message;
        $this->username = $username;
        $this->createdAt = $createdAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
