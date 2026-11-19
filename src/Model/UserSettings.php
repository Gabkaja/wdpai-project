<?php

declare(strict_types=1);

namespace App\Model;

final class UserSettings
{
    private readonly string $revealMode;
    private readonly string $multiOpenMode;
    private readonly bool $soundEnabled;
    private readonly int $soundVolume;
    private readonly string $collectionVisibility;

    public function __construct(string $revealMode, string $multiOpenMode, bool $soundEnabled, int $soundVolume, string $collectionVisibility)
    {
        $this->revealMode = $revealMode;
        $this->multiOpenMode = $multiOpenMode;
        $this->soundEnabled = $soundEnabled;
        $this->soundVolume = $soundVolume;
        $this->collectionVisibility = $collectionVisibility;
    }

    public function getRevealMode(): string
    {
        return $this->revealMode;
    }

    public function getMultiOpenMode(): string
    {
        return $this->multiOpenMode;
    }

    public function isSoundEnabled(): bool
    {
        return $this->soundEnabled;
    }

    public function getSoundVolume(): int
    {
        return $this->soundVolume;
    }

    public function getCollectionVisibility(): string
    {
        return $this->collectionVisibility;
    }
}
