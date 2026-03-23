<?php
/**
 * Doji Funding — Global Application Config
 * 
 * Central configuration for site-wide settings.
 * Edit these values to update across all pages.
 */

define('SITE_NAME', 'Doji Funding®');
define('SITE_URL', 'https://dojifunding.com');
define('SITE_TAGLINE', 'Trade Your Way. Get Funded.');
define('SITE_YEAR', '2026');

// Version (for cache busting CSS/JS/images)
define('ASSET_VERSION', '380');

// Branding
define('BRAND_COLOR_GREEN', '#10B981');
define('BRAND_COLOR_BG', '#08090b');
define('LOGO_FILE', 'assets/img/logo.png?v=' . ASSET_VERSION);

// Social / Trust metrics (update regularly)
define('STAT_FUNDED_TRADERS', '4,200+');
define('STAT_PAID_OUT', '$12M+');
define('STAT_SUPPORT', '24/7');

// Account limits
define('MIN_ACCOUNT', 5000);
define('MAX_ACCOUNT', 100000);
define('ACCOUNT_STEP', 5000);
define('MAX_SPLIT', 90);
