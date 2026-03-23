<?php
/**
 * Doji Funding — 404 Error Page
 * Entry point: /404.php
 */

$currentPage = '404';

// Load configs
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/seo.php';
require_once __DIR__ . '/includes/auth.php';

// Render
http_response_code(404);
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
require_once __DIR__ . '/pages/404.php';
require_once __DIR__ . '/includes/footer.php';
