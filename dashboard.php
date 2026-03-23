<?php
/**
 * Doji Funding — Dashboard
 * Entry point: /dashboard.php
 * Requires authentication.
 */

$currentPage = 'dashboard';

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/pricing.php';
require_once __DIR__ . '/config/faq.php';
require_once __DIR__ . '/config/seo.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/dashboard-data.php';

// Auth guard — redirect to homepage if not logged in
if (!isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$userId  = $_SESSION['user_id'];
$overview = getDashboardOverview($userId);
$challenges = getUserChallenges($userId);
$payouts = getUserPayouts($userId);
$profile = getUserProfile($userId);
$notifications = getUserNotifications($userId);

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
require_once __DIR__ . '/pages/dashboard.php';
require_once __DIR__ . '/includes/footer.php';
