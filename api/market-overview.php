<?php
/**
 * Doji Funding — Market Intelligence API
 * GET  /api/market-overview.php           → cached or fresh analysis
 * GET  /api/market-overview.php?refresh=1 → force fresh analysis
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isLoggedIn()) jsonResponse(['success' => false, 'error' => 'Unauthorized'], 403);

header('Content-Type: application/json');
$forceRefresh = isset($_GET['refresh']);

// ── Check cache (valid if generated today in NY timezone) ────────────
$db     = getDB();
$cached = null;
if ($db) {
    try {
        $stmt   = $db->query("SELECT * FROM market_overview_cache ORDER BY generated_at DESC LIMIT 1");
        $cached = $stmt->fetch() ?: null;
    } catch (PDOException $e) {
        error_log('market_overview cache read: ' . $e->getMessage());
    }
}

$nyTz    = new DateTimeZone('America/New_York');
$todayNY = (new DateTime('now', $nyTz))->format('Y-m-d');

$cacheDay = $cached
    ? (new DateTime($cached['generated_at']))->setTimezone($nyTz)->format('Y-m-d')
    : null;

$cacheAgeMins = $cached
    ? (time() - strtotime($cached['generated_at'])) / 60
    : 999;

$cacheValid = $cached && ($cacheDay === $todayNY) && !$forceRefresh;

if ($cacheValid) {
    echo json_encode([
        'success'      => true,
        'cached'       => true,
        'generated_at' => (new DateTime($cached['generated_at']))->setTimezone($nyTz)->format('H:i') . ' ET',
        'age_minutes'  => (int) round($cacheAgeMins),
        'regime'       => $cached['regime'],
        'conviction'   => (int) $cached['conviction'],
        'reasoning'    => $cached['reasoning'],
        'agents'       => json_decode($cached['agents'], true),
    ]);
    exit;
}

// ── Fetch live market data ───────────────────────────────────────────
$marketData = fetchMarketData();

// ── Call AI ─────────────────────────────────────────────────────────
$analysis = callMarketAI($marketData);

if (!$analysis) {
    if ($cached) {
        echo json_encode([
            'success'      => true,
            'cached'       => true,
            'stale'        => true,
            'generated_at' => (new DateTime($cached['generated_at']))->setTimezone($nyTz)->format('H:i') . ' ET',
            'age_minutes'  => (int) round($cacheAgeMins),
            'regime'       => $cached['regime'],
            'conviction'   => (int) $cached['conviction'],
            'reasoning'    => $cached['reasoning'],
            'agents'       => json_decode($cached['agents'], true),
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error'   => empty(AI_API_KEY)
                ? 'Set AI_API_KEY in config/app.php to enable Market Intelligence.'
                : 'AI analysis unavailable — try again shortly.',
        ]);
    }
    exit;
}

// ── Persist to cache ─────────────────────────────────────────────────
if ($db) {
    try {
        $stmt = $db->prepare(
            "INSERT INTO market_overview_cache (regime, conviction, reasoning, agents, market_data)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $analysis['verdict']['regime'],
            $analysis['verdict']['conviction'],
            $analysis['verdict']['reasoning'],
            json_encode($analysis['agents']),
            json_encode($marketData),
        ]);
    } catch (PDOException $e) {
        error_log('market_overview cache write: ' . $e->getMessage());
    }
}

echo json_encode([
    'success'      => true,
    'cached'       => false,
    'generated_at' => (new DateTime('now', $nyTz))->format('H:i') . ' ET',
    'age_minutes'  => 0,
    'regime'       => $analysis['verdict']['regime'],
    'conviction'   => $analysis['verdict']['conviction'],
    'reasoning'    => $analysis['verdict']['reasoning'],
    'agents'       => $analysis['agents'],
    'market_data'  => $marketData,
]);

// ════════════════════════════════════════════════════════════════════
// MARKET DATA FETCHERS
// ════════════════════════════════════════════════════════════════════

function fetchMarketData(): array
{
    $symbols = [
        'SPY' => '^GSPC',
        'NDX' => '^NDX',
        'VIX' => '^VIX',
        'GLD' => 'GC=F',
        'OIL' => 'CL=F',
        'DXY' => 'DX-Y.NYB',
    ];

    $data = [];
    foreach ($symbols as $key => $symbol) {
        $q = fetchYahooQuote($symbol);
        if ($q) $data[$key] = $q;
    }

    $fg = fetchFearGreed();
    if ($fg) $data['fear_greed'] = $fg;

    return $data;
}

function fetchYahooQuote(string $symbol): ?array
{
    $url = 'https://query1.finance.yahoo.com/v8/finance/chart/'
         . urlencode($symbol)
         . '?interval=1d&range=5d';

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 6,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
        CURLOPT_HTTPHEADER     => ['Accept: application/json'],
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $raw = curl_exec($ch);
    curl_close($ch);
    if (!$raw) return null;

    $json   = json_decode($raw, true);
    $result = $json['chart']['result'][0] ?? null;
    if (!$result) return null;

    $closes = array_values(array_filter(
        $result['indicators']['quote'][0]['close'] ?? [],
        fn($v) => $v !== null
    ));
    if (count($closes) < 2) return null;

    $current  = round(end($closes), 2);
    $prev     = round($closes[count($closes) - 2], 2);
    $fiveAgo  = round($closes[0], 2);
    $dayChg   = $prev != 0 ? round((($current - $prev) / $prev) * 100, 2) : 0;
    $fiveDChg = $fiveAgo != 0 ? round((($current - $fiveAgo) / $fiveAgo) * 100, 2) : 0;

    return ['price' => $current, 'day_chg' => $dayChg, 'five_chg' => $fiveDChg];
}

function fetchFearGreed(): ?array
{
    $ch = curl_init('https://api.alternative.me/fng/?limit=1');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 5,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $raw = curl_exec($ch);
    curl_close($ch);
    if (!$raw) return null;

    $json  = json_decode($raw, true);
    $entry = $json['data'][0] ?? null;
    return $entry ? ['score' => (int) $entry['value'], 'label' => $entry['value_classification']] : null;
}

// ════════════════════════════════════════════════════════════════════
// AI CALL
// ════════════════════════════════════════════════════════════════════

function callMarketAI(array $md): ?array
{
    if (empty(AI_API_KEY)) return null;

    $date  = date('F j, Y');
    $lines = [];

    if (isset($md['SPY'])) $lines[] = "S&P 500: \${$md['SPY']['price']} ({$md['SPY']['day_chg']}% today, {$md['SPY']['five_chg']}% 5-day trend)";
    if (isset($md['NDX'])) $lines[] = "NASDAQ 100: \${$md['NDX']['price']} ({$md['NDX']['day_chg']}% today)";
    if (isset($md['VIX'])) $lines[] = "VIX Volatility Index: {$md['VIX']['price']} ({$md['VIX']['day_chg']}% today)";
    if (isset($md['GLD'])) $lines[] = "Gold: \${$md['GLD']['price']} ({$md['GLD']['day_chg']}% today)";
    if (isset($md['OIL'])) $lines[] = "Crude Oil: \${$md['OIL']['price']} ({$md['OIL']['day_chg']}% today)";
    if (isset($md['DXY'])) $lines[] = "US Dollar Index: {$md['DXY']['price']} ({$md['DXY']['day_chg']}% today)";
    if (isset($md['fear_greed'])) $lines[] = "Fear & Greed Index: {$md['fear_greed']['score']}/100 ({$md['fear_greed']['label']})";

    if (empty($lines)) {
        $lines[] = "Live market data unavailable — use your general knowledge of conditions as of {$date}.";
    }

    $dataBlock = implode("\n", $lines);

    $systemPrompt = 'You are a professional multi-agent market regime analysis system. '
        . 'You receive current market data and produce structured analysis simulating 6 specialist AI agents. '
        . 'Respond with ONLY valid JSON — no markdown, no explanation outside the JSON object.';

    $userPrompt = <<<EOT
Analyze the current market regime using this live data. Today is {$date}.

MARKET DATA:
{$dataBlock}

Return this EXACT JSON structure (all 6 agents required, all fields required):
{
  "agents": [
    {"id":"macro","name":"MACRO ANALYST","opinion":"BULLISH","confidence":70,"summary":"One concise sentence on macro conditions."},
    {"id":"volatility","name":"VOLATILITY SPECIALIST","opinion":"NEUTRAL","confidence":55,"summary":"One concise sentence on VIX and volatility regime."},
    {"id":"sentiment","name":"SENTIMENT ANALYST","opinion":"BULLISH","confidence":75,"summary":"One concise sentence on fear/greed and investor sentiment."},
    {"id":"technicals","name":"TECHNICALS ANALYST","opinion":"BULLISH","confidence":68,"summary":"One concise sentence on price action and momentum."},
    {"id":"flow","name":"EQUITY FLOW ANALYST","opinion":"NEUTRAL","confidence":60,"summary":"One concise sentence on equity positioning and flow."},
    {"id":"global","name":"GLOBAL ANALYST","opinion":"BULLISH","confidence":65,"summary":"One concise sentence on USD, commodities, cross-asset."}
  ],
  "verdict": {
    "regime": "RISK_ON",
    "conviction": 68,
    "reasoning": "Two to three sentences synthesizing all agent views into a final market regime conclusion."
  }
}
EOT;

    return AI_PROVIDER === 'openai'
        ? callOpenAI($systemPrompt, $userPrompt)
        : callClaude($systemPrompt, $userPrompt);
}

function callClaude(string $system, string $user): ?array
{
    $payload = json_encode([
        'model'      => AI_MODEL,
        'max_tokens' => 1200,
        'system'     => $system,
        'messages'   => [['role' => 'user', 'content' => $user]],
    ]);

    $ch = curl_init('https://api.anthropic.com/v1/messages');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'x-api-key: ' . AI_API_KEY,
            'anthropic-version: 2023-06-01',
        ],
    ]);

    $raw = curl_exec($ch);
    curl_close($ch);
    if (!$raw) return null;

    $resp = json_decode($raw, true);
    $text = $resp['content'][0]['text'] ?? null;
    return $text ? parseAIResponse($text) : null;
}

function callOpenAI(string $system, string $user): ?array
{
    $payload = json_encode([
        'model'           => AI_MODEL,
        'max_tokens'      => 1200,
        'response_format' => ['type' => 'json_object'],
        'messages'        => [
            ['role' => 'system', 'content' => $system],
            ['role' => 'user',   'content' => $user],
        ],
    ]);

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . AI_API_KEY,
        ],
    ]);

    $raw = curl_exec($ch);
    curl_close($ch);
    if (!$raw) return null;

    $resp = json_decode($raw, true);
    $text = $resp['choices'][0]['message']['content'] ?? null;
    return $text ? parseAIResponse($text) : null;
}

function parseAIResponse(string $text): ?array
{
    $text = trim(preg_replace('/^```(?:json)?\s*/m', '', preg_replace('/\s*```\s*$/m', '', $text)));

    $data = json_decode($text, true);
    if (!$data || !isset($data['agents'], $data['verdict'])) return null;

    $allowed = ['RISK_ON', 'RISK_OFF', 'NEUTRAL'];
    if (!in_array($data['verdict']['regime'] ?? '', $allowed, true)) {
        $data['verdict']['regime'] = 'NEUTRAL';
    }
    $data['verdict']['conviction'] = max(0, min(100, (int) ($data['verdict']['conviction'] ?? 50)));

    foreach ($data['agents'] as &$a) {
        $a['opinion']    = strtoupper($a['opinion'] ?? 'NEUTRAL');
        $a['confidence'] = max(0, min(100, (int) ($a['confidence'] ?? 50)));
    }

    return $data;
}
