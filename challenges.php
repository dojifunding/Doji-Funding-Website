<?php
/**
 * Doji Funding — Challenges Page
 * Entry point: /challenges.php
 */

$currentPage = 'challenges';

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/pricing.php';
require_once __DIR__ . '/config/faq.php';
require_once __DIR__ . '/config/seo.php';
require_once __DIR__ . '/config/presets.php';
require_once __DIR__ . '/includes/auth.php';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
require_once __DIR__ . '/pages/challenges.php';
require_once __DIR__ . '/includes/footer.php';
