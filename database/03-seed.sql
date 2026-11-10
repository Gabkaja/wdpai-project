INSERT INTO rarities (code, name, sort_order)
VALUES
    ('common', 'Common', 1),
    ('rare', 'Rare', 2),
    ('epic', 'Epic', 3),
    ('legendary', 'Legendary', 4);

INSERT INTO cards (name, rarity_id, code, drop_chance, icon, description)
SELECT
    c.name,
    (SELECT id FROM rarities WHERE rarities.code = c.rarity_code),
    c.code,
    c.drop_chance,
    c.icon,
    c.description
FROM (VALUES
    ('Cyber Blade', 'common', 'SR-001', 45.0, 'hardware', 'Basic synthetic upgrade. Every chat activity during the stream raises your chance of getting this combat component.'),
    ('Starter Pack', 'common', 'SR-002', 38.2, 'inventory', 'A starter resource kit for new viewers joining the stream. Guaranteed drop every 15 minutes of watching the Kick stream.'),
    ('Quantum Shield', 'rare', 'SR-007', 18.5, 'shield', 'A force field that absorbs streaming errors. Protects against card burn during consecutive pack opening attempts.'),
    ('Shadow Protocol', 'rare', 'SR-009', 14.0, 'visibility_off', 'A masking algorithm for unboxing. Hides a rare foil drop from other chat participants until the official reveal.'),
    ('Overload Matrix', 'epic', 'SR-014', 6.5, 'electric_bolt', 'Erupts in a violent wave of subscription energy. Doubles the drop chance multiplier for the next 5 boosters opened.'),
    ('Time Glitch', 'epic', 'SR-016', 4.2, 'hourglass_empty', 'An anomaly in the video stream. Undoes a rejected card and lets the streamer re-roll in front of the audience.'),
    ('System Annihilator', 'legendary', 'SR-021', 0.8, 'destruction', 'Absolute dominance over the broadcast session. Triggers confetti across the whole Kick chat and an automatic shipment of a physical PSA 10 graded slab.'),
    ('Source Code Dragon', 'legendary', 'SR-024', 0.3, 'terminal', 'The ultimate artifact of the first StreamRip edition. Comes with a holographic certificate and a unique lifetime patronage token on the Kick channel.')
) AS c(name, rarity_code, code, drop_chance, icon, description);

INSERT INTO users (username, email, password_hash, role, points, created_at)
SELECT
    u.username,
    u.username || '@example.com',
    '$2y$10$.HsU7et/b0B6BfboVVMxVecu97hQ6NWzhbBSQtygUlu74UqNpByce',
    u.role,
    u.points,
    u.created_at::timestamptz
FROM (VALUES
    ('ClipMaster', 'viewer', 100, '2026-01-15'),
    ('HypeConductor', 'viewer', 90, '2026-02-20'),
    ('LurkerLarry99', 'viewer', 85, '2026-03-25'),
    ('EmoteGoblin', 'viewer', 75, '2026-04-30'),
    ('RageQuitRhino', 'viewer', 65, '2026-05-15'),
    ('StreamerBoss', 'admin', 30, '2026-06-20'),
    ('ViewerOne', 'viewer', 50, '2026-08-10'),
    ('ModMaria', 'viewer', 40, '2026-09-25')
) AS u(username, role, points, created_at);

