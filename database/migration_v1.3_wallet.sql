-- ══════════════════════════════════════════════════════════════
--  DOJI FUNDING — Migration v1.3 — Doji Wallet + Free challenges
--  Run: mysql -u username -p database_name < migration_v1.3_wallet.sql
-- ══════════════════════════════════════════════════════════════

-- Add wallet balance to users
ALTER TABLE `users`
    ADD COLUMN `wallet_balance` DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER `doji_coins`;

-- Add free challenge flag to challenges
ALTER TABLE `challenges`
    ADD COLUMN `is_free` TINYINT(1) NOT NULL DEFAULT 0 AFTER `platform`,
    MODIFY `platform` ENUM('dxtrade','mt5','ctrader') NOT NULL DEFAULT 'dxtrade';
