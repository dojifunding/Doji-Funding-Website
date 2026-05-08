<?php
/**
 * Doji Funding — Toggle Public Profile API
 * POST /api/toggle-public-profile.php
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['error' => 'Method not allowed'], 405);
if (!isLoggedIn()) jsonResponse(['error' => 'Unauthorized'], 401);
if (!verifyCsrf($_POST['csrf'] ?? '')) jsonResponse(['error' => 'Invalid session.'], 403);

$isPublic = (int)($_POST['is_public'] ?? 0) ? 1 : 0;
$userId   = $_SESSION['user_id'];

$db = getDB();
if (!$db) jsonResponse(['error' => 'Service unavailable.'], 500);

try {
    $stmt = $db->prepare('UPDATE users SET is_public = ? WHERE id = ?');
    $stmt->execute([$isPublic, $userId]);
    jsonResponse(['success' => true, 'is_public' => $isPublic]);
} catch (PDOException $e) {
    error_log('Toggle public profile error: ' . $e->getMessage());
    jsonResponse(['error' => 'An error occurred.'], 500);
}
