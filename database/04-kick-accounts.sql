CREATE TABLE kick_accounts (
    user_id BIGINT NOT NULL,
    kick_user_id BIGINT NOT NULL,
    kick_username VARCHAR(100) NOT NULL,
    linked_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id),
    CONSTRAINT kick_accounts_user_id_fkey FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT kick_accounts_kick_user_id_key UNIQUE (kick_user_id)
);
