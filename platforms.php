<?php
/**
 * Doji Funding — Platforms Page
 * Entry point: /platforms.php
 */

$currentPage = 'platforms';

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/pricing.php';
require_once __DIR__ . '/config/faq.php';
require_once __DIR__ . '/config/seo.php';
require_once __DIR__ . '/includes/auth.php';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
require_once __DIR__ . '/pages/platforms.php';
require_once __DIR__ . '/includes/footer.php';
