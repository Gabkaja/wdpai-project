<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\UserSettings;
use PDO;

final class UserSettingsRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function find(int $userId): UserSettings
    {
        $statement = $this->pdo->prepare('SELECT reveal_mode, multi_open_mode, sound_enabled, sound_volume, collection_visibility FROM user_settings WHERE user_id = :user_id');
        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->execute();

        $row = $statement->fetch();

        if ($row === false) {
            return new UserSettings('auto', 'queue', true, 70, 'public');
        }

        return new UserSettings(
            $row['reveal_mode'],
            $row['multi_open_mode'],
            (bool) $row['sound_enabled'],
            (int) $row['sound_volume'],
            $row['collection_visibility']
        );
    }

    public function save(int $userId, UserSettings $settings): void
    {
        $statement = $this->pdo->prepare('
            INSERT INTO user_settings (user_id, reveal_mode, multi_open_mode, sound_enabled, sound_volume, collection_visibility, updated_at)
            VALUES (:user_id, :reveal_mode, :multi_open_mode, :sound_enabled, :sound_volume, :collection_visibility, CURRENT_TIMESTAMP)
            ON CONFLICT (user_id) DO UPDATE SET
                reveal_mode = EXCLUDED.reveal_mode,
                multi_open_mode = EXCLUDED.multi_open_mode,
                sound_enabled = EXCLUDED.sound_enabled,
                sound_volume = EXCLUDED.sound_volume,
                collection_visibility = EXCLUDED.collection_visibility,
                updated_at = CURRENT_TIMESTAMP
        ');

        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->bindValue(':reveal_mode', $settings->getRevealMode(), PDO::PARAM_STR);
        $statement->bindValue(':multi_open_mode', $settings->getMultiOpenMode(), PDO::PARAM_STR);
        $statement->bindValue(':sound_enabled', $settings->isSoundEnabled(), PDO::PARAM_BOOL);
        $statement->bindValue(':sound_volume', $settings->getSoundVolume(), PDO::PARAM_INT);
        $statement->bindValue(':collection_visibility', $settings->getCollectionVisibility(), PDO::PARAM_STR);
        $statement->execute();
    }
}
