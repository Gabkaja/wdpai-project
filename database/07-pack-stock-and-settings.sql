ALTER TABLE users ADD COLUMN unopened_packs INTEGER NOT NULL DEFAULT 0;

ALTER TABLE users ADD CONSTRAINT users_unopened_packs_check CHECK (unopened_packs >= 0);

ALTER TABLE pack_settings ADD COLUMN max_bulk_packs SMALLINT NOT NULL DEFAULT 10;

ALTER TABLE pack_settings ADD CONSTRAINT pack_settings_max_bulk_packs_check CHECK (max_bulk_packs BETWEEN 1 AND 50);

CREATE TABLE pack_purchases (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    quantity INTEGER NOT NULL,
    points_spent INTEGER NOT NULL,
    purchased_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pack_purchases_quantity_check CHECK (quantity > 0),
    CONSTRAINT pack_purchases_points_spent_check CHECK (points_spent >= 0)
);

CREATE INDEX pack_purchases_user_id_idx ON pack_purchases (user_id, purchased_at DESC);

INSERT INTO pack_purchases (user_id, quantity, points_spent, purchased_at)
SELECT user_id, 1, points_spent, opened_at FROM pack_openings;

ALTER TABLE pack_openings DROP COLUMN points_spent;

ALTER TABLE pack_opening_cards ADD COLUMN is_new BOOLEAN NOT NULL DEFAULT FALSE;

CREATE TABLE user_settings (
    user_id BIGINT PRIMARY KEY REFERENCES users (id) ON DELETE CASCADE,
    reveal_mode VARCHAR(10) NOT NULL DEFAULT 'auto',
    multi_open_mode VARCHAR(12) NOT NULL DEFAULT 'queue',
    sound_enabled BOOLEAN NOT NULL DEFAULT TRUE,
    sound_volume SMALLINT NOT NULL DEFAULT 70,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT user_settings_reveal_mode_check CHECK (reveal_mode IN ('auto', 'manual')),
    CONSTRAINT user_settings_multi_open_mode_check CHECK (multi_open_mode IN ('queue', 'all_at_once')),
    CONSTRAINT user_settings_sound_volume_check CHECK (sound_volume BETWEEN 0 AND 100)
);