INSERT INTO user_cards (user_id, card_id, quantity) VALUES
    ((SELECT id FROM users WHERE username = 'ClipMaster'), (SELECT id FROM cards WHERE code = 'SR-001'), 2),
    ((SELECT id FROM users WHERE username = 'ClipMaster'), (SELECT id FROM cards WHERE code = 'SR-002'), 2),
    ((SELECT id FROM users WHERE username = 'ClipMaster'), (SELECT id FROM cards WHERE code = 'SR-007'), 1),
    ((SELECT id FROM users WHERE username = 'ClipMaster'), (SELECT id FROM cards WHERE code = 'SR-009'), 1),
    ((SELECT id FROM users WHERE username = 'ClipMaster'), (SELECT id FROM cards WHERE code = 'SR-014'), 1),
    ((SELECT id FROM users WHERE username = 'ClipMaster'), (SELECT id FROM cards WHERE code = 'SR-016'), 1),
    ((SELECT id FROM users WHERE username = 'ClipMaster'), (SELECT id FROM cards WHERE code = 'SR-021'), 1),
    ((SELECT id FROM users WHERE username = 'ClipMaster'), (SELECT id FROM cards WHERE code = 'SR-024'), 1),
    ((SELECT id FROM users WHERE username = 'HypeConductor'), (SELECT id FROM cards WHERE code = 'SR-001'), 2),
    ((SELECT id FROM users WHERE username = 'HypeConductor'), (SELECT id FROM cards WHERE code = 'SR-002'), 1),
    ((SELECT id FROM users WHERE username = 'HypeConductor'), (SELECT id FROM cards WHERE code = 'SR-007'), 1),
    ((SELECT id FROM users WHERE username = 'HypeConductor'), (SELECT id FROM cards WHERE code = 'SR-009'), 1),
    ((SELECT id FROM users WHERE username = 'HypeConductor'), (SELECT id FROM cards WHERE code = 'SR-014'), 1),
    ((SELECT id FROM users WHERE username = 'HypeConductor'), (SELECT id FROM cards WHERE code = 'SR-016'), 1),
    ((SELECT id FROM users WHERE username = 'HypeConductor'), (SELECT id FROM cards WHERE code = 'SR-021'), 1),
    ((SELECT id FROM users WHERE username = 'LurkerLarry99'), (SELECT id FROM cards WHERE code = 'SR-001'), 1),
    ((SELECT id FROM users WHERE username = 'LurkerLarry99'), (SELECT id FROM cards WHERE code = 'SR-002'), 1),
    ((SELECT id FROM users WHERE username = 'LurkerLarry99'), (SELECT id FROM cards WHERE code = 'SR-007'), 1),
    ((SELECT id FROM users WHERE username = 'LurkerLarry99'), (SELECT id FROM cards WHERE code = 'SR-009'), 1),
    ((SELECT id FROM users WHERE username = 'LurkerLarry99'), (SELECT id FROM cards WHERE code = 'SR-014'), 1),
    ((SELECT id FROM users WHERE username = 'LurkerLarry99'), (SELECT id FROM cards WHERE code = 'SR-016'), 1),
    ((SELECT id FROM users WHERE username = 'LurkerLarry99'), (SELECT id FROM cards WHERE code = 'SR-024'), 1),
    ((SELECT id FROM users WHERE username = 'EmoteGoblin'), (SELECT id FROM cards WHERE code = 'SR-001'), 1),
    ((SELECT id FROM users WHERE username = 'EmoteGoblin'), (SELECT id FROM cards WHERE code = 'SR-002'), 1),
    ((SELECT id FROM users WHERE username = 'EmoteGoblin'), (SELECT id FROM cards WHERE code = 'SR-007'), 1),
    ((SELECT id FROM users WHERE username = 'EmoteGoblin'), (SELECT id FROM cards WHERE code = 'SR-009'), 1),
    ((SELECT id FROM users WHERE username = 'EmoteGoblin'), (SELECT id FROM cards WHERE code = 'SR-014'), 1),
    ((SELECT id FROM users WHERE username = 'EmoteGoblin'), (SELECT id FROM cards WHERE code = 'SR-016'), 1),
    ((SELECT id FROM users WHERE username = 'RageQuitRhino'), (SELECT id FROM cards WHERE code = 'SR-001'), 1),
    ((SELECT id FROM users WHERE username = 'RageQuitRhino'), (SELECT id FROM cards WHERE code = 'SR-002'), 1),
    ((SELECT id FROM users WHERE username = 'RageQuitRhino'), (SELECT id FROM cards WHERE code = 'SR-007'), 1),
    ((SELECT id FROM users WHERE username = 'RageQuitRhino'), (SELECT id FROM cards WHERE code = 'SR-009'), 1),
    ((SELECT id FROM users WHERE username = 'RageQuitRhino'), (SELECT id FROM cards WHERE code = 'SR-014'), 1),
    ((SELECT id FROM users WHERE username = 'ViewerOne'), (SELECT id FROM cards WHERE code = 'SR-001'), 1),
    ((SELECT id FROM users WHERE username = 'ViewerOne'), (SELECT id FROM cards WHERE code = 'SR-007'), 1),
    ((SELECT id FROM users WHERE username = 'ViewerOne'), (SELECT id FROM cards WHERE code = 'SR-014'), 1),
    ((SELECT id FROM users WHERE username = 'ViewerOne'), (SELECT id FROM cards WHERE code = 'SR-021'), 1),
    ((SELECT id FROM users WHERE username = 'ModMaria'), (SELECT id FROM cards WHERE code = 'SR-001'), 1),
    ((SELECT id FROM users WHERE username = 'ModMaria'), (SELECT id FROM cards WHERE code = 'SR-002'), 1),
    ((SELECT id FROM users WHERE username = 'ModMaria'), (SELECT id FROM cards WHERE code = 'SR-007'), 1),
    ((SELECT id FROM users WHERE username = 'StreamerBoss'), (SELECT id FROM cards WHERE code = 'SR-002'), 1),
    ((SELECT id FROM users WHERE username = 'StreamerBoss'), (SELECT id FROM cards WHERE code = 'SR-009'), 1),
    ((SELECT id FROM users WHERE username = 'StreamerBoss'), (SELECT id FROM cards WHERE code = 'SR-016'), 1),
    ((SELECT id FROM users WHERE username = 'StreamerBoss'), (SELECT id FROM cards WHERE code = 'SR-021'), 1),
    ((SELECT id FROM users WHERE username = 'StreamerBoss'), (SELECT id FROM cards WHERE code = 'SR-024'), 1);

INSERT INTO system_logs (level, message, context, user_id) VALUES
    ('info', 'Pack opened for ViewerOne (3 cards).', '{"card_count": 3}'::jsonb, (SELECT id FROM users WHERE username = 'ViewerOne')),
    ('warning', 'Kick chat bot reconnected after timeout.', NULL, NULL),
    ('error', 'Failed to deliver overlay event to OBS.', '{"component": "obs", "retry": 2}'::jsonb, NULL),
    ('info', 'New user registered: ClipMaster.', '{"username": "ClipMaster"}'::jsonb, (SELECT id FROM users WHERE username = 'ClipMaster')),
    ('warning', 'Rate limit approached for IP 192.168.1.100.', '{"ip": "192.168.1.100", "requests": 95}'::jsonb, NULL),
    ('error', 'Database connection timeout.', '{"duration_ms": 5000}'::jsonb, NULL),
    ('info', 'Card SR-021 claimed by StreamerBoss.', '{"card_code": "SR-021"}'::jsonb, (SELECT id FROM users WHERE username = 'StreamerBoss'));
