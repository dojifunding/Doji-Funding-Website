<?php
/**
 * Doji Funding — Update Profile API
 * POST /api/update-profile.php
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['error' => 'Method not allowed'], 405);
if (!isLoggedIn()) jsonResponse(['error' => 'Unauthorized'], 403);
if (!verifyCsrf($_POST['csrf'] ?? '')) jsonResponse(['error' => 'Invalid session.'], 403);

$userId    = $_SESSION['user_id'];
$firstName = trim($_POST['first_name'] ?? '');
$lastName  = trim($_POST['last_name'] ?? '');
$username  = trim($_POST['username'] ?? '');
$phone     = trim($_POST['phone'] ?? '');
$address   = trim($_POST['address'] ?? '');
$city      = trim($_POST['city'] ?? '');
$zipcode   = trim($_POST['zipcode'] ?? '');
$country   = trim($_POST['country'] ?? '');
$region    = trim($_POST['region'] ?? '');

if (strlen($firstName) < 1 || strlen($firstName) > 50) jsonResponse(['error' => 'First name is required.'], 400);
if (strlen($lastName) < 1 || strlen($lastName) > 50) jsonResponse(['error' => 'Last name is required.'], 400);

// Username: optional, but if provided must be valid
$usernameVal = null;
if ($username !== '') {
    if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
        jsonResponse(['error' => 'Username must be 3–30 characters: letters, numbers, underscores only.'], 400);
    }
    $usernameVal = $username;
}

$db = getDB();
if (!$db) jsonResponse(['error' => 'Service unavailable.'], 500);

// Check username uniqueness (if changing)
if ($usernameVal !== null) {
    $check = $db->prepare('SELECT id FROM users WHERE username = ? AND id != ?');
    $check->execute([$usernameVal, $userId]);
    if ($check->fetch()) {
        jsonResponse(['error' => 'This username is already taken. Please choose another.'], 400);
    }
}

try {
    $stmt = $db->prepare('UPDATE users SET first_name=?, last_name=?, username=?, phone=?, address=?, city=?, zipcode=?, country=?, region=? WHERE id=?');
    $stmt->execute([$firstName, $lastName, $usernameVal, $phone ?: null, $address ?: null, $city ?: null, $zipcode ?: null, $country ?: null, $region ?: null, $userId]);

    $_SESSION['user_first_name'] = $firstName;
    $_SESSION['user_last_name']  = $lastName;

    jsonResponse(['success' => true, 'message' => 'Profile updated successfully.']);
} catch (PDOException $e) {
    error_log('Profile update error: ' . $e->getMessage());
    jsonResponse(['error' => 'An error occurred.'], 500);
}
