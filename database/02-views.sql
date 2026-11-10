CREATE VIEW leaderboard_view AS
SELECT
    u.id AS user_id,
    u.username,
    COALESCE(SUM(uc.quantity), 0) AS total_cards,
    COUNT(uc.card_id) AS unique_cards,
    RANK() OVER (ORDER BY COALESCE(SUM(uc.quantity), 0) DESC, COUNT(uc.card_id) DESC) AS rank_position
FROM users u
LEFT JOIN user_cards uc ON uc.user_id = u.id
GROUP BY u.id, u.username;
