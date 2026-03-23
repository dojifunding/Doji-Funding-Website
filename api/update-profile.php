<?php
/**
 * Doji Funding — Update Profile API
 * POST /api/update-profile.php
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['error' => 'Method not allowed'], 405);
if (!isLoggedIn()) jsonResponse(['error' => 'Unauthorized'], 401);
if (!verifyCsrf($_POST['csrf'] ?? '')) jsonResponse(['error' => 'Invalid session.'], 403);

$userId    = $_SESSION['user_id'];
$firstName = trim($_POST['first_name'] ?? '');
$lastName  = trim($_POST['last_name'] ?? '');
$phone     = trim($_POST['phone'] ?? '');
$address   = trim($_POST['address'] ?? '');
$city      = trim($_POST['city'] ?? '');
$zipcode   = trim($_POST['zipcode'] ?? '');
$country   = trim($_POST['country'] ?? '');
$region    = trim($_POST['region'] ?? '');

if (strlen($firstName) < 1 || strlen($firstName) > 50) jsonResponse(['error' => 'First name is required.'], 400);
if (strlen($lastName) < 1 || strlen($lastName) > 50) jsonResponse(['error' => 'Last name is required.'], 400);

$db = getDB();
if (!$db) jsonResponse(['error' => 'Service unavailable.'], 500);

try {
    $stmt = $db->prepare('UPDATE users SET first_name=?, last_name=?, phone=?, address=?, city=?, zipcode=?, country=?, region=? WHERE id=?');
    $stmt->execute([$firstName, $lastName, $phone ?: null, $address ?: null, $city ?: null, $zipcode ?: null, $country ?: null, $region ?: null, $userId]);

    $_SESSION['user_first_name'] = $firstName;
    $_SESSION['user_last_name']  = $lastName;

    jsonResponse(['success' => true, 'message' => 'Profile updated successfully.']);
} catch (PDOException $e) {
    error_log('Profile update error: ' . $e->getMessage());
    jsonResponse(['error' => 'An error occurred.'], 500);
}
