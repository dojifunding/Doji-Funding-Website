<?php
/**
 * Doji Funding — Global Application Config
 *
 * Central configuration for site-wide settings.
 * Secrets (API keys, OAuth secrets) live in config/secrets.local.php — never committed.
 */

// Load local secrets before constants are defined (not committed to git)
if (file_exists(__DIR__ . '/secrets.local.php')) {
    require_once __DIR__ . '/secrets.local.php';
}

define('SITE_NAME', 'Doji Funding®');
define('SITE_URL', 'https://dojifunding.com');
define('SITE_TAGLINE', 'Trade Your Way. Get Funded.');
define('SITE_YEAR', '2026');

// Version (for cache busting CSS/JS/images)
define('ASSET_VERSION', '558');

// Branding
define('BRAND_COLOR_GREEN', '#10B981');
define('BRAND_COLOR_BG', '#08090b');
define('LOGO_FILE', 'assets/img/doji white.svg?v=' . ASSET_VERSION);
define('DOJI_COIN_FILE', 'assets/img/doji-coin.svg?v=' . ASSET_VERSION);

// Social / Trust metrics (update regularly)
define('STAT_FUNDED_TRADERS', '4,200+');
define('STAT_PAID_OUT', '$12M+');
define('STAT_SUPPORT', '24/7');

// Discord OAuth2 — secrets in secrets.local.php
define('DISCORD_CLIENT_ID',    '1502242627205070919');
defined('DISCORD_CLIENT_SECRET') || define('DISCORD_CLIENT_SECRET', '');
define('DISCORD_REDIRECT_URI', 'https://dojifunding.com/discord-callback');
defined('BOT_SECRET')            || define('BOT_SECRET', '');

// AI — Market Intelligence — key in secrets.local.php
define('AI_PROVIDER',         'claude');           // 'claude' or 'openai'
defined('AI_API_KEY')          || define('AI_API_KEY', '');
define('AI_MODEL',            'claude-haiku-4-5-20251001');
define('AI_MARKET_CACHE_MIN', 30);

// Account limits
define('MIN_ACCOUNT', 5000);
define('MAX_ACCOUNT', 100000);
define('ACCOUNT_STEP', 5000);
define('MAX_SPLIT', 90);
