<?php
/**
 * Doji Funding — Market Intelligence Cron Trigger
 *
 * Called by an external cron service (e.g. cron-job.org) every trading day at 09:00 ET.
 * URL: https://dojifunding.com/api/market-trigger.php?secret=YOUR_BOT_SECRET
 *
 * Setup on cron-job.org:
 *   URL      → https://dojifunding.com/api/market-trigger.php?secret=<BOT_SECRET>
 *   Schedule → Every weekday (Mon-Fri) at 09:00
 *   Timezone → America/New_York
 */
require_once __DIR__ . '/../config/app.php';

// ── Auth ────────────────────────────────────────────────
$secret = $_GET['secret'] ?? '';
if (!hash_equals(BOT_SECRET, $secret)) {
    http_response_code(403);
    die(json_encode(['success' => false, 'error' => 'Forbidden']));
}

// ── Trading day check (Mon–Fri) ─────────────────────────
$nyTz    = new DateTimeZone('America/New_York');
$nowNY   = new DateTime('now', $nyTz);
$dow     = (int) $nowNY->format('N'); // 1 = Mon … 7 = Sun
if ($dow >= 6) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'skipped' => true,
        'reason'  => 'Weekend — markets closed',
        'day'     => $nowNY->format('l'),
    ]);
    exit;
}

// ── Force a fresh analysis ──────────────────────────────
$_GET['refresh'] = '1';
require __DIR__ . '/market-overview.php';
