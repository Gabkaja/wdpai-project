ALTER TABLE rarities ADD COLUMN drop_weight INTEGER NOT NULL DEFAULT 1;

ALTER TABLE rarities ADD CONSTRAINT rarities_drop_weight_check CHECK (drop_weight >= 0);

UPDATE rarities SET drop_weight = 60 WHERE code = 'common';
UPDATE rarities SET drop_weight = 25 WHERE code = 'rare';
UPDATE rarities SET drop_weight = 10 WHERE code = 'epic';
UPDATE rarities SET drop_weight = 5 WHERE code = 'legendary';

CREATE TABLE pack_settings (
    id SMALLINT PRIMARY KEY DEFAULT 1,
    cards_per_pack SMALLINT NOT NULL DEFAULT 4,
    cost_points INTEGER NOT NULL DEFAULT 100,
    CONSTRAINT pack_settings_single_row_check CHECK (id = 1),
    CONSTRAINT pack_settings_cards_per_pack_check CHECK (cards_per_pack BETWEEN 1 AND 20),
    CONSTRAINT pack_settings_cost_points_check CHECK (cost_points >= 0)
);

INSERT INTO pack_settings DEFAULT VALUES;

CREATE TABLE pack_openings (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    points_spent INTEGER NOT NULL,
    opened_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX pack_openings_user_id_idx ON pack_openings (user_id, opened_at DESC);

CREATE TABLE pack_opening_cards (
    opening_id BIGINT NOT NULL REFERENCES pack_openings (id) ON DELETE CASCADE,
    slot SMALLINT NOT NULL,
    card_id BIGINT NOT NULL REFERENCES cards (id) ON DELETE RESTRICT,
    PRIMARY KEY (opening_id, slot)
);

CREATE INDEX pack_opening_cards_card_id_idx ON pack_opening_cards (card_id);

UPDATE users SET points = 500 WHERE username IN ('StreamerBoss', 'ClipMaster');
