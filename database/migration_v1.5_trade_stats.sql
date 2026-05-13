-- ══════════════════════════════════════════════════════════════
--  DOJI FUNDING — Migration v1.5 — Trade bias + session distribution
--  Adds per-challenge aggregates for Bias and Trading Session cards.
--  Run: paste in phpMyAdmin SQL tab, or via CLI.
-- ══════════════════════════════════════════════════════════════

ALTER TABLE `challenges`
    ADD COLUMN `long_trades`    INT UNSIGNED NOT NULL DEFAULT 0
        COMMENT 'Number of long positions taken'
        AFTER `losing_trades`,
    ADD COLUMN `short_trades`   INT UNSIGNED NOT NULL DEFAULT 0
        COMMENT 'Number of short positions taken'
        AFTER `long_trades`,
    ADD COLUMN `session_ny`     INT UNSIGNED NOT NULL DEFAULT 0
        COMMENT 'Trades opened during New York session (13:00–22:00 UTC)'
        AFTER `short_trades`,
    ADD COLUMN `session_london` INT UNSIGNED NOT NULL DEFAULT 0
        COMMENT 'Trades opened during London session (07:00–16:00 UTC)'
        AFTER `session_ny`,
    ADD COLUMN `session_asia`   INT UNSIGNED NOT NULL DEFAULT 0
        COMMENT 'Trades opened during Asia/Tokyo session (00:00–09:00 UTC)'
        AFTER `session_london`;
