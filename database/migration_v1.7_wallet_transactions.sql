-- ══════════════════════════════════════════════════════════════
--  Migration v1.7 — Wallet Transactions + Coins Log Tables
-- ══════════════════════════════════════════════════════════════

-- Wallet movements (credits from payouts, debits from challenge purchases)
CREATE TABLE IF NOT EXISTS `wallet_transactions` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`      INT UNSIGNED NOT NULL,
    `type`         ENUM('payout_transfer','challenge_purchase') NOT NULL,
    `amount`       DECIMAL(12,2) NOT NULL COMMENT 'Positive = credit, negative = debit',
    `description`  VARCHAR(255) NOT NULL DEFAULT '',
    `reference_id` INT UNSIGNED DEFAULT NULL COMMENT 'payout.id or challenge.id',
    `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_wt_user_date` (`user_id`, `created_at`),
    INDEX `idx_wt_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Doji Coins event log (volume rewards, bonuses, promo)
CREATE TABLE IF NOT EXISTS `coins_log` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`      INT UNSIGNED NOT NULL,
    `amount`       INT NOT NULL COMMENT 'Coins earned (positive) or spent (negative)',
    `source`       ENUM('volume','bonus','promo','referral') NOT NULL DEFAULT 'volume',
    `description`  VARCHAR(255) NOT NULL DEFAULT '',
    `reference_id` INT UNSIGNED DEFAULT NULL,
    `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_cl_user_date` (`user_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
