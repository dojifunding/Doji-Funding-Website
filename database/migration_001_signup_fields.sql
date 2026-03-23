-- ══════════════════════════════════════════════════════════════
--  MIGRATION 001 — Add full signup fields to users table
--  Run in phpMyAdmin → Import tab or SQL tab
--  
--  NOTE: If a column already exists, that line will error — 
--  just ignore and it will continue with the next one.
-- ══════════════════════════════════════════════════════════════

ALTER TABLE `users` ADD COLUMN `phone_code` VARCHAR(6) DEFAULT '+1' AFTER `last_name`;
ALTER TABLE `users` ADD COLUMN `address` VARCHAR(255) DEFAULT NULL AFTER `phone`;
ALTER TABLE `users` ADD COLUMN `city` VARCHAR(100) DEFAULT NULL AFTER `address`;
ALTER TABLE `users` ADD COLUMN `zipcode` VARCHAR(20) DEFAULT NULL AFTER `city`;
ALTER TABLE `users` ADD COLUMN `region` VARCHAR(100) DEFAULT NULL AFTER `country`;
ALTER TABLE `users` ADD COLUMN `marketing_consent` TINYINT(1) NOT NULL DEFAULT 0 AFTER `referred_by`;

-- ══════════════════════════════════════════════════════════════
