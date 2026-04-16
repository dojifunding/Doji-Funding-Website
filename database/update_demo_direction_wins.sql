-- ══════════════════════════════════════════════════════════════
--  DOJI FUNDING — Demo directional win rates
--  Requires migration_v1.6_direction_wins.sql applied first.
--
--  Populates long_winning_trades + short_winning_trades for
--  the 8 demo challenges (user_id = 2).
--  Safe to re-run (plain UPDATE, no INSERT).
-- ══════════════════════════════════════════════════════════════

-- DEMO-001 · Active 1-Step $10K · 17L / 11S · long win 71% / short win 55%
UPDATE challenges SET
    long_winning_trades  = 12,
    short_winning_trades = 6
WHERE order_id = 'DEMO-001' AND user_id = 2;

-- DEMO-002 · Active 2-Step $25K Phase 2 · 23L / 19S · long win 65% / short win 58%
UPDATE challenges SET
    long_winning_trades  = 15,
    short_winning_trades = 11
WHERE order_id = 'DEMO-002' AND user_id = 2;

-- DEMO-003 · Funded $50K · 54L / 41S · long win 69% / short win 61%
UPDATE challenges SET
    long_winning_trades  = 37,
    short_winning_trades = 25
WHERE order_id = 'DEMO-003' AND user_id = 2;

-- DEMO-004 · Passed $10K · 22L / 13S · long win 73% / short win 62%
UPDATE challenges SET
    long_winning_trades  = 16,
    short_winning_trades = 8
WHERE order_id = 'DEMO-004' AND user_id = 2;

-- DEMO-005 · Failed $10K · 2L / 6S · long win 50% / short win 33%
UPDATE challenges SET
    long_winning_trades  = 1,
    short_winning_trades = 2
WHERE order_id = 'DEMO-005' AND user_id = 2;

-- DEMO-006 · Active $5K · 3L / 2S · long win 67% / short win 50%
UPDATE challenges SET
    long_winning_trades  = 2,
    short_winning_trades = 1
WHERE order_id = 'DEMO-006' AND user_id = 2;

-- DEMO-007 · Funded $100K · 117L / 63S · long win 74% / short win 60%
UPDATE challenges SET
    long_winning_trades  = 87,
    short_winning_trades = 38
WHERE order_id = 'DEMO-007' AND user_id = 2;

-- DEMO-008 · Failed $25K · 3L / 8S · long win 33% / short win 38%
UPDATE challenges SET
    long_winning_trades  = 1,
    short_winning_trades = 3
WHERE order_id = 'DEMO-008' AND user_id = 2;


-- ── Vérification ──────────────────────────────────────────────
SELECT order_id, status, long_trades, long_winning_trades,
       short_trades, short_winning_trades
FROM challenges
WHERE order_id LIKE 'DEMO-%' AND user_id = 2
ORDER BY id;
