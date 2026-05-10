<?php
/**
 * Doji Funding — Request Payout to Wallet
 * POST /api/request-payout.php
 * Credits the requested amount from a funded account to the user's Doji Wallet.
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['error' => 'Method not allowed'], 405);
if (!isLoggedIn()) jsonResponse(['error' => 'Unauthorized'], 403);
if (!verifyCsrf($_POST['csrf'] ?? '')) jsonResponse(['error' => 'Invalid session.'], 403);

$userId      = (int)$_SESSION['user_id'];
$challengeId = (int)($_POST['challenge_id'] ?? 0);
$amount      = round((float)($_POST['amount'] ?? 0), 2);

if ($challengeId < 1 || $amount <= 0) {
    jsonResponse(['error' => 'Invalid request.'], 400);
}

$db = getDB();
if (!$db) jsonResponse(['error' => 'Service unavailable.'], 500);

// Verify ownership + funded status
$stmt = $db->prepare('SELECT id, account_size, total_profit, profit_split FROM challenges WHERE id = ? AND user_id = ? AND status = ?');
$stmt->execute([$challengeId, $userId, 'funded']);
$challenge = $stmt->fetch();

if (!$challenge) {
    jsonResponse(['error' => 'Account not found or not eligible for payout.'], 404);
}

$maxPayout = round((float)$challenge['total_profit'] * ((float)$challenge['profit_split'] / 100), 2);
if ($amount > $maxPayout) {
    jsonResponse(['error' => 'Amount exceeds eligible payout of ' . number_format($maxPayout, 2) . '.'], 400);
}

$db->beginTransaction();
try {
    // Record payout request
    $stmt = $db->prepare(
        'INSERT INTO payouts (user_id, challenge_id, amount, currency, status, requested_at)
         VALUES (?, ?, ?, ?, ?, NOW())'
    );
    $stmt->execute([$userId, $challengeId, $amount, 'USD', 'pending']);
    $payoutId = (int)$db->lastInsertId();

    // Credit wallet balance
    $db->prepare('UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?')
       ->execute([$amount, $userId]);

    // Wallet transaction record
    $desc = 'Payout — Challenge #' . $challengeId . ' (account $' . number_format((float)$challenge['account_size'], 0) . ')';
    $db->prepare(
        'INSERT INTO wallet_transactions (user_id, type, amount, description, reference_id) VALUES (?, ?, ?, ?, ?)'
    )->execute([$userId, 'payout_transfer', $amount, $desc, $payoutId]);

    $db->commit();
    jsonResponse(['success' => true]);
} catch (Exception $e) {
    $db->rollBack();
    error_log('Request payout error: ' . $e->getMessage());
    jsonResponse(['error' => 'Failed to process request. Please try again.'], 500);
}
