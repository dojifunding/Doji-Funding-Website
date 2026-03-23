<?php
/**
 * Doji Funding — Login API
 * POST /api/login.php
 * 
 * Authenticates user and creates session.
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

// CSRF check
if (!verifyCsrf($_POST['csrf'] ?? '')) {
    jsonResponse(['error' => 'Invalid session. Please refresh and try again.'], 403);
}

// Collect input
$email    = trim(strtolower($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

// ─── Validation ───
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['error' => 'Please enter a valid email address.'], 400);
}
if (empty($password)) {
    jsonResponse(['error' => 'Please enter your password.'], 400);
}

// ─── Database ───
$db = getDB();
if (!$db) {
    jsonResponse(['error' => 'Service temporarily unavailable. Please try again later.'], 500);
}

try {
    // Find user by email
    $stmt = $db->prepare(
        'SELECT id, email, password_hash, first_name, last_name, created_at, status 
         FROM users WHERE email = ? LIMIT 1'
    );
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        // Generic error to prevent email enumeration
        jsonResponse(['error' => 'Invalid email or password.'], 401);
    }

    // Check if account is active
    if (($user['status'] ?? 'active') !== 'active') {
        jsonResponse(['error' => 'This account has been suspended. Please contact support.'], 403);
    }

    // Verify password
    if (!password_verify($password, $user['password_hash'])) {
        // TODO: Implement login attempt tracking / rate limiting
        jsonResponse(['error' => 'Invalid email or password.'], 401);
    }

    // Update last login
    $stmt = $db->prepare('UPDATE users SET last_login = NOW() WHERE id = ?');
    $stmt->execute([$user['id']]);

    // Create session
    loginUser([
        'id'         => $user['id'],
        'email'      => $user['email'],
        'first_name' => $user['first_name'],
        'last_name'  => $user['last_name'],
        'created_at' => $user['created_at'],
    ]);

    jsonResponse(['success' => true, 'message' => 'Login successful.']);

} catch (PDOException $e) {
    error_log('Login error: ' . $e->getMessage());
    jsonResponse(['error' => 'An error occurred. Please try again.'], 500);
}
