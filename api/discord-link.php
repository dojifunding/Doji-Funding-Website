<?php
/**
 * Doji Funding — Discord Link Code API
 * POST /api/discord-link.php
 * Generates a 6-char code the user types in Discord with /link <code>
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['error' => 'Method not allowed'], 405);
if (!isLoggedIn()) jsonResponse(['error' => 'Unauthorized'], 403);
if (!verifyCsrf($_POST['csrf'] ?? '')) jsonResponse(['error' => 'Invalid session.'], 403);

$userId = $_SESSION['user_id'];
$db     = getDB();

// Check if already linked
$stmt = $db->prepare('SELECT discord_id FROM users WHERE id = ?');
$stmt->execute([$userId]);
$row = $stmt->fetch();

if (!empty($row['discord_id'])) {
    jsonResponse(['error' => 'Your account is already linked to Discord.'], 400);
}

// Delete any existing unused codes for this user
$db->prepare('DELETE FROM discord_link_codes WHERE user_id = ?')->execute([$userId]);

// Generate unique 6-char alphanumeric code
do {
    $code = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6));
    $check = $db->prepare('SELECT id FROM discord_link_codes WHERE code = ?');
    $check->execute([$code]);
} while ($check->fetch());

$expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

$stmt = $db->prepare(
    'INSERT INTO discord_link_codes (user_id, code, expires_at) VALUES (?, ?, ?)'
);
$stmt->execute([$userId, $code, $expires]);

jsonResponse(['success' => true, 'code' => $code, 'expires_in' => 600]);
