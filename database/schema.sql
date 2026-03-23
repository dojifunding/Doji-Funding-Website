-- ══════════════════════════════════════════════════════════════
--  DOJI FUNDING — Complete Database Schema
--  Version: 1.0
--  Engine: MySQL 5.7+ / MariaDB 10.3+
--  Import:  mysql -u username -p database_name < schema.sql
-- ══════════════════════════════════════════════════════════════

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ──────────────────────────────────────────
--  1. USERS
-- ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email`           VARCHAR(255) NOT NULL UNIQUE,
    `password_hash`   VARCHAR(255) NOT NULL,
    `first_name`      VARCHAR(50) NOT NULL,
    `last_name`       VARCHAR(50) NOT NULL,
    `phone_code`      VARCHAR(6) DEFAULT '+1',
    `phone`           VARCHAR(30) DEFAULT NULL,
    `address`         VARCHAR(255) DEFAULT NULL,
    `city`            VARCHAR(100) DEFAULT NULL,
    `zipcode`         VARCHAR(20) DEFAULT NULL,
    `country`         VARCHAR(100) DEFAULT NULL,
    `region`          VARCHAR(100) DEFAULT NULL,
    `marketing_consent` TINYINT(1) NOT NULL DEFAULT 0,
    `avatar_url`      VARCHAR(500) DEFAULT NULL,
    `status`          ENUM('active','suspended','banned') NOT NULL DEFAULT 'active',
    `email_verified`  TINYINT(1) NOT NULL DEFAULT 0,
    `kyc_status`      ENUM('none','pending','approved','rejected') NOT NULL DEFAULT 'none',
    `kyc_document`    VARCHAR(500) DEFAULT NULL,
    `kyc_submitted_at` DATETIME DEFAULT NULL,
    `kyc_reviewed_at` DATETIME DEFAULT NULL,
    `trader_level`    ENUM('rookie','trader','pro','elite') NOT NULL DEFAULT 'rookie',
    `doji_coins`      INT UNSIGNED NOT NULL DEFAULT 0,
    `referral_code`   VARCHAR(20) DEFAULT NULL UNIQUE,
    `referred_by`     INT UNSIGNED DEFAULT NULL,
    `last_login`      DATETIME DEFAULT NULL,
    `login_count`     INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_users_email` (`email`),
    INDEX `idx_users_status` (`status`),
    INDEX `idx_users_kyc` (`kyc_status`),
    INDEX `idx_users_referral` (`referral_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────
--  2. CHALLENGES (Evaluations)
-- ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `challenges` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`         INT UNSIGNED NOT NULL,
    `order_id`        VARCHAR(50) DEFAULT NULL UNIQUE,
    `type`            ENUM('one_step','two_step') NOT NULL,
    `status`          ENUM('active','passed','failed','funded','expired','refunded') NOT NULL DEFAULT 'active',
    `phase`           TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `account_size`    INT UNSIGNED NOT NULL,
    `platform`        ENUM('mt5','ctrader') NOT NULL DEFAULT 'mt5',
    `mt_login`        VARCHAR(50) DEFAULT NULL,
    `mt_password`     VARCHAR(100) DEFAULT NULL,
    `mt_server`       VARCHAR(100) DEFAULT NULL,
    `profit_target_1` DECIMAL(5,2) NOT NULL,
    `profit_target_2` DECIMAL(5,2) DEFAULT NULL,
    `daily_loss`      DECIMAL(5,2) NOT NULL,
    `max_loss`        DECIMAL(5,2) NOT NULL,
    `profit_split`    DECIMAL(5,2) NOT NULL,
    `min_trading_days` INT UNSIGNED NOT NULL DEFAULT 5,
    `consistency_rule` DECIMAL(5,2) NOT NULL DEFAULT 30.00,
    `daily_loss_type` ENUM('intraday','eod','static') NOT NULL DEFAULT 'intraday',
    `max_loss_type`   ENUM('intraday','eod','static') NOT NULL DEFAULT 'intraday',
    `payout_frequency` ENUM('monthly','biweekly','weekly') NOT NULL DEFAULT 'monthly',
    `overnight_holding` TINYINT(1) NOT NULL DEFAULT 0,
    `weekend_holding` TINYINT(1) NOT NULL DEFAULT 0,
    `current_balance` DECIMAL(12,2) NOT NULL DEFAULT 0,
    `peak_balance`    DECIMAL(12,2) NOT NULL DEFAULT 0,
    `current_equity`  DECIMAL(12,2) NOT NULL DEFAULT 0,
    `total_profit`    DECIMAL(12,2) NOT NULL DEFAULT 0,
    `total_trades`    INT UNSIGNED NOT NULL DEFAULT 0,
    `winning_trades`  INT UNSIGNED NOT NULL DEFAULT 0,
    `losing_trades`   INT UNSIGNED NOT NULL DEFAULT 0,
    `trading_days`    INT UNSIGNED NOT NULL DEFAULT 0,
    `profitable_days` INT UNSIGNED NOT NULL DEFAULT 0,
    `best_trade`      DECIMAL(12,2) NOT NULL DEFAULT 0,
    `worst_trade`     DECIMAL(12,2) NOT NULL DEFAULT 0,
    `avg_win`         DECIMAL(12,2) NOT NULL DEFAULT 0,
    `avg_loss`        DECIMAL(12,2) NOT NULL DEFAULT 0,
    `risk_score`      DECIMAL(5,2) DEFAULT NULL,
    `base_price`      DECIMAL(10,2) NOT NULL,
    `adjustments`     DECIMAL(10,2) NOT NULL DEFAULT 0,
    `promo_discount`  DECIMAL(10,2) NOT NULL DEFAULT 0,
    `promo_code`      VARCHAR(30) DEFAULT NULL,
    `final_price`     DECIMAL(10,2) NOT NULL,
    `currency`        VARCHAR(3) NOT NULL DEFAULT 'USD',
    `purchased_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `started_at`      DATETIME DEFAULT NULL,
    `phase2_at`       DATETIME DEFAULT NULL,
    `completed_at`    DATETIME DEFAULT NULL,
    `funded_at`       DATETIME DEFAULT NULL,
    `expires_at`      DATETIME DEFAULT NULL,
    `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_challenges_user` (`user_id`),
    INDEX `idx_challenges_status` (`status`),
    INDEX `idx_challenges_type` (`type`),
    CONSTRAINT `fk_challenges_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────
--  3. PAYOUTS
-- ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `payouts` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`         INT UNSIGNED NOT NULL,
    `challenge_id`    INT UNSIGNED NOT NULL,
    `amount`          DECIMAL(12,2) NOT NULL,
    `currency`        VARCHAR(3) NOT NULL DEFAULT 'USD',
    `method`          ENUM('crypto_btc','crypto_eth','crypto_usdt','bank_transfer','wise','paypal') DEFAULT NULL,
    `wallet_address`  VARCHAR(255) DEFAULT NULL,
    `bank_details`    TEXT DEFAULT NULL,
    `status`          ENUM('pending','processing','completed','rejected','cancelled') NOT NULL DEFAULT 'pending',
    `admin_notes`     TEXT DEFAULT NULL,
    `requested_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `processed_at`    DATETIME DEFAULT NULL,
    `completed_at`    DATETIME DEFAULT NULL,
    `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_payouts_user` (`user_id`),
    INDEX `idx_payouts_challenge` (`challenge_id`),
    INDEX `idx_payouts_status` (`status`),
    CONSTRAINT `fk_payouts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_payouts_challenge` FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────
--  4. TRANSACTIONS
-- ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `transactions` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`         INT UNSIGNED NOT NULL,
    `challenge_id`    INT UNSIGNED DEFAULT NULL,
    `payout_id`       INT UNSIGNED DEFAULT NULL,
    `type`            ENUM('challenge_purchase','payout','refund','promo_credit') NOT NULL,
    `amount`          DECIMAL(12,2) NOT NULL,
    `currency`        VARCHAR(3) NOT NULL DEFAULT 'USD',
    `status`          ENUM('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
    `payment_method`  VARCHAR(50) DEFAULT NULL,
    `payment_ref`     VARCHAR(255) DEFAULT NULL,
    `description`     VARCHAR(255) DEFAULT NULL,
    `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_tx_user` (`user_id`),
    INDEX `idx_tx_type` (`type`),
    CONSTRAINT `fk_tx_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────
--  5. DAILY METRICS
-- ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `daily_metrics` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `challenge_id`    INT UNSIGNED NOT NULL,
    `date`            DATE NOT NULL,
    `open_balance`    DECIMAL(12,2) NOT NULL,
    `close_balance`   DECIMAL(12,2) NOT NULL,
    `high_equity`     DECIMAL(12,2) NOT NULL,
    `low_equity`      DECIMAL(12,2) NOT NULL,
    `daily_pnl`       DECIMAL(12,2) NOT NULL DEFAULT 0,
    `daily_pnl_pct`   DECIMAL(5,2) NOT NULL DEFAULT 0,
    `trades_count`    INT UNSIGNED NOT NULL DEFAULT 0,
    `lots_traded`     DECIMAL(10,2) NOT NULL DEFAULT 0,
    `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_challenge_date` (`challenge_id`, `date`),
    CONSTRAINT `fk_dm_challenge` FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────
--  6. ACHIEVEMENTS
-- ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `achievements` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`         INT UNSIGNED NOT NULL,
    `achievement_key` VARCHAR(50) NOT NULL,
    `label`           VARCHAR(100) NOT NULL,
    `description`     VARCHAR(255) DEFAULT NULL,
    `icon`            VARCHAR(10) DEFAULT NULL,
    `doji_coins`      INT UNSIGNED NOT NULL DEFAULT 0,
    `unlocked_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_user_achievement` (`user_id`, `achievement_key`),
    CONSTRAINT `fk_ach_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────
--  7. NOTIFICATIONS
-- ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `notifications` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`         INT UNSIGNED NOT NULL,
    `type`            ENUM('info','success','warning','error') NOT NULL DEFAULT 'info',
    `title`           VARCHAR(100) NOT NULL,
    `message`         VARCHAR(500) NOT NULL,
    `link`            VARCHAR(255) DEFAULT NULL,
    `is_read`         TINYINT(1) NOT NULL DEFAULT 0,
    `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_notif_user` (`user_id`, `is_read`),
    CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────
--  8. LOGIN ATTEMPTS
-- ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `login_attempts` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email`           VARCHAR(255) NOT NULL,
    `ip_address`      VARCHAR(45) NOT NULL,
    `success`         TINYINT(1) NOT NULL DEFAULT 0,
    `attempted_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_la_email` (`email`, `attempted_at`),
    INDEX `idx_la_ip` (`ip_address`, `attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────
--  9. AUDIT LOG
-- ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `audit_log` (
    `id`              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`         INT UNSIGNED DEFAULT NULL,
    `action`          VARCHAR(100) NOT NULL,
    `entity_type`     VARCHAR(50) DEFAULT NULL,
    `entity_id`       INT UNSIGNED DEFAULT NULL,
    `ip_address`      VARCHAR(45) DEFAULT NULL,
    `user_agent`      VARCHAR(500) DEFAULT NULL,
    `metadata`        TEXT DEFAULT NULL,
    `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_audit_user` (`user_id`),
    INDEX `idx_audit_action` (`action`),
    INDEX `idx_audit_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
-- ══════════════════════════════════════════════════════════════
