-- ══════════════════════════════════════════════════════════════
--  DOJI FUNDING — Migration v1.4 — lots_traded per challenge
--  Adds lots_traded column to challenges for Doji Coins tracking.
--  Run: paste in phpMyAdmin SQL tab, or run via CLI.
-- ══════════════════════════════════════════════════════════════

ALTER TABLE `challenges`
    ADD COLUMN `lots_traded` DECIMAL(10,2) NOT NULL DEFAULT 0.00
    AFTER `trading_days`;
