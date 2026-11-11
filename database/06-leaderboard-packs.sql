CREATE OR REPLACE VIEW leaderboard_view AS
SELECT
    u.id AS user_id,
    u.username,
    COALESCE(SUM(uc.quantity), 0) AS total_cards,
    COUNT(uc.card_id) AS unique_cards,
    u.created_at AS joined_at,
    rc.rarest_card_name,
    RANK() OVER (ORDER BY COALESCE(SUM(uc.quantity), 0) DESC, COUNT(uc.card_id) DESC) AS rank_position,
    (SELECT COUNT(*) FROM pack_openings po WHERE po.user_id = u.id) AS packs_opened
FROM users u
LEFT JOIN user_cards uc ON uc.user_id = u.id
LEFT JOIN LATERAL (
    SELECT c.name AS rarest_card_name
    FROM user_cards uc2
    JOIN cards c ON c.id = uc2.card_id
    JOIN rarities r ON r.id = c.rarity_id
    WHERE uc2.user_id = u.id
    ORDER BY r.sort_order DESC, c.id ASC
    LIMIT 1
) rc ON true
GROUP BY u.id, u.username, u.created_at, rc.rarest_card_name;
