<?php
/**
 * Doji Funding — Authentication Helper
 * 
 * Session management, login state check, CSRF protection.
 * Include this in every page that needs auth awareness.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user data from session
 */
function getCurrentUser() {
    if (!isLoggedIn()) return null;
    return [
        'id'         => $_SESSION['user_id'],
        'email'      => $_SESSION['user_email'] ?? '',
        'first_name' => $_SESSION['user_first_name'] ?? '',
        'last_name'  => $_SESSION['user_last_name'] ?? '',
        'created_at' => $_SESSION['user_created_at'] ?? '',
    ];
}

/**
 * Set user session after successful login
 */
function loginUser($user) {
    $_SESSION['user_id']         = $user['id'];
    $_SESSION['user_email']      = $user['email'];
    $_SESSION['user_first_name'] = $user['first_name'];
    $_SESSION['user_last_name']  = $user['last_name'];
    $_SESSION['user_created_at'] = $user['created_at'];
    session_regenerate_id(true);
}

/**
 * Destroy session on logout
 */
function logoutUser() {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

/**
 * Generate CSRF token
 */
function generateCsrf() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCsrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Send JSON response helper
 */
function jsonResponse($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
