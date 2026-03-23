-- ══════════════════════════════════════════════════════════════
--  DOJI FUNDING — Migration v1.1: Add user registration fields
--  Run this in phpMyAdmin if you already imported schema.sql
-- ══════════════════════════════════════════════════════════════

ALTER TABLE `users`
    ADD COLUMN `phone_code` VARCHAR(6) DEFAULT '+1' AFTER `last_name`,
    ADD COLUMN `address` VARCHAR(255) DEFAULT NULL AFTER `phone`,
    ADD COLUMN `city` VARCHAR(100) DEFAULT NULL AFTER `address`,
    ADD COLUMN `zipcode` VARCHAR(20) DEFAULT NULL AFTER `city`,
    ADD COLUMN `region_state` VARCHAR(100) DEFAULT NULL AFTER `country`,
    ADD COLUMN `marketing_consent` TINYINT(1) NOT NULL DEFAULT 0 AFTER `region_state`;
