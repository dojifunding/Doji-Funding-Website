<?php
/**
 * Doji Funding — Discord OAuth2 — Final Callback
 * GET /api/discord-callback.php?user_id=X&discord_id=Y&ts=Z&sig=S
 *
 * Called by the bot (via browser redirect) after Discord OAuth completes.
 * Verifies the HMAC signature, then saves discord_id to the database.
 * No outgoing requests — only PHP ↔ MySQL.
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$redirect = function ($param) {
    header('Location: ' . SITE_URL . '/dashboard.php?discord=' . $param . '#settings');
    exit;
};

$userId    = (int)  ($_GET['user_id']    ?? 0);
$discordId = trim(   $_GET['discord_id'] ?? '');
$timestamp = (int)  ($_GET['ts']         ?? 0);
$sig       = trim(   $_GET['sig']        ?? '');

if (!$userId || !$discordId || !$timestamp || !$sig) $redirect('token_error');

// 5-minute replay protection
if (abs(time() - $timestamp) > 300) $redirect('token_error');

// Verify HMAC
$payload  = $userId . ':' . $discordId . ':' . $timestamp;
$expected = hash_hmac('sha256', $payload, BOT_SECRET);
if (!hash_equals($expected, $sig)) $redirect('token_error');

$db = getDB();

// Conflict: this Discord account is already linked to a different user
$stmt = $db->prepare('SELECT id FROM users WHERE discord_id = ? AND id != ?');
$stmt->execute([$discordId, $userId]);
if ($stmt->fetch()) $redirect('already_linked');

$stmt = $db->prepare('UPDATE users SET discord_id = ? WHERE id = ?');
$stmt->execute([$discordId, $userId]);

$redirect('linked');
