-- ══════════════════════════════════════════════════════════════
--  Migration v1.8 — Expand coins_log source ENUM
--  Adds debit types for purchases & upgrades via Doji Coins.
-- ══════════════════════════════════════════════════════════════

ALTER TABLE `coins_log`
    MODIFY COLUMN `source`
        ENUM('volume','bonus','promo','referral','discount_purchase','account_purchase','profit_split_upgrade')
        NOT NULL DEFAULT 'volume';
