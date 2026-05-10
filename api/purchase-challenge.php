<?php
/**
 * Doji Funding — Purchase Challenge API (Mock)
 * POST /api/purchase-challenge.php
 *
 * Creates a challenge record without real payment.
 * Replace the transaction block with a real PSP when payment is ready.
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

// ── Auth ─────────────────────────────────────────────────────
if (!isLoggedIn()) {
    jsonResponse(['error' => 'Authentication required.'], 403);
}

// ── CSRF ─────────────────────────────────────────────────────
$body = json_decode(file_get_contents('php://input'), true);
if (!verifyCsrf($body['csrf'] ?? '')) {
    jsonResponse(['error' => 'Invalid session. Please refresh and try again.'], 403);
}

// ── Input ─────────────────────────────────────────────────────
$currentUser  = getCurrentUser();
$userId       = (int) $currentUser['id'];
$type         = $body['type']         ?? '';
$accountSize  = (int)   ($body['account_size']    ?? 0);
$platform     = $body['platform']     ?? 'ctrader';
$profitTarget1 = (float) ($body['profit_target_1'] ?? 10);
$profitTarget2 = isset($body['profit_target_2']) ? (float) $body['profit_target_2'] : null;
$dailyLoss    = (float) ($body['daily_loss']      ?? 5);
$maxLoss      = (float) ($body['max_loss']        ?? 8);
$profitSplit  = (float) ($body['profit_split']    ?? 80);
$minDays      = (int)   ($body['min_trading_days'] ?? 5);
$consistency  = (float) ($body['consistency_rule'] ?? 30);
$dailyType    = $body['daily_loss_type']  ?? 'intraday';
$maxType      = $body['max_loss_type']    ?? 'intraday';
$payout       = $body['payout_frequency'] ?? 'monthly';
$overnight    = !empty($body['overnight_holding']) ? 1 : 0;
$weekend      = !empty($body['weekend_holding'])   ? 1 : 0;
$basePrice    = (float) ($body['base_price']    ?? 0);
$finalPrice   = (float) ($body['final_price']   ?? 0);
$promoDiscount = (float) ($body['promo_discount'] ?? 0);
$promoCode    = trim($body['promo_code'] ?? '');

// ── Validation ───────────────────────────────────────────────
$allowedTypes     = ['one_step', 'two_step'];
$allowedPlatforms = ['dxtrade', 'ctrader', 'mt5'];
$allowedDailyType = ['intraday', 'eod', 'static'];
$allowedPayout    = ['monthly', 'biweekly', 'weekly'];

if (!in_array($type, $allowedTypes, true)) {
    jsonResponse(['error' => 'Invalid challenge type.'], 400);
}
if (!in_array($platform, $allowedPlatforms, true)) {
    jsonResponse(['error' => 'Invalid platform.'], 400);
}
if (!in_array($dailyType, $allowedDailyType, true) || !in_array($maxType, $allowedDailyType, true)) {
    jsonResponse(['error' => 'Invalid loss type.'], 400);
}
if (!in_array($payout, $allowedPayout, true)) {
    jsonResponse(['error' => 'Invalid payout frequency.'], 400);
}
if ($accountSize <= 0 || $finalPrice <= 0) {
    jsonResponse(['error' => 'Invalid account size or price.'], 400);
}

// ── DB ───────────────────────────────────────────────────────
$db = getDB();
if (!$db) {
    jsonResponse(['error' => 'Service temporarily unavailable.'], 500);
}

try {
    $db->beginTransaction();

    $orderId = 'DOJI-' . strtoupper(bin2hex(random_bytes(4))) . '-' . time();

    // ── Insert challenge ─────────────────────────────────────
    $stmt = $db->prepare("
        INSERT INTO challenges (
            user_id, order_id, type, status, phase, account_size, platform,
            profit_target_1, profit_target_2,
            daily_loss, max_loss, profit_split,
            min_trading_days, consistency_rule,
            daily_loss_type, max_loss_type, payout_frequency,
            overnight_holding, weekend_holding,
            current_balance, peak_balance, current_equity,
            base_price, adjustments, promo_discount, promo_code, final_price,
            purchased_at, started_at, expires_at
        ) VALUES (
            ?, ?, ?, 'active', 1, ?, ?,
            ?, ?,
            ?, ?, ?,
            ?, ?,
            ?, ?, ?,
            ?, ?,
            ?, ?, ?,
            ?, 0, ?, ?, ?,
            NOW(), NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY)
        )
    ");

    $stmt->execute([
        $userId, $orderId, $type, $accountSize, $platform,
        $profitTarget1, $profitTarget2,
        $dailyLoss, $maxLoss, $profitSplit,
        $minDays, $consistency,
        $dailyType, $maxType, $payout,
        $overnight, $weekend,
        $accountSize, $accountSize, $accountSize,
        $basePrice, $promoDiscount, ($promoCode ?: null), $finalPrice,
    ]);

    $challengeId = (int) $db->lastInsertId();

    // ── Insert transaction (mock) ────────────────────────────
    $stmt = $db->prepare("
        INSERT INTO transactions (
            user_id, challenge_id, type, amount, currency,
            status, payment_method, payment_ref, description
        ) VALUES (?, ?, 'challenge_purchase', ?, 'USD', 'completed', 'mock', ?, ?)
    ");
    $stmt->execute([
        $userId,
        $challengeId,
        $finalPrice,
        $orderId,
        ucfirst(str_replace('_', ' ', $type)) . ' — $' . number_format($accountSize) . ' account',
    ]);

    // ── Notification ─────────────────────────────────────────
    $stmt = $db->prepare("
        INSERT INTO notifications (user_id, type, title, message, link)
        VALUES (?, 'success', 'Challenge Activated', ?, '/dashboard.php')
    ");
    $stmt->execute([
        $userId,
        'Your ' . ucfirst(str_replace('_', ' ', $type)) . ' $' . number_format($accountSize) . ' challenge is now active. Good luck!',
    ]);

    $db->commit();

    jsonResponse([
        'success'      => true,
        'challenge_id' => $challengeId,
        'order_id'     => $orderId,
        'message'      => 'Challenge activated successfully.',
    ]);

} catch (PDOException $e) {
    $db->rollBack();
    error_log('Purchase error: ' . $e->getMessage());
    jsonResponse(['error' => 'An error occurred. Please try again.'], 500);
}
