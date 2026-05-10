<?php
/**
 * Doji Funding — Support / Bug-report API
 * POST /api/support.php
 * Accepts contact and bug-report form submissions, sends email to team.
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
}

if (!isLoggedIn()) {
    jsonResponse(['success' => false, 'error' => 'Authentication required'], 403);
}

$csrf = $_POST['csrf'] ?? '';
if (!validateCsrfToken($csrf)) {
    jsonResponse(['success' => false, 'error' => 'Invalid session — please reload'], 403);
}

$user  = getCurrentUser();
$email = $user['email'] ?? 'unknown';
$name  = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));

/* ── Determine submission type ── */
$isBug = !empty($_POST['bug_type']);

if ($isBug) {
    $bugType = htmlspecialchars(trim($_POST['bug_type'] ?? ''), ENT_QUOTES, 'UTF-8');
    $area    = htmlspecialchars(trim($_POST['area'] ?? ''), ENT_QUOTES, 'UTF-8');
    $desc    = htmlspecialchars(trim($_POST['description'] ?? ''), ENT_QUOTES, 'UTF-8');

    if ($area === '' || $desc === '') {
        jsonResponse(['success' => false, 'error' => 'Please fill in all required fields'], 422);
    }
    if (strlen($area) > 120 || strlen($desc) > 2000) {
        jsonResponse(['success' => false, 'error' => 'Input exceeds maximum length'], 422);
    }

    $subject = '[BUG REPORT] ' . strtoupper($bugType) . ' — ' . $area;
    $body    = "BUG REPORT\n"
             . "==========\n"
             . "From    : {$name} <{$email}>\n"
             . "Type    : {$bugType}\n"
             . "Where   : {$area}\n\n"
             . "Description:\n{$desc}\n";

} else {
    $category = htmlspecialchars(trim($_POST['category'] ?? ''), ENT_QUOTES, 'UTF-8');
    $subj     = htmlspecialchars(trim($_POST['subject'] ?? ''), ENT_QUOTES, 'UTF-8');
    $message  = htmlspecialchars(trim($_POST['message'] ?? ''), ENT_QUOTES, 'UTF-8');

    if ($subj === '' || $message === '') {
        jsonResponse(['success' => false, 'error' => 'Please fill in all required fields'], 422);
    }
    if (strlen($subj) > 120 || strlen($message) > 2000) {
        jsonResponse(['success' => false, 'error' => 'Input exceeds maximum length'], 422);
    }

    $subject = '[SUPPORT] [' . strtoupper($category) . '] ' . $subj;
    $body    = "SUPPORT REQUEST\n"
             . "===============\n"
             . "From     : {$name} <{$email}>\n"
             . "Category : {$category}\n"
             . "Subject  : {$subj}\n\n"
             . "Message:\n{$message}\n";
}

$to      = 'hello@dojifunding.com';
$headers = "From: noreply@dojifunding.com\r\n"
         . "Reply-To: {$email}\r\n"
         . "X-Mailer: Doji-Support/1.0\r\n"
         . "Content-Type: text/plain; charset=UTF-8";

$sent = mail($to, $subject, $body, $headers);

if (!$sent) {
    jsonResponse(['success' => false, 'error' => 'Failed to send — please contact us directly at hello@dojifunding.com'], 500);
}

jsonResponse(['success' => true]);
