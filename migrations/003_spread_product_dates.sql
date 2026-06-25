-- Spread created_at dates so only a few products qualify for the NEW badge
UPDATE Products SET created_at = '2026-02-15 10:00:00' WHERE id BETWEEN 1 AND 21;
UPDATE Products SET created_at = '2026-02-15 10:00:00' WHERE id BETWEEN 39 AND 83;
UPDATE Products SET created_at = '2026-03-01 10:00:00' WHERE id BETWEEN 84 AND 100;
UPDATE Products SET created_at = '2026-04-10 10:00:00' WHERE id BETWEEN 101 AND 125;
UPDATE Products SET created_at = '2026-05-20 10:00:00' WHERE id BETWEEN 126 AND 130;

-- Recent products (within 7 days) — these get the NEW badge
UPDATE Products SET created_at = '2026-06-26 08:00:00' WHERE id IN (1, 14, 73, 113);

-- Borderline — 6 days ago, still qualifies as NEW
UPDATE Products SET created_at = '2026-06-20 10:00:00' WHERE id = 131;
