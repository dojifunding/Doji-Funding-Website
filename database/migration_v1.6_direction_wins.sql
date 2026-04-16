-- ══════════════════════════════════════════════════════════════
--  DOJI FUNDING — Migration v1.6 — Long / Short winning trades
--  Adds directional win counters for Bias card win-rate display.
--  MySQL 5.7+ compatible (no IF NOT EXISTS on ADD COLUMN).
-- ══════════════════════════════════════════════════════════════

ALTER TABLE `challenges`
    ADD COLUMN `long_winning_trades`  INT UNSIGNED NOT NULL DEFAULT 0
        COMMENT 'Winning long positions'
        AFTER `long_trades`,
    ADD COLUMN `short_winning_trades` INT UNSIGNED NOT NULL DEFAULT 0
        COMMENT 'Winning short positions'
        AFTER `short_trades`;
