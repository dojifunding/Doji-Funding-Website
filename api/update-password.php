<?php
/**
 * Doji Funding — Update Password API
 * POST /api/update-password.php
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['error' => 'Method not allowed'], 405);
if (!isLoggedIn()) jsonResponse(['error' => 'Unauthorized'], 401);
if (!verifyCsrf($_POST['csrf'] ?? '')) jsonResponse(['error' => 'Invalid session.'], 403);

$userId  = $_SESSION['user_id'];
$current = $_POST['current_password'] ?? '';
$newPw   = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if (empty($current)) jsonResponse(['error' => 'Current password is required.'], 400);
if (strlen($newPw) < 8) jsonResponse(['error' => 'New password must be at least 8 characters.'], 400);
if ($newPw !== $confirm) jsonResponse(['error' => 'Passwords do not match.'], 400);

$db = getDB();
if (!$db) jsonResponse(['error' => 'Service unavailable.'], 500);

try {
    $stmt = $db->prepare('SELECT password_hash FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($current, $user['password_hash'])) {
        jsonResponse(['error' => 'Current password is incorrect.'], 401);
    }

    $hash = password_hash($newPw, PASSWORD_BCRYPT, ['cost' => 12]);
    $stmt = $db->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
    $stmt->execute([$hash, $userId]);

    jsonResponse(['success' => true, 'message' => 'Password updated successfully.']);
} catch (PDOException $e) {
    error_log('Password update error: ' . $e->getMessage());
    jsonResponse(['error' => 'An error occurred.'], 500);
}
