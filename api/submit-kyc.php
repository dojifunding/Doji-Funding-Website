<?php
/**
 * Doji Funding — Submit KYC Document API
 * POST /api/submit-kyc.php
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['error' => 'Method not allowed'], 405);
if (!isLoggedIn()) jsonResponse(['error' => 'Unauthorized'], 401);
if (!verifyCsrf($_POST['csrf'] ?? '')) jsonResponse(['error' => 'Invalid session.'], 403);

$userId = $_SESSION['user_id'];

if (!isset($_FILES['kyc_document']) || $_FILES['kyc_document']['error'] !== UPLOAD_ERR_OK) {
    jsonResponse(['error' => 'Please upload a valid file.'], 400);
}

$file = $_FILES['kyc_document'];
$allowed = ['image/jpeg', 'image/png', 'application/pdf'];
if (!in_array($file['type'], $allowed)) {
    jsonResponse(['error' => 'Only JPG, PNG, and PDF files are accepted.'], 400);
}
if ($file['size'] > 5 * 1024 * 1024) {
    jsonResponse(['error' => 'File must be under 5MB.'], 400);
}

// Create upload dir
$uploadDir = __DIR__ . '/../uploads/kyc/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

// Generate safe filename
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'kyc_' . $userId . '_' . time() . '.' . $ext;
$destination = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $destination)) {
    jsonResponse(['error' => 'Upload failed. Please try again.'], 500);
}

$db = getDB();
if (!$db) jsonResponse(['error' => 'Service unavailable.'], 500);

try {
    $stmt = $db->prepare('UPDATE users SET kyc_status="pending", kyc_document=?, kyc_submitted_at=NOW() WHERE id=?');
    $stmt->execute([$filename, $userId]);

    jsonResponse(['success' => true, 'message' => 'Document submitted! We\'ll review it within 24-48 hours.']);
} catch (PDOException $e) {
    error_log('KYC submit error: ' . $e->getMessage());
    jsonResponse(['error' => 'An error occurred.'], 500);
}
