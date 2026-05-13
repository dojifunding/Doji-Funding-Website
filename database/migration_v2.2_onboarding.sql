-- ══════════════════════════════════════════════════════════════
--  DOJI FUNDING — Migration v2.2: Onboarding flow tracking
--  Run in phpMyAdmin on doji_funding database
-- ══════════════════════════════════════════════════════════════

ALTER TABLE `users`
    ADD COLUMN `onboarding_modal_seen` TINYINT(1) NOT NULL DEFAULT 0 AFTER `marketing_consent`,
    ADD COLUMN `onboarding_dismissed`  TINYINT(1) NOT NULL DEFAULT 0 AFTER `onboarding_modal_seen`;
