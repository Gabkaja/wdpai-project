<?php

declare(strict_types=1);

namespace App\Repository;

final class CardRepository
{
    private const CARDS = [
        ['id' => 1, 'code' => 'SR-001', 'name' => 'Cyber Blade', 'rarity' => 'common', 'dropChance' => 45.0, 'icon' => 'hardware',
         'description' => 'Basic synthetic upgrade. Every chat activity during the stream raises your chance of getting this combat component.'],
        ['id' => 2, 'code' => 'SR-002', 'name' => 'Starter Pack', 'rarity' => 'common', 'dropChance' => 38.2, 'icon' => 'inventory',
         'description' => 'A starter resource kit for new viewers joining the stream. Guaranteed drop every 15 minutes of watching the Kick stream.'],
        ['id' => 3, 'code' => 'SR-007', 'name' => 'Quantum Shield', 'rarity' => 'rare', 'dropChance' => 18.5, 'icon' => 'shield',
         'description' => 'A force field that absorbs streaming errors. Protects against card burn during consecutive pack opening attempts.'],
        ['id' => 4, 'code' => 'SR-009', 'name' => 'Shadow Protocol', 'rarity' => 'rare', 'dropChance' => 14.0, 'icon' => 'visibility_off',
         'description' => 'A masking algorithm for unboxing. Hides a rare foil drop from other chat participants until the official reveal.'],
        ['id' => 5, 'code' => 'SR-014', 'name' => 'Overload Matrix', 'rarity' => 'epic', 'dropChance' => 6.5, 'icon' => 'electric_bolt',
         'description' => 'Erupts in a violent wave of subscription energy. Doubles the drop chance multiplier for the next 5 boosters opened.'],
        ['id' => 6, 'code' => 'SR-016', 'name' => 'Time Glitch', 'rarity' => 'epic', 'dropChance' => 4.2, 'icon' => 'hourglass_empty',
         'description' => 'An anomaly in the video stream. Undoes a rejected card and lets the streamer re-roll in front of the audience.'],
        ['id' => 7, 'code' => 'SR-021', 'name' => 'System Annihilator', 'rarity' => 'legendary', 'dropChance' => 0.8, 'icon' => 'destruction',
         'description' => 'Absolute dominance over the broadcast session. Triggers confetti across the whole Kick chat and an automatic shipment of a physical PSA 10 graded slab.'],
        ['id' => 8, 'code' => 'SR-024', 'name' => 'Source Code Dragon', 'rarity' => 'legendary', 'dropChance' => 0.3, 'icon' => 'terminal',
         'description' => 'The ultimate artifact of the first StreamRip edition. Comes with a holographic certificate and a unique lifetime patronage token on the Kick channel.'],
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