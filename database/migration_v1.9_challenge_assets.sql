-- ══════════════════════════════════════════════════════════════
--  DOJI FUNDING — Migration v1.9 — challenge_assets
--  Per-symbol trade aggregates for the Statistics "Traded Assets" card.
--  Run: paste in phpMyAdmin SQL tab.
-- ══════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS `challenge_assets` (
    `id`           INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `challenge_id` INT UNSIGNED     NOT NULL,
    `user_id`      INT UNSIGNED     NOT NULL,
    `symbol`       VARCHAR(20)      NOT NULL,
    `trades`       INT UNSIGNED     NOT NULL DEFAULT 0,
    `lots`         DECIMAL(10,2)    NOT NULL DEFAULT 0.00,
    `pnl`          DECIMAL(12,2)    NOT NULL DEFAULT 0.00,
    `win_rate`     DECIMAL(5,2)     NOT NULL DEFAULT 0.00   COMMENT 'Percentage 0–100',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_challenge_symbol` (`challenge_id`, `symbol`),
    KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Clean demo rows (safe to re-run) ──────────────────────────
DELETE ca FROM `challenge_assets` ca
INNER JOIN `challenges` c ON c.id = ca.challenge_id
WHERE c.order_id LIKE 'DEMO-%' AND c.user_id = 2;

-- ══════════════════════════════════════════════════════════════
--  DEMO-001  10K one_step active  (28 trades)
-- ══════════════════════════════════════════════════════════════
INSERT INTO `challenge_assets` (challenge_id, user_id, symbol, trades, lots, pnl, win_rate)
SELECT c.id, 2, 'EURUSD',  10, 2.50,  312.40, 70.00 FROM `challenges` c WHERE c.order_id = 'DEMO-001' AND c.user_id = 2;
INSERT INTO `challenge_assets` (challenge_id, user_id, symbol, trades, lots, pnl, win_rate)
SELECT c.id, 2, 'XAUUSD',   8, 0.80,  198.20, 62.50 FROM `challenges` c WHERE c.order_id = 'DEMO-001' AND c.user_id = 2;
INSERT INTO `challenge_assets` (challenge_id, user_id, symbol, trades, lots, pnl, win_rate)
SELECT c.id, 2, 'NAS100',   6, 0.30,  145.60, 66.67 FROM `challenges` c WHERE c.order_id = 'DEMO-001' AND c.user_id = 2;
INSERT INTO `challenge_assets` (challenge_id, user_id, symbol, trades, lots, pnl, win_rate)
SELECT c.id, 2, 'GBPUSD',   4, 1.00,   68.80, 50.00 FROM `challenges` c WHERE c.order_id = 'DEMO-001' AND c.user_id = 2;

-- ══════════════════════════════════════════════════════════════
--  DEMO-002  25K two_step phase 2  (42 trades)
-- ══════════════════════════════════════════════════════════════
INSERT INTO `challenge_assets` (challenge_id, user_id, symbol, trades, lots, pnl, win_rate)
SELECT c.id, 2, 'EURUSD',  14, 3.50,  640.00, 71.43 FROM `challenges` c WHERE c.order_id = 'DEMO-002' AND c.user_id = 2;
INSERT INTO `challenge_assets` (challenge_id, user_id, symbol, trades, lots, pnl, win_rate)
SELECT c.id, 2, 'SP500',   12, 0.60,  410.80, 66.67 FROM `challenges` c WHERE c.order_id = 'DEMO-002' AND c.user_id = 2;
INSERT INTO `challenge_assets` (challenge_id, user_id, symbol, trades, lots, pnl, win_rate)
SELECT c.id, 2, 'US30',     8, 0.40,  195.40, 62.50 FROM `challenges` c WHERE c.order_id = 'DEMO-002' AND c.user_id = 2;
INSERT INTO `challenge_assets` (challenge_id, user_id, symbol, trades, lots, pnl, win_rate)
SELECT c.id, 2, 'GBPUSD',   8, 2.00, -45.20, 37.50  FROM `challenges` c WHERE c.order_id = 'DEMO-002' AND c.user_id = 2;

-- ══════════════════════════════════════════════════════════════
--  DEMO-003  50K one_step funded   (est. 55 trades)
-- ══════════════════════════════════════════════════════════════
INSERT INTO `challenge_assets` (challenge_id, user_id, symbol, trades, lots, pnl, win_rate)
SELECT c.id, 2, 'XAUUSD',  20, 2.00, 2140.00, 70.00 FROM `challenges` c WHERE c.order_id = 'DEMO-003' AND c.user_id = 2;
INSERT INTO `challenge_assets` (challenge_id, user_id, symbol, trades, lots, pnl, win_rate)
SELECT c.id, 2, 'NAS100',  18, 0.90, 1820.50, 66.67 FROM `challenges` c WHERE c.order_id = 'DEMO-003' AND c.user_id = 2;
INSERT INTO `challenge_assets` (challenge_id, user_id, symbol, trades, lots, pnl, win_rate)
SELECT c.id, 2, 'EURUSD',  12, 3.00,  680.20, 58.33 FROM `challenges` c WHERE c.order_id = 'DEMO-003' AND c.user_id = 2;
INSERT INTO `challenge_assets` (challenge_id, user_id, symbol, trades, lots, pnl, win_rate)
SELECT c.id, 2, 'BTCUSD',   5, 0.10, -120.40, 40.00 FROM `challenges` c WHERE c.order_id = 'DEMO-003' AND c.user_id = 2;

-- ══════════════════════════════════════════════════════════════
--  DEMO-007  100K one_step funded  (est. 120 trades)
-- ══════════════════════════════════════════════════════════════
INSERT INTO `challenge_assets` (challenge_id, user_id, symbol, trades, lots, pnl, win_rate)
SELECT c.id, 2, 'XAUUSD',  38, 3.80, 5420.00, 71.05 FROM `challenges` c WHERE c.order_id = 'DEMO-007' AND c.user_id = 2;
INSERT INTO `challenge_assets` (challenge_id, user_id, symbol, trades, lots, pnl, win_rate)
SELECT c.id, 2, 'NAS100',  30, 1.50, 4100.80, 66.67 FROM `challenges` c WHERE c.order_id = 'DEMO-007' AND c.user_id = 2;
INSERT INTO `challenge_assets` (challenge_id, user_id, symbol, trades, lots, pnl, win_rate)
SELECT c.id, 2, 'EURUSD',  24, 6.00, 2680.40, 62.50 FROM `challenges` c WHERE c.order_id = 'DEMO-007' AND c.user_id = 2;
INSERT INTO `challenge_assets` (challenge_id, user_id, symbol, trades, lots, pnl, win_rate)
SELECT c.id, 2, 'US30',    16, 0.80, 1240.60, 68.75 FROM `challenges` c WHERE c.order_id = 'DEMO-007' AND c.user_id = 2;
INSERT INTO `challenge_assets` (challenge_id, user_id, symbol, trades, lots, pnl, win_rate)
SELECT c.id, 2, 'GBPUSD',  12, 3.00, -380.20, 41.67 FROM `challenges` c WHERE c.order_id = 'DEMO-007' AND c.user_id = 2;
