<?php
/**
 * Doji Funding — Discord OAuth2 — Initiate
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isLoggedIn()) {
    header('Location: ' . SITE_URL . '/dashboard.php');
    exit;
}

$userId = $_SESSION['user_id'];
$db     = getDB();

$stmt = $db->prepare('SELECT discord_id FROM users WHERE id = ?');
$stmt->execute([$userId]);
$row = $stmt->fetch();
if (!empty($row['discord_id'])) {
    header('Location: ' . SITE_URL . '/dashboard.php#settings');
    exit;
}

// HMAC-signed state: base64(userId:timestamp:hmac) — verified by bot, no DB needed
$timestamp = time();
$payload   = $userId . ':' . $timestamp;
$sig       = hash_hmac('sha256', $payload, BOT_SECRET);
$state     = base64_encode($payload . ':' . $sig);

$params = http_build_query([
    'client_id'     => DISCORD_CLIENT_ID,
    'redirect_uri'  => DISCORD_REDIRECT_URI,
    'response_type' => 'code',
    'scope'         => 'identify',
    'state'         => $state,
    'prompt'        => 'none',
]);

header('Location: https://discord.com/oauth2/authorize?' . $params);
exit;
