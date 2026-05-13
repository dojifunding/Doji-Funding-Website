-- Migration v2.1: Market Intelligence overview cache
-- Run in phpMyAdmin before deploying api/market-overview.php

CREATE TABLE IF NOT EXISTS `market_overview_cache` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `regime`       ENUM('RISK_ON','RISK_OFF','NEUTRAL') NOT NULL DEFAULT 'NEUTRAL',
    `conviction`   TINYINT UNSIGNED NOT NULL DEFAULT 50,
    `reasoning`    TEXT NOT NULL,
    `agents`       JSON NOT NULL,
    `market_data`  JSON,
    `generated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_generated` (`generated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
