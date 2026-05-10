<?php
/**
 * Doji Funding — Discord Save (internal, called by the bot only)
 * POST /api/discord-save.php
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); exit;
}

$userId    = (int)($_POST['user_id']    ?? 0);
$discordId = trim($_POST['discord_id'] ?? '');
$sig       = trim($_POST['sig']        ?? '');

if (!$userId || !$discordId || !$sig) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing fields']);
    exit;
}

// Verify HMAC signature
$expected = hash_hmac('sha256', $userId . ':' . $discordId, BOT_SECRET);
if (!hash_equals($expected, $sig)) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid signature']);
    exit;
}

$db = getDB();

// Check not already linked to another account
$stmt = $db->prepare('SELECT id FROM users WHERE discord_id = ? AND id != ?');
$stmt->execute([$discordId, $userId]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['error' => 'already_linked']);
    exit;
}

$stmt = $db->prepare('UPDATE users SET discord_id = ? WHERE id = ?');
$stmt->execute([$discordId, $userId]);

echo json_encode(['success' => true]);
