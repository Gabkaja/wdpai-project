ALTER TABLE pack_settings ADD COLUMN max_purchase_packs INTEGER NOT NULL DEFAULT 500;

ALTER TABLE pack_settings ADD CONSTRAINT pack_settings_max_purchase_packs_check CHECK (max_purchase_packs BETWEEN 1 AND 100000);
