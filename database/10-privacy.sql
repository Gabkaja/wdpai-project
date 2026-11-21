ALTER TABLE user_settings ADD COLUMN IF NOT EXISTS collection_visibility VARCHAR(10) NOT NULL DEFAULT 'public' CHECK (collection_visibility IN ('public','private'));
