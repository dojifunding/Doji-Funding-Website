<?php
/**
 * Doji Funding — Homepage
 * Entry point: /index.php
 */

$currentPage = 'home';

// Load configs
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/pricing.php';
require_once __DIR__ . '/config/faq.php';
require_once __DIR__ . '/config/seo.php';
require_once __DIR__ . '/includes/auth.php';

// Render
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
require_once __DIR__ . '/pages/home.php';
require_once __DIR__ . '/includes/footer.php';
