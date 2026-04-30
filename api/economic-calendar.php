<?php
/**
 * Doji Funding — Economic Calendar Proxy
 * Fetches Forex Factory XML, normalises to JSON, caches 1 h server-side.
 */
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

$year  = isset($_GET['year'])  ? (int)$_GET['year']  : (int)date('Y');
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');

if ($year < 2020 || $year > 2035 || $month < 1 || $month > 12) {
    echo json_encode(['events' => [], 'error' => 'invalid_params']);
    exit;
}

$curYear  = (int)date('Y');
$curMonth = (int)date('n');

$cacheFile = sys_get_temp_dir() . '/doji_econ_' . $year . '_' . $month . '.json';
$cacheTTL  = 3600;

/* serve from cache when fresh */
if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTTL) {
    echo file_get_contents($cacheFile);
    exit;
}

/* only FF "thismonth" feed is reliable; return empty for other months */
if ($year !== $curYear || $month !== $curMonth) {
    $out = json_encode(['events' => [], 'error' => 'not_current_month']);
    @file_put_contents($cacheFile, $out);
    echo $out;
    exit;
}

$url = 'https://nfs.faireconomy.media/ff_calendar_thismonth.xml';
$ctx = stream_context_create([
    'http' => [
        'method'     => 'GET',
        'timeout'    => 12,
        'user_agent' => 'Mozilla/5.0 (compatible; DojiBot/1.0)',
    ],
    'ssl' => [
        'verify_peer'      => false,
        'verify_peer_name' => false,
    ],
]);

$xmlStr = @file_get_contents($url, false, $ctx);
if ($xmlStr === false) {
    echo json_encode(['events' => [], 'error' => 'fetch_failed']);
    exit;
}

libxml_use_internal_errors(true);
$xml = simplexml_load_string($xmlStr);
if ($xml === false) {
    echo json_encode(['events' => [], 'error' => 'parse_failed']);
    exit;
}

$events = [];
foreach ($xml->event as $ev) {
    $dateStr = parseFFDate((string)$ev->date);
    if (!$dateStr) continue;

    $events[] = [
        'title'    => trim((string)$ev->title),
        'country'  => strtoupper(trim((string)$ev->country)),
        'date'     => $dateStr,
        'time'     => trim((string)$ev->time),
        'impact'   => trim((string)$ev->impact),
        'forecast' => trim((string)$ev->forecast),
        'previous' => trim((string)$ev->previous),
        'actual'   => trim((string)$ev->actual),
    ];
}

$out = json_encode(['events' => $events, 'error' => null], JSON_UNESCAPED_UNICODE);
@file_put_contents($cacheFile, $out);
echo $out;

/* ── helpers ── */
function parseFFDate($str) {
    $str = trim($str);
    /* FF format: MM-DD-YYYY */
    if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $str, $m)) {
        return sprintf('%04d-%02d-%02d', (int)$m[3], (int)$m[1], (int)$m[2]);
    }
    return '';
}
