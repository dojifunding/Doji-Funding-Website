<?php
/**
 * Doji Funding — Onboarding State API
 * POST action=modal_seen  → marks welcome modal as viewed
 * POST action=dismiss     → hides the checklist widget permanently
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
$action = $_POST['action'] ?? '';

if (!$userId) {
    echo json_encode(['success' => false, 'error' => 'No session']);
    exit;
}

$db = getDB();
if (!$db) {
    echo json_encode(['success' => false, 'error' => 'DB unavailable']);
    exit;
}

try {
    if ($action === 'modal_seen') {
        $db->prepare('UPDATE users SET onboarding_modal_seen = 1 WHERE id = ?')
           ->execute([$userId]);
        echo json_encode(['success' => true]);
    } elseif ($action === 'dismiss') {
        $db->prepare('UPDATE users SET onboarding_dismissed = 1 WHERE id = ?')
           ->execute([$userId]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }
} catch (PDOException $e) {
    error_log('onboarding API: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'DB error']);
}
