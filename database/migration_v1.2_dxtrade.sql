-- ══════════════════════════════════════════════════════════════
--  DOJI FUNDING — Migration v1.2 — DXTrade platform support
--  Run: mysql -u username -p database_name < migration_v1.2_dxtrade.sql
-- ══════════════════════════════════════════════════════════════

-- Update platform ENUM to include dxtrade
ALTER TABLE `challenges`
    MODIFY `platform` ENUM('dxtrade','mt5','ctrader') NOT NULL DEFAULT 'dxtrade';

-- Rename MT columns to generic trading account credentials
ALTER TABLE `challenges`
    CHANGE `mt_login`    `account_login`    VARCHAR(50)  DEFAULT NULL,
    CHANGE `mt_password` `account_password` VARCHAR(100) DEFAULT NULL,
    CHANGE `mt_server`   `account_server`   VARCHAR(100) DEFAULT NULL;
