<?php

declare(strict_types=1);

namespace App\Repository;

final class CardRepository
{
    private const CARDS = [
        ['id' => 1, 'name' => 'Lurker Larry', 'rarity' => 'common', 'dropChance' => 30,
         'description' => 'Watches every stream, never types a single word.'],
        ['id' => 2, 'name' => 'Emote Spammer', 'rarity' => 'common', 'dropChance' => 30,
         'description' => 'Fills the chat with emotes faster than anyone can read.'],
        ['id' => 3, 'name' => 'Hype Train Conductor', 'rarity' => 'rare', 'dropChance' => 12,
         'description' => 'Keeps the hype train rolling, one gifted sub at a time.'],
        ['id' => 4, 'name' => 'Clip It Chief', 'rarity' => 'rare', 'dropChance' => 12,
         'description' => 'Clips the funniest moment before it even happens.'],
        ['id' => 5, 'name' => 'Rage Quit Rhino', 'rarity' => 'epic', 'dropChance' => 6,
         'description' => 'Charges straight into the boss. Closes the game after.'],
        ['id' => 6, 'name' => 'Subathon Survivor', 'rarity' => 'epic', 'dropChance' => 6,
         'description' => 'Still awake after 72 hours. Chat refuses to let them sleep.'],
        ['id' => 7, 'name' => 'Mighty Pog', 'rarity' => 'legendary', 'dropChance' => 3,
         'description' => 'The face of pure hype. Appears only at the biggest clutch plays.'],
        ['id' => 8, 'name' => 'The Final Boss Streamer', 'rarity' => 'legendary', 'dropChance' => 1,
         'description' => 'Undefeated since the first stream. Nobody has seen the loot table.'],
    ];

    public function all(): array
    {
        return self::CARDS;
    }

    public function find(int $id): ?array
    {
        foreach (self::CARDS as $card) {
            if ($card['id'] === $id) {
                return $card;
            }
        }

        return null;
    }
}