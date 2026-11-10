CREATE TABLE rarities (
    id SMALLINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(50) NOT NULL,
    sort_order SMALLINT NOT NULL UNIQUE
);

CREATE TABLE users (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'viewer',
    points INTEGER NOT NULL DEFAULT 0,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT users_role_check CHECK (role IN ('viewer', 'admin')),
    CONSTRAINT users_points_check CHECK (points >= 0)
);

CREATE UNIQUE INDEX users_email_lower_idx ON users (LOWER(email));

CREATE TABLE cards (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    rarity_id SMALLINT NOT NULL REFERENCES rarities (id) ON DELETE RESTRICT,
    code VARCHAR(20) NOT NULL UNIQUE,
    drop_chance NUMERIC(5,2) NOT NULL,
    icon VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX cards_rarity_id_idx ON cards (rarity_id);

CREATE TABLE user_cards (
    user_id BIGINT NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    card_id BIGINT NOT NULL REFERENCES cards (id) ON DELETE RESTRICT,
    quantity INTEGER NOT NULL DEFAULT 1,
    acquired_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, card_id),
    CONSTRAINT user_cards_quantity_check CHECK (quantity >= 1)
);

CREATE INDEX user_cards_card_id_idx ON user_cards (card_id);

CREATE TABLE system_logs (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    level VARCHAR(10) NOT NULL,
    message TEXT NOT NULL,
    context JSONB NULL,
    user_id BIGINT NULL REFERENCES users (id) ON DELETE SET NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT system_logs_level_check CHECK (level IN ('info', 'warning', 'error'))
);

CREATE INDEX system_logs_created_at_idx ON system_logs (created_at DESC);
CREATE INDEX system_logs_level_idx ON system_logs (level);
