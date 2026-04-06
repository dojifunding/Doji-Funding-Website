<?php
/**
 * Doji Funding — User Presets API
 * GET    /api/presets.php          → list user's saved presets
 * POST   /api/presets.php          → save a preset   { csrf, name, config }
 * POST   /api/presets.php          → delete a preset { csrf, action=delete, id }
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isLoggedIn()) { jsonResponse(['error' => 'Unauthorized'], 401); }

$db = getDB();
if (!$db) { jsonResponse(['error' => 'Service unavailable.'], 500); }

/* ── Auto-create table on first use ── */
$db->exec("CREATE TABLE IF NOT EXISTS user_presets (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL,
    name       VARCHAR(100) NOT NULL,
    config     JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$userId = (int) $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

/* ══════════════ GET — list presets ══════════════ */
if ($method === 'GET') {
    $stmt = $db->prepare('SELECT id, name, config, created_at FROM user_presets WHERE user_id = ? ORDER BY created_at DESC LIMIT 20');
    $stmt->execute([$userId]);
    $rows = $stmt->fetchAll();
    foreach ($rows as &$row) {
        $row['config'] = json_decode($row['config'], true);
    }
    jsonResponse(['success' => true, 'presets' => $rows]);
}

/* ══════════════ POST — save or delete ══════════════ */
if ($method === 'POST') {
    if (!verifyCsrf($_POST['csrf'] ?? '')) { jsonResponse(['error' => 'Invalid session.'], 403); }

    $action = $_POST['action'] ?? 'save';

    /* ── Delete ── */
    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id < 1) { jsonResponse(['error' => 'Invalid preset.'], 400); }
        $stmt = $db->prepare('DELETE FROM user_presets WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        jsonResponse(['success' => true]);
    }

    /* ── Save ── */
    $name = trim($_POST['name'] ?? '');
    if (strlen($name) < 1 || strlen($name) > 100) {
        jsonResponse(['error' => 'Please enter a name (1–100 characters).'], 400);
    }

    $configRaw = $_POST['config'] ?? '';
    $config = json_decode($configRaw, true);
    if (!is_array($config)) { jsonResponse(['error' => 'Invalid configuration.'], 400); }

    /* Limit to 20 presets per user */
    $cnt = $db->prepare('SELECT COUNT(*) FROM user_presets WHERE user_id = ?');
    $cnt->execute([$userId]);
    if ((int) $cnt->fetchColumn() >= 20) {
        jsonResponse(['error' => 'Maximum 20 saved presets reached. Delete one first.'], 400);
    }

    $stmt = $db->prepare('INSERT INTO user_presets (user_id, name, config) VALUES (?, ?, ?)');
    $stmt->execute([$userId, $name, json_encode($config)]);
    $newId = (int) $db->lastInsertId();

    jsonResponse(['success' => true, 'preset' => ['id' => $newId, 'name' => $name, 'config' => $config]]);
}

jsonResponse(['error' => 'Method not allowed'], 405);
