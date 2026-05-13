-- ══════════════════════════════════════════════════════════════
--  DOJI FUNDING — Migration v2.0 — public_profile
--  Adds is_public flag to users table.
--  Run: paste in phpMyAdmin SQL tab.
-- ══════════════════════════════════════════════════════════════

ALTER TABLE `users`
    ADD COLUMN `is_public` TINYINT(1) NOT NULL DEFAULT 0
    AFTER `referral_code`;
