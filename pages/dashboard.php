<?php
/**
 * Doji Funding — Dashboard Page
 * Layout: fixed sidebar + fixed topbar + scrollable content (Phidias-style)
 */
?>
<?php require_once __DIR__ . '/../includes/pixel-icons.php'; ?>
<style>
/* Nothing Style — supprime grain/bruit/scanlines */
body::before  { opacity: 0 !important; animation: none !important; }
.noise        { display: none !important; }
.scanlines    { display: none !important; }
</style>
<?php

$user = getCurrentUser();
$kycLabels  = ['none' => 'Not Submitted', 'pending' => 'Under Review', 'approved' => 'Verified', 'rejected' => 'Rejected'];
$kycStatus  = $profile['kyc_status'] ?? 'none';
$kycClass   = ['none' => 'kyc-none', 'pending' => 'kyc-pending', 'approved' => 'kyc-approved', 'rejected' => 'kyc-rejected'];
$initials   = strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1));

// ── Onboarding state ──────────────────────────────────────
$onbSteps = [
    'welcome'   => true,
    'profile'   => !empty($profile['phone']) && !empty($profile['country']),
    'kyc'       => ($profile['kyc_status'] ?? 'none') !== 'none',
    'challenge' => !empty($challenges),
    'discord'   => !empty($profile['discord_id']),
];
$onbCompletedCount = count(array_filter($onbSteps));
$showOnbModal      = empty($profile['onboarding_modal_seen']);
$showOnbChecklist  = empty($profile['onboarding_dismissed']);
// ──────────────────────────────────────────────────────────

// ── Topbar: total allocation across active + funded accounts ──
$topbar_capital = 0;
$topbar_pnl        = 0;
foreach ($challenges as $_ch) {
    if (in_array($_ch['status'], ['active', 'funded'])) {
        $topbar_capital += (float)($_ch['account_size'] ?? 0);
        $topbar_pnl        += (float)($_ch['total_profit'] ?? 0);
    }
}
$topbar_perf_pct = $topbar_capital > 0 ? ($topbar_pnl / $topbar_capital) * 100 : 0;

// ── Topbar: cumulative Doji Coins from all accounts (1 coin / lot) ──
$topbar_coins = 0;
foreach ($challenges as $_ch) {
    $topbar_coins += (int)floor((float)($_ch['lots_traded'] ?? 0));
}

// ── Payout notification counts (topbar badge + sidebar) ──
// Demo data injected once here if no real payouts exist, so badge and tab stay in sync
if (empty($payouts)) {
    $payouts = [
        ['id'=>1,'challenge_id'=>1,'account_size'=>100000,'challenge_type'=>'one_step', 'method'=>'rise',    'amount'=>4200.00,'status'=>'completed',      'requested_at'=>'2024-11-10 10:00:00','processed_at'=>'2024-11-17 14:00:00','action_detail'=>''],
        ['id'=>2,'challenge_id'=>2,'account_size'=>50000, 'challenge_type'=>'two_step','method'=>'confirmo','amount'=>1850.00,'status'=>'completed',      'requested_at'=>'2024-12-05 09:30:00','processed_at'=>'2024-12-12 16:00:00','action_detail'=>''],
        ['id'=>3,'challenge_id'=>1,'account_size'=>100000,'challenge_type'=>'one_step', 'method'=>'rise',    'amount'=>3100.00,'status'=>'action_required','requested_at'=>'2025-01-20 11:00:00','processed_at'=>null,                 'action_detail'=>'Justificatif de domicile'],
        ['id'=>4,'challenge_id'=>3,'account_size'=>200000,'challenge_type'=>'one_step', 'method'=>'confirmo','amount'=>8500.00,'status'=>'pending',       'requested_at'=>'2025-02-14 15:00:00','processed_at'=>null,                 'action_detail'=>''],
        ['id'=>5,'challenge_id'=>2,'account_size'=>50000, 'challenge_type'=>'two_step','method'=>'rise',    'amount'=>2200.00,'status'=>'completed',      'requested_at'=>'2025-03-01 08:00:00','processed_at'=>'2025-03-08 12:00:00','action_detail'=>''],
        ['id'=>6,'challenge_id'=>1,'account_size'=>100000,'challenge_type'=>'one_step', 'method'=>'confirmo','amount'=>5750.00,'status'=>'pending',       'requested_at'=>'2025-04-01 10:00:00','processed_at'=>null,                 'action_detail'=>''],
    ];
}
$notifPayoutPending = 0;
$notifPayoutAction  = 0;
foreach ($payouts as $_p) {
    if (in_array($_p['status'], ['pending', 'processing'])) $notifPayoutPending++;
    if ($_p['status'] === 'action_required') $notifPayoutAction++;
}
$notifTotal = $notifPayoutPending + $notifPayoutAction;

// ── Competition helper functions ──
function compTimeLeft(string $endsStr): string {
    $now = new DateTime('now');
    $end = new DateTime($endsStr);
    if ($now >= $end) return '00:00:00';
    $s = $end->getTimestamp() - $now->getTimestamp();
    $d = (int)floor($s / 86400); $s -= $d * 86400;
    $h = (int)floor($s / 3600);  $s -= $h * 3600;
    $m = (int)floor($s / 60);    $s -= $m * 60;
    return $d > 0 ? sprintf('%dD %02d:%02d:%02d', $d, $h, $m, $s) : sprintf('%02d:%02d:%02d', $h, $m, $s);
}
function compTimeUntil(string $startsStr): string {
    $now   = new DateTime('now');
    $start = new DateTime($startsStr);
    if ($now >= $start) return '00:00:00';
    $s = $start->getTimestamp() - $now->getTimestamp();
    $d = (int)floor($s / 86400); $s -= $d * 86400;
    $h = (int)floor($s / 3600);  $s -= $h * 3600;
    $m = (int)floor($s / 60);    $s -= $m * 60;
    return $d > 0 ? sprintf('%dD %02d:%02d:%02d', $d, $h, $m, $s) : sprintf('%02d:%02d:%02d', $h, $m, $s);
}

// ── Competitions demo data ──
$competitions_all = [
    ['id'=>1,  'name'=>'Monthly Traders Cup',   'edition'=>'May 2026',      'type'=>'free', 'entry'=>0,    'organizer'=>'Doji Funding','starts'=>'2026-05-01 00:00:00','ends'=>'2026-05-31 23:59:59','participants'=>0,  'platform'=>'DXtrade','status'=>'live',    'category'=>'monthly',      'prize_pool'=>5000, 'featured'=>true],
    ['id'=>2,  'name'=>'Elite Challenge Series','edition'=>'Season 3',      'type'=>'paid', 'entry'=>25,   'organizer'=>'Doji Funding','starts'=>'2026-04-25 00:00:00','ends'=>'2026-05-25 23:59:59','participants'=>89, 'platform'=>'DXtrade','status'=>'live',    'category'=>'championship', 'prize_pool'=>25000,'featured'=>false],
    ['id'=>100,'name'=>'Monthly Traders Cup',   'edition'=>'April 2026',    'type'=>'free', 'entry'=>0,    'organizer'=>'Doji Funding','starts'=>'2026-04-01 00:00:00','ends'=>'2026-04-30 23:59:59','participants'=>247,'platform'=>'DXtrade','status'=>'ended',   'category'=>'monthly',      'prize_pool'=>5000, 'featured'=>false],
    ['id'=>101,'name'=>'Monthly Traders Cup',   'edition'=>'March 2026',    'type'=>'free', 'entry'=>0,    'organizer'=>'Doji Funding','starts'=>'2026-03-01 00:00:00','ends'=>'2026-03-31 23:59:59','participants'=>312,'platform'=>'DXtrade','status'=>'ended',   'category'=>'monthly',      'prize_pool'=>5000, 'featured'=>false],
    ['id'=>102,'name'=>'Monthly Traders Cup',   'edition'=>'February 2026', 'type'=>'free', 'entry'=>0,    'organizer'=>'Doji Funding','starts'=>'2026-02-01 00:00:00','ends'=>'2026-02-28 23:59:59','participants'=>289,'platform'=>'DXtrade','status'=>'ended',   'category'=>'monthly',      'prize_pool'=>5000, 'featured'=>false],
    ['id'=>103,'name'=>'Monthly Traders Cup',   'edition'=>'January 2026',  'type'=>'free', 'entry'=>0,    'organizer'=>'Doji Funding','starts'=>'2026-01-01 00:00:00','ends'=>'2026-01-31 23:59:59','participants'=>201,'platform'=>'DXtrade','status'=>'ended',   'category'=>'monthly',      'prize_pool'=>5000, 'featured'=>false],
    ['id'=>201,'name'=>'Elite Challenge Series','edition'=>'Season 2',      'type'=>'paid', 'entry'=>25,   'organizer'=>'Doji Funding','starts'=>'2026-03-01 00:00:00','ends'=>'2026-03-31 23:59:59','participants'=>54, 'platform'=>'DXtrade','status'=>'ended',   'category'=>'championship', 'prize_pool'=>25000,'featured'=>false],
    ['id'=>202,'name'=>'Elite Challenge Series','edition'=>'Season 1',      'type'=>'paid', 'entry'=>25,   'organizer'=>'Doji Funding','starts'=>'2026-02-01 00:00:00','ends'=>'2026-02-28 23:59:59','participants'=>41, 'platform'=>'DXtrade','status'=>'ended',   'category'=>'championship', 'prize_pool'=>25000,'featured'=>false],
];
$comp_joined_ids = [1, 101];
$comp_featured   = null;
foreach ($competitions_all as $_c) { if (!empty($_c['featured'])) { $comp_featured = $_c; break; } }
if (!$comp_featured) { foreach ($competitions_all as $_c) { if ($_c['status'] === 'live') { $comp_featured = $_c; break; } } }
if (!$comp_featured) { foreach ($competitions_all as $_c) { if ($_c['status'] === 'upcoming') { $comp_featured = $_c; break; } } }

// ── Competition leaderboards demo data ──
$comp_leaderboards = [
    1 => [
        ['rank'=>1, 'uid'=>101,'name'=>'Rahul K',    'country'=>'IN','trades'=>179,'winRate'=>75, 'profitPct'=>1988.54,'profit'=>1988536.90,'pair'=>'XAUUSD','avgWin'=>14850,'avgLoss'=>7070,'avgHold'=>45, 'avgRR'=>2.10],
        ['rank'=>2, 'uid'=>102,'name'=>'Mahendra J', 'country'=>'IN','trades'=>200,'winRate'=>100,'profitPct'=>1701.38,'profit'=>1701383.56,'pair'=>'NAS100','avgWin'=>8510, 'avgLoss'=>3040,'avgHold'=>30, 'avgRR'=>2.80],
        ['rank'=>3, 'uid'=>103,'name'=>'Dikshit S',  'country'=>'IN','trades'=>257,'winRate'=>67, 'profitPct'=>1105.22,'profit'=>1105219.28,'pair'=>'EURUSD','avgWin'=>6420, 'avgLoss'=>3570,'avgHold'=>65, 'avgRR'=>1.80],
        ['rank'=>4, 'uid'=>104,'name'=>'Raushan K',  'country'=>'IN','trades'=>88, 'winRate'=>52, 'profitPct'=>1008.06,'profit'=>1008061.40,'pair'=>'GBPUSD','avgWin'=>22050,'avgLoss'=>8820,'avgHold'=>90, 'avgRR'=>2.50],
        ['rank'=>5, 'uid'=>105,'name'=>'Manoj S',    'country'=>'—', 'trades'=>117,'winRate'=>71, 'profitPct'=>770.88, 'profit'=>770878.12, 'pair'=>'XAUUSD','avgWin'=>9280, 'avgLoss'=>4220,'avgHold'=>55, 'avgRR'=>2.20],
        ['rank'=>6, 'uid'=>106,'name'=>'Alex T',     'country'=>'US','trades'=>94, 'winRate'=>63, 'profitPct'=>621.45, 'profit'=>621450.00, 'pair'=>'USDJPY','avgWin'=>10490,'avgLoss'=>5520,'avgHold'=>75, 'avgRR'=>1.90],
        ['rank'=>7, 'uid'=>107,'name'=>'Chen W',     'country'=>'CN','trades'=>143,'winRate'=>69, 'profitPct'=>543.20, 'profit'=>543200.00, 'pair'=>'EURUSD','avgWin'=>5510, 'avgLoss'=>2760,'avgHold'=>40, 'avgRR'=>2.00],
        ['rank'=>8, 'uid'=>108,'name'=>'Sofia M',    'country'=>'ES','trades'=>112,'winRate'=>58, 'profitPct'=>489.30, 'profit'=>489300.00, 'pair'=>'GBPUSD','avgWin'=>7530, 'avgLoss'=>3270,'avgHold'=>120,'avgRR'=>2.30],
        ['rank'=>9, 'uid'=>109,'name'=>'Yuki T',     'country'=>'JP','trades'=>87, 'winRate'=>72, 'profitPct'=>412.50, 'profit'=>412500.00, 'pair'=>'USDJPY','avgWin'=>6590, 'avgLoss'=>2540,'avgHold'=>35, 'avgRR'=>2.60],
        ['rank'=>10,'uid'=>110,'name'=>'Lucas B',    'country'=>'BR','trades'=>201,'winRate'=>55, 'profitPct'=>387.60, 'profit'=>387600.00, 'pair'=>'EURUSD','avgWin'=>3510, 'avgLoss'=>2060,'avgHold'=>85, 'avgRR'=>1.70],
        ['rank'=>11,'uid'=>111,'name'=>'Emma K',     'country'=>'DE','trades'=>76, 'winRate'=>68, 'profitPct'=>312.40, 'profit'=>312400.00, 'pair'=>'NAS100','avgWin'=>6050, 'avgLoss'=>2520,'avgHold'=>50, 'avgRR'=>2.40],
        ['rank'=>12,'uid'=>0,  'name'=>'You',        'country'=>'FR','trades'=>45, 'winRate'=>62, 'profitPct'=>198.75, 'profit'=>198750.00, 'pair'=>'XAUUSD','avgWin'=>7120, 'avgLoss'=>3650,'avgHold'=>70, 'avgRR'=>1.95,'me'=>true],
        ['rank'=>13,'uid'=>112,'name'=>'Omar A',     'country'=>'AE','trades'=>63, 'winRate'=>49, 'profitPct'=>167.30, 'profit'=>167300.00, 'pair'=>'GBPUSD','avgWin'=>5420, 'avgLoss'=>2460,'avgHold'=>110,'avgRR'=>2.20],
        ['rank'=>14,'uid'=>113,'name'=>'Ivan P',     'country'=>'RU','trades'=>98, 'winRate'=>54, 'profitPct'=>143.20, 'profit'=>143200.00, 'pair'=>'EURUSD','avgWin'=>2710, 'avgLoss'=>1510,'avgHold'=>95, 'avgRR'=>1.80],
        ['rank'=>15,'uid'=>114,'name'=>'Fatima Z',   'country'=>'MA','trades'=>55, 'winRate'=>65, 'profitPct'=>121.80, 'profit'=>121800.00, 'pair'=>'XAUUSD','avgWin'=>3410, 'avgLoss'=>1620,'avgHold'=>60, 'avgRR'=>2.10],
    ],
    2 => [],
    101 => [
        ['rank'=>1, 'uid'=>115,'name'=>'Pierre D',   'country'=>'FR','trades'=>156,'winRate'=>78, 'profitPct'=>2341.20,'profit'=>2341200.00,'pair'=>'XAUUSD','avgWin'=>19240,'avgLoss'=>8020,'avgHold'=>55, 'avgRR'=>2.40],
        ['rank'=>2, 'uid'=>116,'name'=>'Ankit S',    'country'=>'IN','trades'=>189,'winRate'=>82, 'profitPct'=>1987.40,'profit'=>1987400.00,'pair'=>'EURUSD','avgWin'=>12820,'avgLoss'=>4750,'avgHold'=>40, 'avgRR'=>2.70],
        ['rank'=>3, 'uid'=>117,'name'=>'Kim J',      'country'=>'KR','trades'=>134,'winRate'=>71, 'profitPct'=>1654.30,'profit'=>1654300.00,'pair'=>'NAS100','avgWin'=>17390,'avgLoss'=>7900,'avgHold'=>50, 'avgRR'=>2.20],
        ['rank'=>4, 'uid'=>118,'name'=>'Liu W',      'country'=>'CN','trades'=>167,'winRate'=>66, 'profitPct'=>1234.50,'profit'=>1234500.00,'pair'=>'USDJPY','avgWin'=>11200,'avgLoss'=>5890,'avgHold'=>70, 'avgRR'=>1.90],
        ['rank'=>5, 'uid'=>119,'name'=>'Maria G',    'country'=>'MX','trades'=>142,'winRate'=>73, 'profitPct'=>987.60, 'profit'=>987600.00, 'pair'=>'EURUSD','avgWin'=>9530, 'avgLoss'=>4140,'avgHold'=>45, 'avgRR'=>2.30],
        ['rank'=>6, 'uid'=>120,'name'=>'David N',    'country'=>'NG','trades'=>118,'winRate'=>69, 'profitPct'=>812.30, 'profit'=>812300.00, 'pair'=>'XAUUSD','avgWin'=>9980, 'avgLoss'=>4990,'avgHold'=>60, 'avgRR'=>2.00],
        ['rank'=>7, 'uid'=>121,'name'=>'Sarah M',    'country'=>'GB','trades'=>95, 'winRate'=>77, 'profitPct'=>698.40, 'profit'=>698400.00, 'pair'=>'GBPUSD','avgWin'=>9550, 'avgLoss'=>3410,'avgHold'=>35, 'avgRR'=>2.80],
        ['rank'=>8, 'uid'=>0,  'name'=>'You',        'country'=>'FR','trades'=>67, 'winRate'=>59, 'profitPct'=>534.20, 'profit'=>534200.00, 'pair'=>'EURUSD','avgWin'=>13510,'avgLoss'=>7510,'avgHold'=>80, 'avgRR'=>1.80,'me'=>true],
        ['rank'=>9, 'uid'=>122,'name'=>'Hassan A',   'country'=>'SA','trades'=>89, 'winRate'=>61, 'profitPct'=>456.70, 'profit'=>456700.00, 'pair'=>'USDJPY','avgWin'=>8410, 'avgLoss'=>4010,'avgHold'=>90, 'avgRR'=>2.10],
        ['rank'=>10,'uid'=>123,'name'=>'Priya R',    'country'=>'IN','trades'=>112,'winRate'=>74, 'profitPct'=>389.10, 'profit'=>389100.00, 'pair'=>'XAUUSD','avgWin'=>4700, 'avgLoss'=>1880,'avgHold'=>42, 'avgRR'=>2.50],
    ],
    102 => [
        ['rank'=>1, 'uid'=>124,'name'=>'Kenji T',    'country'=>'JP','trades'=>178,'winRate'=>81, 'profitPct'=>2156.70,'profit'=>2156700.00,'pair'=>'XAUUSD','avgWin'=>14960,'avgLoss'=>5750,'avgHold'=>38, 'avgRR'=>2.60],
        ['rank'=>2, 'uid'=>125,'name'=>'Amara D',    'country'=>'SN','trades'=>145,'winRate'=>76, 'profitPct'=>1876.40,'profit'=>1876400.00,'pair'=>'EURUSD','avgWin'=>17030,'avgLoss'=>7400,'avgHold'=>55, 'avgRR'=>2.30],
        ['rank'=>3, 'uid'=>126,'name'=>'Carlos V',   'country'=>'CO','trades'=>198,'winRate'=>68, 'profitPct'=>1543.20,'profit'=>1543200.00,'pair'=>'GBPUSD','avgWin'=>11460,'avgLoss'=>6030,'avgHold'=>65, 'avgRR'=>1.90],
        ['rank'=>4, 'uid'=>127,'name'=>'Nadia B',    'country'=>'TN','trades'=>134,'winRate'=>72, 'profitPct'=>1234.80,'profit'=>1234800.00,'pair'=>'NAS100','avgWin'=>12800,'avgLoss'=>6100,'avgHold'=>75, 'avgRR'=>2.10],
        ['rank'=>5, 'uid'=>128,'name'=>'Ryan C',     'country'=>'AU','trades'=>167,'winRate'=>65, 'profitPct'=>987.30, 'profit'=>987300.00, 'pair'=>'USDJPY','avgWin'=>9100, 'avgLoss'=>5060,'avgHold'=>85, 'avgRR'=>1.80],
    ],
    103 => [
        ['rank'=>1, 'uid'=>129,'name'=>'Ming L',     'country'=>'CN','trades'=>201,'winRate'=>79, 'profitPct'=>2345.60,'profit'=>2345600.00,'pair'=>'XAUUSD','avgWin'=>14770,'avgLoss'=>5910,'avgHold'=>42, 'avgRR'=>2.50],
        ['rank'=>2, 'uid'=>130,'name'=>'Aisha M',    'country'=>'NG','trades'=>167,'winRate'=>83, 'profitPct'=>1987.20,'profit'=>1987200.00,'pair'=>'NAS100','avgWin'=>14340,'avgLoss'=>4630,'avgHold'=>30, 'avgRR'=>3.10],
        ['rank'=>3, 'uid'=>131,'name'=>'Thomas B',   'country'=>'FR','trades'=>189,'winRate'=>71, 'profitPct'=>1654.80,'profit'=>1654800.00,'pair'=>'EURUSD','avgWin'=>12330,'avgLoss'=>6170,'avgHold'=>55, 'avgRR'=>2.00],
    ],
    201 => [
        ['rank'=>1, 'uid'=>132,'name'=>'Viktor S',   'country'=>'UA','trades'=>123,'winRate'=>84, 'profitPct'=>987.40, 'profit'=>987400.00, 'pair'=>'XAUUSD','avgWin'=>9560, 'avgLoss'=>2990,'avgHold'=>28, 'avgRR'=>3.20],
        ['rank'=>2, 'uid'=>133,'name'=>'Jun K',       'country'=>'KR','trades'=>145,'winRate'=>78, 'profitPct'=>756.30, 'profit'=>756300.00, 'pair'=>'NAS100','avgWin'=>6690, 'avgLoss'=>2480,'avgHold'=>40, 'avgRR'=>2.70],
        ['rank'=>3, 'uid'=>134,'name'=>'Lena M',     'country'=>'DE','trades'=>98, 'winRate'=>71, 'profitPct'=>534.20, 'profit'=>534200.00, 'pair'=>'EURUSD','avgWin'=>7680, 'avgLoss'=>3660,'avgHold'=>60, 'avgRR'=>2.10],
    ],
    202 => [
        ['rank'=>1, 'uid'=>135,'name'=>'Andre F',    'country'=>'BR','trades'=>112,'winRate'=>82, 'profitPct'=>876.50, 'profit'=>876500.00, 'pair'=>'XAUUSD','avgWin'=>9540, 'avgLoss'=>3290,'avgHold'=>32, 'avgRR'=>2.90],
        ['rank'=>2, 'uid'=>136,'name'=>'Mei X',      'country'=>'CN','trades'=>98, 'winRate'=>77, 'profitPct'=>654.30, 'profit'=>654300.00, 'pair'=>'EURUSD','avgWin'=>8670, 'avgLoss'=>3470,'avgHold'=>45, 'avgRR'=>2.50],
        ['rank'=>3, 'uid'=>137,'name'=>'James O',    'country'=>'GH','trades'=>87, 'winRate'=>69, 'profitPct'=>432.10, 'profit'=>432100.00, 'pair'=>'GBPUSD','avgWin'=>7200, 'avgLoss'=>3600,'avgHold'=>65, 'avgRR'=>2.00],
    ],
];

// ── Account index map: challenge id → sequential position (1 = first ever created) ──
$_chTotal     = count($challenges);
$acctIndexMap = [];
foreach ($challenges as $_idx => $_ch) {
    $acctIndexMap[(int)$_ch['id']] = $_chTotal - $_idx;
}

// ── Summary bar counts (used in overview + challenges tabs) ──
$sumEvalCount = 0;  $sumEvalAlloc = 0.0;  $sumEvalProfit = 0.0;
$sumFundedCount = 0; $sumFundedAlloc = 0.0; $sumFundedProfit = 0.0;
foreach ($challenges as $_c) {
    if (in_array($_c['status'], ['active', 'passed'])) {
        $sumEvalCount++;
        $sumEvalAlloc  += (float)$_c['account_size'];
        $sumEvalProfit += (float)$_c['total_profit'];
    } elseif ($_c['status'] === 'funded') {
        $sumFundedCount++;
        $sumFundedAlloc  += (float)$_c['account_size'];
        $sumFundedProfit += (float)$_c['total_profit'];
    }
}
$sumEvalPct   = $sumEvalAlloc   > 0 ? ($sumEvalProfit   / $sumEvalAlloc)   * 100 : 0;
$sumFundedPct = $sumFundedAlloc > 0 ? ($sumFundedProfit / $sumFundedAlloc) * 100 : 0;

// ── Stats cards (overview tab) ──

// 1. Bias — long vs short across eval + funded
$biasLong = 0; $biasShort = 0;
$biasLongWins = 0; $biasShortWins = 0;
foreach ($challenges as $_c) {
    if (in_array($_c['status'], ['active', 'passed', 'funded'])) {
        $biasLong      += (int)($_c['long_trades']          ?? 0);
        $biasShort     += (int)($_c['short_trades']         ?? 0);
        $biasLongWins  += (int)($_c['long_winning_trades']  ?? 0);
        $biasShortWins += (int)($_c['short_winning_trades'] ?? 0);
    }
}
$biasLongWR  = $biasLong  > 0 ? round($biasLongWins  / $biasLong  * 100) : null;
$biasShortWR = $biasShort > 0 ? round($biasShortWins / $biasShort * 100) : null;
$biasTotal    = $biasLong + $biasShort;
$biasLongPct  = $biasTotal > 0 ? round($biasLong  / $biasTotal * 100) : 0;
$biasShortPct = $biasTotal > 0 ? 100 - $biasLongPct : 0;
$biasDir      = $biasTotal === 0 ? 'N/A'
              : ($biasLong > $biasShort ? 'LONG BIAS'
              : ($biasShort > $biasLong ? 'SHORT BIAS' : 'NEUTRAL'));
// SVG semicircle gauge arc endpoint (cx=50, cy=60, r=46)
// angle 180°=left(short), 90°=top(neutral), 0°=right(long)
$_biasPctCl   = $biasTotal > 0 ? max(1, min(99, $biasLongPct)) : 50;
$_biasAngle   = deg2rad(180.0 - $_biasPctCl * 1.8);
$biasArcEndX  = round(50 + 46 * cos($_biasAngle), 2);
$biasArcEndY  = round(60 - 46 * sin($_biasAngle), 2);
$biasArcColor = $biasTotal === 0 ? '#333333'
              : ($biasLongPct > 52 ? '#10B981'
              : ($biasLongPct < 48 ? '#D71921' : '#999999'));

// 2. Daily P&L — average per trading day per category
$dpEvalProfit = 0.0; $dpEvalDays = 0;
$dpFundedProfit = 0.0; $dpFundedDays = 0;
foreach ($challenges as $_c) {
    $days = max(1, (int)($_c['trading_days'] ?? 1));
    if (in_array($_c['status'], ['active', 'passed'])) {
        $dpEvalProfit += (float)$_c['total_profit'];
        $dpEvalDays   += $days;
    } elseif ($_c['status'] === 'funded') {
        $dpFundedProfit += (float)$_c['total_profit'];
        $dpFundedDays   += $days;
    }
}
$dpEvalDaily   = $dpEvalDays   > 0 ? $dpEvalProfit   / $dpEvalDays   : null;
$dpFundedDaily = $dpFundedDays > 0 ? $dpFundedProfit / $dpFundedDays : null;

// 3. Grade — based on total payout amount
$gradePayoutCount = (int)($overview['total_payouts']        ?? 0);
$gradePayoutTotal = (float)($overview['total_payout_amount'] ?? 0);
$gradeHighest = 0.0;
foreach (($payouts ?? []) as $_p) {
    if ($_p['status'] === 'completed') {
        $gradeHighest = max($gradeHighest, (float)$_p['amount']);
    }
}
// Moody's-style investment grade scale based on total payouts
// BBB and above = Investment Grade threshold
// ── Payout tier system — overview Grade card + Lifetime Payout certificate ──
if      ($gradePayoutTotal === 0.0)    { $gradeLetter = '—';        $gradeLabel = 'UNRANKED';    $gradeColor = 'var(--text-dis)'; $gradeTarget = 1000;    $gradeIsIG = false; }
elseif  ($gradePayoutTotal < 1000)     { $gradeLetter = 'BRONZE';   $gradeLabel = 'RISING';      $gradeColor = '#CD7F32';         $gradeTarget = 1000;    $gradeIsIG = false; }
elseif  ($gradePayoutTotal < 5000)     { $gradeLetter = 'SILVER';   $gradeLabel = 'CONSISTENT';  $gradeColor = '#94A3B8';         $gradeTarget = 5000;    $gradeIsIG = false; }
elseif  ($gradePayoutTotal < 25000)    { $gradeLetter = 'GOLD';     $gradeLabel = 'ESTABLISHED'; $gradeColor = '#FBBF24';         $gradeTarget = 25000;   $gradeIsIG = false; }
elseif  ($gradePayoutTotal < 100000)   { $gradeLetter = 'PLATINUM'; $gradeLabel = 'ADVANCED';    $gradeColor = '#E2E8F0';         $gradeTarget = 100000;  $gradeIsIG = true;  }
elseif  ($gradePayoutTotal < 500000)   { $gradeLetter = 'DIAMOND';  $gradeLabel = 'ELITE';       $gradeColor = '#67E8F9';         $gradeTarget = 500000;  $gradeIsIG = true;  }
elseif  ($gradePayoutTotal < 1000000)  { $gradeLetter = 'MASTER';   $gradeLabel = 'PINNACLE';    $gradeColor = '#A78BFA';         $gradeTarget = 1000000; $gradeIsIG = true;  }
else                                   { $gradeLetter = 'LEGEND';   $gradeLabel = 'LEGENDARY';   $gradeColor = '#F97316';         $gradeTarget = 0;       $gradeIsIG = true;  }
$gradeProg      = $gradeTarget > 0 && $gradePayoutTotal > 0 ? min(100, ($gradePayoutTotal / $gradeTarget) * 100) : ($gradeTarget === 0 && $gradePayoutTotal > 0 ? 100 : 0);
$gradeLetterLen = strlen($gradeLetter);

// ── Moody's credit notation — Statistics → Trading DNA card ──
if      ($gradePayoutTotal === 0.0)    { $moodysLetter = 'NR';  $moodysLabel = 'NOT RATED';    $moodysColor = 'var(--text-dis)'; $moodysIsIG = false; }
elseif  ($gradePayoutTotal < 1000)     { $moodysLetter = 'B';   $moodysLabel = 'SPECULATIVE';  $moodysColor = '#888888';         $moodysIsIG = false; }
elseif  ($gradePayoutTotal < 5000)     { $moodysLetter = 'BB';  $moodysLabel = 'UPPER SPEC.';  $moodysColor = 'var(--warning)';  $moodysIsIG = false; }
elseif  ($gradePayoutTotal < 25000)    { $moodysLetter = 'BBB'; $moodysLabel = 'INV. GRADE';   $moodysColor = 'var(--info)';     $moodysIsIG = true;  }
elseif  ($gradePayoutTotal < 100000)   { $moodysLetter = 'A';   $moodysLabel = 'UPPER MEDIUM'; $moodysColor = 'var(--success)';  $moodysIsIG = true;  }
elseif  ($gradePayoutTotal < 500000)   { $moodysLetter = 'AA';  $moodysLabel = 'HIGH GRADE';   $moodysColor = '#10E8A8';         $moodysIsIG = true;  }
else                                   { $moodysLetter = 'AAA'; $moodysLabel = 'PRIME';         $moodysColor = 'var(--warning)';  $moodysIsIG = true;  }
$moodysLetterLen = strlen($moodysLetter);
$_moodysScoreMap = ['NR'=>0.0,'B'=>1.5,'BB'=>3.0,'BBB'=>5.0,'A'=>6.5,'AA'=>8.0,'AAA'=>10.0];
$moodysScore     = $_moodysScoreMap[$moodysLetter] ?? 0.0;
$moodysScoreSegs = (int)round($moodysScore);

// 4. Doji Wallet balance + last movements
$walletBalance   = (float)($profile['wallet_balance'] ?? 0);
$walletMovements = getWalletMovements($userId, 3);

// 5. Doji Coins + today earned
$dojiCoins      = (int)($overview['doji_coins'] ?? 0);
if ($dojiCoins === 0) $dojiCoins = $topbar_coins;
$recentCoinsDays = getRecentCoinsDays($userId, 3);

// 6. Trading session distribution
$sesNY = 0; $sesLondon = 0; $sesAsia = 0;
foreach ($challenges as $_c) {
    if (in_array($_c['status'], ['active', 'passed', 'funded'])) {
        $sesNY     += (int)($_c['session_ny']     ?? 0);
        $sesLondon += (int)($_c['session_london'] ?? 0);
        $sesAsia   += (int)($_c['session_asia']   ?? 0);
    }
}
$sesTotal     = $sesNY + $sesLondon + $sesAsia;
$sesNYPct     = $sesTotal > 0 ? round($sesNY     / $sesTotal * 100) : 0;
$sesLondonPct = $sesTotal > 0 ? round($sesLondon / $sesTotal * 100) : 0;
$sesAsiaPct   = $sesTotal > 0 ? round($sesAsia   / $sesTotal * 100) : 0;

// ── Trading credentials data (shared between overview + challenges tabs) ──
$credData    = [];
$credFirstId = 0;
foreach ($challenges as $ch) {
    $cid    = (int)$ch['id'];
    $login  = $ch['account_login'] ?? $ch['mt_login'] ?? '—';
    if (empty($login)) $login = '—';
    $server = $ch['account_server'] ?? $ch['mt_server'] ?? '';
    if (empty($server)) {
        $server = in_array($ch['status'], ['funded']) ? 'DXTrade-Live-EU1' : 'DXTrade-Demo-US1';
    }
    $rawPwd = $ch['account_password'] ?? $ch['mt_password'] ?? null;
    $hasPwd = !empty($rawPwd) && in_array($ch['status'], ['active', 'funded']);
    $masterPwd   = $hasPwd ? $rawPwd : null;
    $investorPwd = $hasPwd ? 'Inv@' . str_pad($cid, 4, '0', STR_PAD_LEFT) . 'Rd' : null;
    $kBalance  = (float)$ch['current_balance'];
    $kProfit   = (float)$ch['total_profit'];
    $kSize     = (float)$ch['account_size'] ?: 1;
    $kPeak     = max((float)$ch['peak_balance'], $kBalance);
    $kTarget   = (float)$ch['profit_target_1'];
    $kTarget2  = (float)($ch['profit_target_2'] ?? 0);
    $kDailyLoss = (float)$ch['daily_loss'];
    $kMaxLoss   = (float)$ch['max_loss'];
    $kPnlPct   = ($kProfit / $kSize) * 100;
    $kProfProg = $kTarget > 0 ? min(100, max(0, ($kPnlPct / $kTarget) * 100)) : 0;
    $kDdRaw    = max(0, (($kPeak - $kBalance) / $kSize) * 100);
    $kDdUsed   = min($kDdRaw, $kDailyLoss);
    $kDdProg   = $kDailyLoss > 0 ? min(100, max(0, ($kDdUsed / $kDailyLoss) * 100)) : 0;
    $kMdUsed   = $kDdRaw;
    $kMdProg   = $kMaxLoss > 0 ? min(100, max(0, ($kMdUsed / $kMaxLoss) * 100)) : 0;
    $kBestTrade = (float)($ch['best_trade'] ?? 0);
    $kConsRule  = max(1, (float)($ch['consistency_rule'] ?? 30));
    $kIsEval    = in_array($ch['status'], ['active', 'passed']);
    $kIsFunded  = $ch['status'] === 'funded';
    // Consistency rule applies to Funded accounts only
    $kConsUsed  = ($kIsFunded && $kProfit > 0 && $kBestTrade > 0) ? ($kBestTrade / $kProfit) * 100 : 0;
    $kConsPct   = ($kIsFunded && $kConsUsed > 0) ? ($kConsUsed / $kConsRule) * 100 : 0;
    $kPhase     = (int)$ch['phase'];
    $kTypeLbl   = $kIsFunded
        ? 'FUNDED ACCOUNT'
        : (($ch['type'] === 'one_step' ? '1-STEP' : '2-STEP')
           . ($kPhase > 1 ? ' · PHASE ' . $kPhase : '') . ' EVAL');
    $kAcctRef        = challengeAcctRef($ch['type'], $ch['account_size'], $userId, $acctIndexMap[$cid] ?? 1);
    $kTradingDays    = (int)($ch['trading_days']     ?? 0);
    $kMinTradingDays = (int)($ch['min_trading_days'] ?? 0);
    $kPayoutEligible = $kIsFunded && $kProfit > 0
        && ($kMinTradingDays === 0 || $kTradingDays >= $kMinTradingDays)
        && $kConsPct < 100;
    $credData[$cid] = [
        'id'                => $cid,
        'label'             => ($ch['type'] === 'one_step' ? '1-Step' : '2-Step')
                               . ' · ' . formatMoneyShort($ch['account_size']),
        'status'            => $ch['status'],
        'login'             => $login,
        'master_password'   => $masterPwd,
        'investor_password' => $investorPwd,
        'server'            => $server,
        'balance'           => $kBalance,
        'profit'            => $kProfit,
        'account_size'      => $kSize,
        'profit_target'     => $kTarget,
        'profit_target_2'   => $kTarget2,
        'daily_loss'        => $kDailyLoss,
        'max_loss'          => $kMaxLoss,
        'pnl_pct'           => round($kPnlPct, 2),
        'prof_prog'         => round($kProfProg, 2),
        'dd_used'           => round($kDdUsed, 2),
        'dd_prog'           => round($kDdProg, 2),
        'md_used'           => round($kMdUsed, 2),
        'md_prog'           => round($kMdProg, 2),
        'daily_loss_type'   => $ch['daily_loss_type'] ?? 'intraday',
        'max_loss_type'     => $ch['max_loss_type']   ?? 'intraday',
        'best_trade'        => $kBestTrade,
        'cons_rule'         => $kConsRule,
        'cons_used'         => round($kConsUsed, 1),
        'cons_pct'          => round($kConsPct, 1),
        'is_eval'           => $kIsEval,
        'is_funded'         => $kIsFunded,
        'type_label'        => $kTypeLbl,
        'ch_id_fmt'         => $kAcctRef,
        'acct_ref'          => $kAcctRef,
        'payout_eligible'   => $kPayoutEligible,
    ];
    if ($credFirstId === 0) $credFirstId = $cid;
}
?>

<div class="dash">

    <!-- ═══════════════ SIDEBAR ═══════════════ -->
    <aside class="dash-sidebar">

        <!-- Logo -->
        <div class="dash-sidebar-logo">
            <a href="index.php" class="dash-logo-link">
                <img src="assets/img/doji white.svg" alt="Doji" class="dash-logo-img dash-logo-dark" onerror="this.style.display='none'">
                <img src="assets/img/doji black.svg" alt="Doji" class="dash-logo-img dash-logo-light" onerror="this.style.display='none'">
                <span class="dash-logo-brand">DOJI <span class="green">FUNDING</span></span>
            </a>
        </div>

        <!-- Nav -->
        <nav class="dash-nav">
            <button class="dash-nav-item active" data-tab="overview">
                <?= pix('grid', 16, 16, 'dash-nav-icon') ?>
                <span>Dashboard</span>
            </button>
            <button class="dash-nav-item" data-tab="challenges">
                <?= pix('layers', 16, 16, 'dash-nav-icon') ?>
                <span>Challenges</span>
                <?php if (($overview['active_challenges'] ?? 0) > 0): ?>
                <span class="nav-status-badge nav-status-badge--pending"><span class="nav-status-badge-dot"></span><?= $overview['active_challenges'] ?></span>
                <?php endif; ?>
            </button>
            <button class="dash-nav-item" data-tab="configurator">
                <?= pix('gear', 16, 16, 'dash-nav-icon') ?>
                <span>Configurator</span>
            </button>
            <button class="dash-nav-item" data-tab="wallet">
                <?= pix('wallet', 16, 16, 'dash-nav-icon') ?>
                <span>WALLET</span>
            </button>
            <button class="dash-nav-item" data-tab="payouts">
                <?= pix('dollar', 16, 16, 'dash-nav-icon') ?>
                <span>Payouts</span>
                <?php if ($notifPayoutAction > 0): ?>
                <span class="nav-status-badge nav-status-badge--urgent"><span class="nav-status-badge-dot"></span><?= $notifPayoutAction ?></span>
                <?php elseif ($notifPayoutPending > 0): ?>
                <span class="nav-status-badge nav-status-badge--pending"><span class="nav-status-badge-dot"></span><?= $notifPayoutPending ?></span>
                <?php endif; ?>
            </button>

            <button class="dash-nav-item" data-tab="statistics">
                <?= pix('bar-chart', 16, 16, 'dash-nav-icon') ?>
                <span>Statistics</span>
            </button>
            <button class="dash-nav-item" data-tab="competitions">
                <?= pix('trophy', 16, 16, 'dash-nav-icon') ?>
                <span>Competitions</span>
            </button>
            <button class="dash-nav-item" data-tab="leaderboard">
                <?= pix('columns', 16, 16, 'dash-nav-icon') ?>
                <span>Leaderboard</span>
            </button>
            <button class="dash-nav-item" data-tab="certificates">
                <?= pix('medal', 16, 16, 'dash-nav-icon') ?>
                <span>Certificates</span>
            </button>
            <button class="dash-nav-item" data-tab="calendar">
                <?= pix('calendar', 16, 16, 'dash-nav-icon') ?>
                <span>Calendar</span>
            </button>
            <button class="dash-nav-item" data-tab="affiliate">
                <?= pix('share', 16, 16, 'dash-nav-icon') ?>
                <span>Affiliate</span>
            </button>
            <button class="dash-nav-item" data-tab="testimonials">
                <?= pix('star', 16, 16, 'dash-nav-icon') ?>
                <span>Testimonials</span>
            </button>
            <button class="dash-nav-item" data-tab="support">
                <?= pix('question', 16, 16, 'dash-nav-icon') ?>
                <span>Support</span>
            </button>

            <div class="dash-nav-group" id="navGroupProfile">
                <button class="dash-nav-item" data-tab="settings" id="navProfile">
                    <?= pix('user', 16, 16, 'dash-nav-icon') ?>
                    <span>Profile</span>
                    <?= pix('chevron-down', 13, 13, 'dash-nav-chevron') ?>
                </button>
                <div class="dash-nav-sub" id="navSubProfile">
                    <a class="dash-nav-sub-item" data-section="profile" href="#">Profile</a>
                    <a class="dash-nav-sub-item" data-section="verification" href="#">Account Verification</a>
                    <a class="dash-nav-sub-item" data-section="security" href="#">Security</a>
                    <a class="dash-nav-sub-item" data-section="bank" href="#">Bank Accounts</a>
                    <a class="dash-nav-sub-item" data-section="cards" href="#">Credit Cards</a>
                    <a class="dash-nav-sub-item" data-section="crypto" href="#">Crypto Wallets</a>
                    <a class="dash-nav-sub-item" data-section="payments" href="#">Payment History</a>
                    <a class="dash-nav-sub-item" data-section="discord" href="#">Discord</a>
                    <a class="dash-nav-sub-item" data-section="suggestions" href="#">Feature Suggestions</a>
                    <a class="dash-nav-sub-item" data-section="preferences" href="#">Preferences</a>
                </div>
            </div>
        </nav>

        <!-- Sidebar footer -->
        <div class="dash-sidebar-foot">
            <button class="dash-foot-theme-row" id="dashThemeSwitch" onclick="Dashboard.toggleTheme()" title="Toggle theme">
                <span class="dash-foot-row-label">THEME</span>
                <div class="dash-foot-row-right">
                    <span class="dash-foot-theme-txt" id="dashThemeLabel">DARK</span>
                    <svg id="dashThemeIcon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"></svg>
                </div>
            </button>
            <a href="https://discord.gg/kNUqAqCppU" target="_blank" rel="noopener noreferrer" class="dash-discord-btn" title="Join Doji Funding Discord">
                <span>JOIN DISCORD</span>
                <svg viewBox="0 0 24 24" fill="currentColor" width="13" height="13"><path d="M20.317 4.492c-1.53-.69-3.17-1.2-4.885-1.49a.075.075 0 00-.079.036c-.21.369-.444.85-.608 1.23a18.566 18.566 0 00-5.487 0 12.36 12.36 0 00-.617-1.23A.077.077 0 008.562 3c-1.714.29-3.354.8-4.885 1.491a.07.07 0 00-.032.027C.533 9.093-.32 13.555.099 17.961a.08.08 0 00.031.055 20.03 20.03 0 005.993 2.98.078.078 0 00.084-.026 13.83 13.83 0 001.226-1.963.074.074 0 00-.041-.104 13.201 13.201 0 01-1.872-.878.075.075 0 01-.008-.125c.126-.093.252-.19.372-.287a.075.075 0 01.078-.01c3.927 1.764 8.18 1.764 12.061 0a.075.075 0 01.079.009c.12.098.245.195.372.288a.075.075 0 01-.006.125c-.598.344-1.22.635-1.873.877a.075.075 0 00-.041.105c.36.687.772 1.341 1.225 1.962a.077.077 0 00.084.028 19.963 19.963 0 006.002-2.981.076.076 0 00.032-.054c.5-5.094-.838-9.52-3.549-13.442a.06.06 0 00-.031-.028zM8.02 15.278c-1.182 0-2.157-1.069-2.157-2.38 0-1.312.956-2.38 2.157-2.38 1.21 0 2.176 1.077 2.157 2.38 0 1.312-.956 2.38-2.157 2.38zm7.975 0c-1.183 0-2.157-1.069-2.157-2.38 0-1.312.955-2.38 2.157-2.38 1.21 0 2.176 1.077 2.157 2.38 0 1.312-.946 2.38-2.157 2.38z"/></svg>
            </a>
            <a href="index.php" class="dash-back-link">
                <span class="dash-back-txt">dojifunding.com</span>
                <img src="assets/img/doji white.svg?v=<?= ASSET_VERSION ?>" alt="Doji" class="dash-foot-logo dash-logo-dark">
                <img src="assets/img/doji black.svg?v=<?= ASSET_VERSION ?>" alt="Doji" class="dash-foot-logo dash-logo-light">
            </a>
        </div>

    </aside>

    <!-- ═══════════════ MAIN WRAP ═══════════════ -->
    <div class="dash-main-wrap">

        <!-- ─── TOPBAR ─── -->
        <header class="dash-topbar">

            <!-- LEFT — user + actions -->
            <div class="dash-topbar-left">
                <button class="dash-topbar-hamburger" id="dashHamburger" aria-label="Menu">
                    <?= pix('menu', 16, 16) ?>
                </button>
                <?= pix('user', 14, 14, '') ?>
                <span class="dash-topbar-username"><?= htmlspecialchars(strtolower($user['first_name'] . $user['last_name'])) ?></span>
                <span id="dashFirstName" style="display:none"><?= htmlspecialchars($profile['first_name'] ?? $user['first_name'] ?? '') ?></span>
                <span id="dashUsername" style="display:none"><?= htmlspecialchars($profile['username'] ?? '') ?></span>
                <button class="dash-topbar-new-challenge" onclick="Dashboard.switchTab('configurator')">
                    <?= pix('plus', 11, 11) ?>
                    New Challenge
                </button>
                <!-- icon-button group -->
                <div class="dash-topbar-actions">
                    <!-- Notifications -->
                    <button class="dash-topbar-icon-btn" onclick="Dashboard.switchTab('payouts')" title="Notifications" aria-label="Notifications">
                        <?= pix('bell', 15, 15) ?>
                        <?php if ($notifTotal > 0): ?>
                        <span class="dash-topbar-badge <?= $notifPayoutAction > 0 ? 'dash-topbar-badge-urgent' : '' ?>"><?= $notifTotal ?></span>
                        <?php endif; ?>
                    </button>
                    <!-- Language -->
                    <button class="dash-topbar-icon-btn dash-topbar-lang-btn" id="dashLangBtn" title="Switch language" aria-label="Switch language">
                        <?= pix('globe', 13, 13) ?>
                        <span class="dash-topbar-lang-txt" id="dashLangTxt">EN</span>
                    </button>
                    <!-- Support -->
                    <button class="dash-topbar-icon-btn" onclick="Dashboard.switchTab('support')" title="Support" aria-label="Support">
                        <?= pix('question', 15, 15) ?>
                    </button>
                    <!-- Logout -->
                    <button class="dash-topbar-icon-btn" onclick="AuthModal.logout()" title="Log out" aria-label="Log out">
                        <?= pix('logout', 15, 15) ?>
                    </button>
                </div>
            </div>

            <!-- RIGHT — allocation widget + market session cards -->
            <div class="dash-topbar-right">

                <!-- Allocation + Doji Coins widgets -->
                <div class="dash-topbar-widgets">
                    <div class="dash-topbar-capital">
                        <div class="dash-capital-label">TOTAL CAPITAL</div>
                        <div class="dash-capital-val"><?= formatMoney($topbar_capital) ?></div>
                        <div class="dash-capital-row">
                            <span class="dash-capital-pnl <?= $topbar_pnl >= 0 ? 'pos' : 'neg' ?>">
                                <?= $topbar_pnl >= 0 ? '+' : '' ?><?= formatMoney($topbar_pnl) ?>
                            </span>
                            <span class="dash-capital-pct <?= $topbar_perf_pct >= 0 ? 'pos' : 'neg' ?>">
                                <?= $topbar_perf_pct >= 0 ? '+' : '' ?><?= number_format($topbar_perf_pct, 2) ?>%
                            </span>
                        </div>
                    </div>
                    <div class="dash-topbar-coins">
                        <div class="dash-coins-label">DOJI COINS</div>
                        <div class="dash-coins-val"><?= number_format($dojiCoins) ?></div>
                        <div class="dash-coins-sub">BALANCE</div>
                    </div>
                    <div class="dash-topbar-wallet" onclick="Dashboard.switchTab('wallet')" title="Go to Wallet">
                        <div class="dash-wallet-label">WALLET</div>
                        <div class="dash-wallet-val"><?= formatMoney($walletBalance) ?></div>
                        <div class="dash-wallet-sub">AVAILABLE</div>
                    </div>
                </div>

                <div class="dash-sessions" id="dashSessions">

                    <div class="dash-sc" id="sc-sydney" data-local-open="8" data-local-close="17" data-zone="Australia/Sydney">
                        <div class="dash-sc-top">
                            <span class="dash-sc-dot"></span>
                            <span class="dash-sc-city">Sydney</span>
                            <span class="dash-sc-status">
                                <span class="dash-sc-state-dot"></span>
                                <span class="dash-sc-state-txt"></span>
                            </span>
                        </div>
                        <div class="dash-sc-time"></div>
                        <div class="dash-sc-timelabel">Local Time</div>
                        <div class="dash-sc-countdown"></div>
                        <div class="dash-sc-hours"></div>
                    </div>

                    <div class="dash-sc" id="sc-tokyo" data-local-open="9" data-local-close="18" data-zone="Asia/Tokyo">
                        <div class="dash-sc-top">
                            <span class="dash-sc-dot"></span>
                            <span class="dash-sc-city">Tokyo</span>
                            <span class="dash-sc-status">
                                <span class="dash-sc-state-dot"></span>
                                <span class="dash-sc-state-txt"></span>
                            </span>
                        </div>
                        <div class="dash-sc-time"></div>
                        <div class="dash-sc-timelabel">Local Time</div>
                        <div class="dash-sc-countdown"></div>
                        <div class="dash-sc-hours"></div>
                    </div>

                    <div class="dash-sc" id="sc-london" data-local-open="8" data-local-close="17" data-zone="Europe/London">
                        <div class="dash-sc-top">
                            <span class="dash-sc-dot"></span>
                            <span class="dash-sc-city">London</span>
                            <span class="dash-sc-status">
                                <span class="dash-sc-state-dot"></span>
                                <span class="dash-sc-state-txt"></span>
                            </span>
                        </div>
                        <div class="dash-sc-time"></div>
                        <div class="dash-sc-timelabel">Local Time</div>
                        <div class="dash-sc-countdown"></div>
                        <div class="dash-sc-hours"></div>
                    </div>

                    <div class="dash-sc" id="sc-newyork" data-local-open="8" data-local-close="17" data-zone="America/New_York">
                        <div class="dash-sc-top">
                            <span class="dash-sc-dot"></span>
                            <span class="dash-sc-city">New York</span>
                            <span class="dash-sc-status">
                                <span class="dash-sc-state-dot"></span>
                                <span class="dash-sc-state-txt"></span>
                            </span>
                        </div>
                        <div class="dash-sc-time"></div>
                        <div class="dash-sc-timelabel">Local Time</div>
                        <div class="dash-sc-countdown"></div>
                        <div class="dash-sc-hours"></div>
                    </div>

                    <div class="dash-sc dash-sc-local" id="sc-local">
                        <div class="dash-sc-top">
                            <span class="dash-sc-dot"></span>
                            <span class="dash-sc-city" id="scLocalCity">My Time</span>
                        </div>
                        <div class="dash-sc-time" id="scLocalTime">—</div>
                        <div class="dash-sc-timelabel">Your Time Zone</div>
                        <div class="dash-sc-tz-name" id="scLocalTz"></div>
                        <div class="dash-sc-hours" id="scLocalTzLabel" style="cursor:pointer;color:var(--accent);opacity:0.7" onclick="Dashboard.switchTab('settings')">Set in Profile ›</div>
                    </div>

                </div>
            </div>

        </header>

        <!-- Market session clock script -->
        <script>
        (function() {
            /* ── Country → IANA timezone map ── */
            var COUNTRY_TZ = {
                'AF':'Asia/Kabul','AL':'Europe/Tirane','DZ':'Africa/Algiers','AR':'America/Argentina/Buenos_Aires',
                'AU':'Australia/Sydney','AT':'Europe/Vienna','BE':'Europe/Brussels','BR':'America/Sao_Paulo',
                'BG':'Europe/Sofia','CA':'America/Toronto','CL':'America/Santiago','CN':'Asia/Shanghai',
                'CO':'America/Bogota','HR':'Europe/Zagreb','CZ':'Europe/Prague','DK':'Europe/Copenhagen',
                'EG':'Africa/Cairo','FI':'Europe/Helsinki','FR':'Europe/Paris','DE':'Europe/Berlin',
                'GH':'Africa/Accra','GR':'Europe/Athens','HK':'Asia/Hong_Kong','HU':'Europe/Budapest',
                'IN':'Asia/Kolkata','ID':'Asia/Jakarta','IE':'Europe/Dublin','IL':'Asia/Jerusalem',
                'IT':'Europe/Rome','JP':'Asia/Tokyo','JO':'Asia/Amman','KE':'Africa/Nairobi',
                'KW':'Asia/Kuwait','LB':'Asia/Beirut','MY':'Asia/Kuala_Lumpur','MX':'America/Mexico_City',
                'MA':'Africa/Casablanca','NL':'Europe/Amsterdam','NZ':'Pacific/Auckland','NG':'Africa/Lagos',
                'NO':'Europe/Oslo','PK':'Asia/Karachi','PE':'America/Lima','PH':'Asia/Manila',
                'PL':'Europe/Warsaw','PT':'Europe/Lisbon','QA':'Asia/Qatar','RO':'Europe/Bucharest',
                'RU':'Europe/Moscow','SA':'Asia/Riyadh','SG':'Asia/Singapore','ZA':'Africa/Johannesburg',
                'KR':'Asia/Seoul','ES':'Europe/Madrid','SE':'Europe/Stockholm','CH':'Europe/Zurich',
                'TW':'Asia/Taipei','TH':'Asia/Bangkok','TN':'Africa/Tunis','TR':'Europe/Istanbul',
                'UA':'Europe/Kiev','AE':'Asia/Dubai','GB':'Europe/London','US':'America/New_York',
                'VN':'Asia/Ho_Chi_Minh','VE':'America/Caracas','PK':'Asia/Karachi','BD':'Asia/Dhaka',
                'LK':'Asia/Colombo','NP':'Asia/Kathmandu','MM':'Asia/Rangoon','KZ':'Asia/Almaty',
                'UZ':'Asia/Tashkent','YE':'Asia/Aden','IQ':'Asia/Baghdad','IR':'Asia/Tehran',
                'BH':'Asia/Bahrain','OM':'Asia/Muscat','AO':'Africa/Luanda','CI':'Africa/Abidjan',
                'TZ':'Africa/Dar_es_Salaam','UG':'Africa/Kampala','ZM':'Africa/Lusaka','ZW':'Africa/Harare',
                'LY':'Africa/Tripoli','SD':'Africa/Khartoum','ET':'Africa/Addis_Ababa',
                'FJ':'Pacific/Fiji','GU':'Pacific/Guam','HI':'Pacific/Honolulu'
            };

            /* ── Read/write user timezone from localStorage ── */
            function getUserTz() {
                return localStorage.getItem('doji_tz') || Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC';
            }

            function setUserTz(tz) {
                localStorage.setItem('doji_tz', tz);
                updateLocalCard();
            }

            /* ── Auto-set timezone from country code (2-letter ISO) ── */
            window.DojiTz = {
                setFromCountry: function(code) {
                    var tz = COUNTRY_TZ[code.toUpperCase()];
                    if (tz) { setUserTz(tz); return tz; }
                    return null;
                },
                set: setUserTz,
                get: getUserTz
            };

            /* ── Pre-fill timezone select if on profile page ── */
            function prefillTzSelect() {
                var sel = document.getElementById('profileTimezone');
                if (!sel) return;
                var saved = getUserTz();
                for (var i = 0; i < sel.options.length; i++) {
                    if (sel.options[i].value === saved) { sel.selectedIndex = i; break; }
                }
                sel.addEventListener('change', function() {
                    if (this.value) setUserTz(this.value);
                });
            }
            prefillTzSelect();

            /* ── Local card update ── */
            function updateLocalCard() {
                var tz    = getUserTz();
                var label = tz.split('/').pop().replace(/_/g,' ');
                var city  = document.getElementById('scLocalCity');
                var tzEl  = document.getElementById('scLocalTz');
                var hint  = document.getElementById('scLocalTzLabel');
                if (city) city.textContent = label;
                if (tzEl) tzEl.textContent = tz;
                if (hint) {
                    var hasTz = !!localStorage.getItem('doji_tz');
                    hint.textContent = hasTz ? tz : 'Set in Profile ›';
                    hint.style.color = hasTz ? 'var(--text-dis)' : 'var(--accent)';
                }
            }
            updateLocalCard();

            /* ── Clock helpers ── */
            function fmtCountdown(secs) {
                var h = Math.floor(secs/3600), m = Math.floor((secs%3600)/60);
                return (h > 0 ? h+'h ' : '') + m+'m';
            }

            /* ── Weekend-aware open/close detection ──
               Forex week: Sunday 22:00 UTC (Sydney opens) → Friday 22:00 UTC (NY closes)
               Cross-midnight sessions (Sydney): open=22 > close=7
               TODO: extend with holiday calendar API once broker feed is chosen */
            function isSessionOpen(now, openUTC, closeUTC) {
                var d    = now.getUTCDay(); // 0=Sun … 6=Sat
                var cur  = now.getUTCHours() * 60 + now.getUTCMinutes();
                var o    = openUTC * 60, c = closeUTC * 60;
                var xmid = o > c; // cross-midnight (e.g. Sydney 22–07)

                if (d === 6) return false; // Saturday: always closed
                // Sunday: only cross-midnight sessions open (Sydney at 22:00 UTC)
                if (d === 0) return xmid && cur >= o;
                // Friday: cross-midnight sessions do NOT reopen at 22:00 (would run into Saturday)
                if (d === 5 && xmid && cur >= o) return false;
                return xmid ? (cur >= o || cur < c) : (cur >= o && cur < c);
            }

            /* Seconds until the session's next valid open (skips weekend days) */
            function secsToNextOpen(targetHour, crossMidnight) {
                var now = new Date();
                var candidate = new Date(Date.UTC(
                    now.getUTCFullYear(), now.getUTCMonth(), now.getUTCDate(), targetHour, 0, 0, 0
                ));
                if (candidate <= now) candidate.setUTCDate(candidate.getUTCDate() + 1);
                for (var i = 0; i < 8; i++) {
                    var day = candidate.getUTCDay();
                    // Cross-midnight (Sydney): opens Mon–Thu evenings + Sunday 22:00; not Friday
                    // Regular sessions: opens Mon–Fri
                    var ok = crossMidnight
                        ? (day === 0 || (day >= 1 && day <= 4))
                        : (day >= 1 && day <= 5);
                    if (ok) return Math.round((candidate - now) / 1000);
                    candidate.setUTCDate(candidate.getUTCDate() + 1);
                }
                return 86400;
            }

            /* Seconds until close (session is open, always same or next calendar day) */
            function secsToClose(targetUTCHour) {
                var now    = new Date();
                var target = new Date(now);
                target.setUTCHours(targetUTCHour, 0, 0, 0);
                var diff = Math.round((target - now) / 1000);
                if (diff <= 0) diff += 86400;
                return diff;
            }

            function fmt(now, zone) {
                return new Intl.DateTimeFormat('en-GB', {
                    timeZone: zone, hour:'2-digit', minute:'2-digit', second:'2-digit', hour12:false
                }).format(now);
            }

            /* ── DST-aware: local hour → UTC hour for a given timezone ── */
            function localHourToUTC(localHour, zone) {
                var now = new Date();
                // Get current UTC offset: format the same instant in zone vs UTC and diff
                var fmtH = function(d, tz) {
                    return parseInt(new Intl.DateTimeFormat('en-GB', {
                        timeZone: tz, hour: '2-digit', minute: '2-digit', hour12: false
                    }).format(d).split(':')[0]);
                };
                var localNow = fmtH(now, zone);
                var utcNow   = fmtH(now, 'UTC');
                var offset   = localNow - utcNow;
                if (offset > 12)  offset -= 24;
                if (offset < -12) offset += 24;
                return ((localHour - offset) % 24 + 24) % 24;
            }

            /* ── Format hour as "HH:00" ── */
            function fmtHour(h) { return String(h).padStart(2,'0') + ':00'; }

            /* ── Init: compute DST-correct UTC hours + render local labels ── */
            function initSessionCards() {
                document.querySelectorAll('.dash-sc:not(.dash-sc-local)').forEach(function(card) {
                    var zone      = card.dataset.zone;
                    var localOpen = parseInt(card.dataset.localOpen);
                    var localClose= parseInt(card.dataset.localClose);
                    // Compute DST-correct UTC hours and store for tick()
                    card.dataset.utcOpen  = localHourToUTC(localOpen, zone);
                    card.dataset.utcClose = localHourToUTC(localClose, zone);
                    // Render local opening hours label
                    var hoursEl = card.querySelector('.dash-sc-hours');
                    if (hoursEl) hoursEl.textContent = fmtHour(localOpen) + ' – ' + fmtHour(localClose) + ' local';
                });
            }
            initSessionCards();

            /* ── Main tick ── */
            function tick() {
                var now  = new Date();
                var utcH = now.getUTCHours(), utcM = now.getUTCMinutes();

                /* Market session cards */
                document.querySelectorAll('.dash-sc:not(.dash-sc-local)').forEach(function(card) {
                    var zone   = card.dataset.zone;
                    var openH  = parseInt(card.dataset.utcOpen);
                    var closeH = parseInt(card.dataset.utcClose);
                    var xmid   = openH > closeH;
                    var open   = isSessionOpen(now, openH, closeH);

                    card.querySelector('.dash-sc-time').textContent = fmt(now, zone);

                    var stateTxt  = card.querySelector('.dash-sc-state-txt');
                    var stateDot  = card.querySelector('.dash-sc-state-dot');
                    var countdown = card.querySelector('.dash-sc-countdown');

                    if (open) {
                        card.classList.add('open'); card.classList.remove('closed');
                        stateDot.className = 'dash-sc-state-dot open';
                        stateTxt.textContent = 'OPEN';
                        stateTxt.className   = 'dash-sc-state-txt';
                        countdown.textContent = 'Closes in ' + fmtCountdown(secsToClose(closeH));
                    } else {
                        card.classList.remove('open'); card.classList.add('closed');
                        stateDot.className = 'dash-sc-state-dot closed';
                        stateTxt.textContent = 'CLOSED';
                        stateTxt.className   = 'dash-sc-state-txt closed';
                        countdown.textContent = 'Opens in ' + fmtCountdown(secsToNextOpen(openH, xmid));
                    }
                });

                /* Local card */
                var localTimeEl = document.getElementById('scLocalTime');
                if (localTimeEl) {
                    try { localTimeEl.textContent = fmt(now, getUserTz()); }
                    catch(e) { localTimeEl.textContent = '—'; }
                }
            }

            tick();
            setInterval(tick, 1000);
        })();
        </script>

        <!-- Auto-map country → timezone on signup form -->
        <script>
        (function() {
            /* Hook into signup country select (if modal exists) */
            function hookSignup() {
                var sel = document.getElementById('signupCountry');
                if (!sel) return;
                sel.addEventListener('change', function() {
                    if (this.value && window.DojiTz) {
                        var tz = window.DojiTz.setFromCountry(this.value);
                        /* Also pre-fill profile timezone select if open */
                        var pSel = document.getElementById('profileTimezone');
                        if (pSel && tz) {
                            for (var i=0; i<pSel.options.length; i++) {
                                if (pSel.options[i].value === tz) { pSel.selectedIndex=i; break; }
                            }
                        }
                    }
                });
            }
            /* Wait for modal to be in DOM */
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', hookSignup);
            } else {
                hookSignup();
            }
        })();
        </script>

        <script>
        (function () {
            var sidebar  = document.querySelector('.dash-sidebar');
            var mainWrap = document.querySelector('.dash-main-wrap');
            var KEY      = 'doji_sidebar_collapsed';

            function applyState(collapsed) {
                if (!sidebar || !mainWrap) return;
                sidebar.classList.toggle('nav-collapsed', collapsed);
                mainWrap.classList.toggle('nav-collapsed', collapsed);
            }

            /* Apply immediately — sidebar + mainWrap already in DOM at this point */
            applyState(localStorage.getItem(KEY) === '1');

            /* Button is below this script in DOM — wait for DOMContentLoaded */
            document.addEventListener('DOMContentLoaded', function () {
                var btn = document.getElementById('sidebarToggle');
                if (!btn) return;
                btn.addEventListener('click', function () {
                    var collapsed = !sidebar.classList.contains('nav-collapsed');
                    applyState(collapsed);
                    localStorage.setItem(KEY, collapsed ? '1' : '0');
                });
            });
        })();
        </script>

        <main class="dash-main" id="main-content">

            <div class="dash-page-head">
                <button class="dash-sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                    <svg class="sidebar-toggle-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"/>
                    </svg>
                </button>
                <h1 class="dash-page-title" id="dashPageTitle">DASHBOARD</h1>
            </div>

            <?php if ($kycStatus === 'rejected'): ?>
            <div class="dash-kyc-banner rejected">
                <span class="dash-kyc-banner-icon"><?= pix('info', 18, 18) ?></span>
                <div class="dash-kyc-banner-text">
                    <strong>Verification Rejected</strong>
                    Your identity document was not accepted. Please resubmit with a valid government-issued ID.
                </div>
                <button class="dash-kyc-banner-btn" onclick="Dashboard.switchTab('settings'); Dashboard.showProfileSection('verification')">Resubmit →</button>
            </div>
            <?php else: ?>
            <div class="dash-kyc-banner">
                <span class="dash-kyc-banner-icon"><?= pix('shield', 18, 18) ?></span>
                <div class="dash-kyc-banner-text">
                    <strong>Identity Verification Required</strong>
                    Verify your identity to unlock payouts and funded accounts.
                </div>
                <button class="dash-kyc-banner-btn" onclick="Dashboard.switchTab('settings'); Dashboard.showProfileSection('verification')">Verify Now →</button>
            </div>
            <?php endif; ?>

            <?php if (!($profile['is_public'] ?? 0)): ?>
            <div class="dash-kyc-banner dash-kyc-banner--lb" id="dashPublicBanner" style="display:none">
                <span class="dash-kyc-banner-icon"><?= pix('trophy', 18, 18) ?></span>
                <div class="dash-kyc-banner-text">
                    <strong>You're not on the leaderboard yet</strong>
                    Enable your public profile to compete for tier ranks, earn badges, and show off your stats.
                </div>
                <button class="dash-kyc-banner-btn" onclick="Dashboard.switchTab('settings'); Dashboard.showProfileSection('public')">Enable Public Profile &rarr;</button>
            </div>
            <?php endif; ?>

            <!-- ══ TAB: OVERVIEW ══ -->
            <div class="dash-tab active" id="tab-overview">

                <?php if ($showOnbChecklist): ?>
                <div class="onb-checklist" id="onbChecklist">
                    <div class="onb-cl-header">
                        <div>
                            <div class="onb-cl-title">GUIDE DE DÉMARRAGE</div>
                            <div class="onb-cl-sub"><?= $onbCompletedCount ?> / 5 ÉTAPES COMPLÉTÉES</div>
                        </div>
                        <button class="onb-cl-dismiss" onclick="Onboarding.dismissChecklist()">MASQUER ×</button>
                    </div>

                    <div class="onb-cl-progress">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                        <div class="onb-cl-seg<?= $i < $onbCompletedCount ? ' done' : '' ?>"></div>
                        <?php endfor; ?>
                    </div>

                    <div class="onb-cl-steps">
                        <div class="onb-cl-step<?= $onbSteps['welcome'] ? ' done' : '' ?>">
                            <div class="onb-cl-check"><span class="onb-cl-check-tick">✓</span></div>
                            <span class="onb-cl-name">BIENVENUE SUR DOJI FUNDING</span>
                            <span class="onb-cl-arrow">→</span>
                        </div>
                        <div class="onb-cl-step<?= $onbSteps['profile'] ? ' done' : '' ?>"
                             <?= !$onbSteps['profile'] ? "onclick=\"Dashboard.switchTab('settings'); Dashboard.showProfileSection('profile')\"" : '' ?>>
                            <div class="onb-cl-check"><span class="onb-cl-check-tick">✓</span></div>
                            <span class="onb-cl-name">PROFIL COMPLÉTÉ</span>
                            <span class="onb-cl-arrow">→</span>
                        </div>
                        <div class="onb-cl-step<?= $onbSteps['kyc'] ? ' done' : '' ?>"
                             <?= !$onbSteps['kyc'] ? "onclick=\"Dashboard.switchTab('settings'); Dashboard.showProfileSection('verification')\"" : '' ?>>
                            <div class="onb-cl-check"><span class="onb-cl-check-tick">✓</span></div>
                            <span class="onb-cl-name">IDENTITÉ VÉRIFIÉE (KYC)</span>
                            <span class="onb-cl-arrow">→</span>
                        </div>
                        <div class="onb-cl-step<?= $onbSteps['challenge'] ? ' done' : '' ?>"
                             <?= !$onbSteps['challenge'] ? "onclick=\"Dashboard.switchTab('configurator')\"" : '' ?>>
                            <div class="onb-cl-check"><span class="onb-cl-check-tick">✓</span></div>
                            <span class="onb-cl-name">PREMIER CHALLENGE LANCÉ</span>
                            <span class="onb-cl-arrow">→</span>
                        </div>
                        <div class="onb-cl-step<?= $onbSteps['discord'] ? ' done' : '' ?>"
                             <?= !$onbSteps['discord'] ? "onclick=\"window.open('https://discord.gg/kNUqAqCppU','_blank')\"" : '' ?>>
                            <div class="onb-cl-check"><span class="onb-cl-check-tick">✓</span></div>
                            <span class="onb-cl-name">DISCORD REJOINT</span>
                            <span class="onb-cl-arrow">→</span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php
                // ── Overview data from first active challenge ──
                $firstCh    = $overview['active_list'][0] ?? null;
                $ovBalance  = $firstCh ? (float)$firstCh['current_balance'] : 0;
                $ovProfit   = $firstCh ? (float)$firstCh['total_profit']    : 0;
                $ovSize     = $firstCh && $firstCh['account_size'] > 0 ? (float)$firstCh['account_size'] : 1;
                $ovPnlPct   = ($ovProfit / $ovSize) * 100;
                $ovTarget   = $firstCh ? (float)$firstCh['profit_target_1'] : 10;
                $ovProfProg = $ovTarget > 0 ? min(100, max(0, ($ovPnlPct / $ovTarget) * 100)) : 0;
                $ovPeak     = $firstCh && $firstCh['peak_balance'] > 0 ? (float)$firstCh['peak_balance'] : $ovBalance;
                $ovDdRaw    = max(0, (($ovPeak - $ovBalance) / $ovSize) * 100); // brut depuis le peak
                $ovDdMax    = $firstCh ? (float)$firstCh['daily_loss'] : 5;
                $ovDdUsed   = min($ovDdRaw, $ovDdMax);  // soft: jamais > limite daily
                $ovDdProg   = $ovDdMax > 0 ? min(100, max(0, ($ovDdUsed / $ovDdMax) * 100)) : 0;
                $ovMdMax    = $firstCh ? (float)$firstCh['max_loss'] : 10;
                $ovMdUsed   = $ovDdRaw;                 // hard: valeur brute non capée
                $ovMdProg   = $ovMdMax > 0 ? min(100, max(0, ($ovMdUsed / $ovMdMax) * 100)) : 0;
                $ovChId     = $firstCh ? (int)$firstCh['id'] : 0;
                $ovAcctRef  = ($firstCh && $ovChId)
                    ? challengeAcctRef($firstCh['type'], $firstCh['account_size'], $userId, $acctIndexMap[$ovChId] ?? 1)
                    : '—';
                $ovType     = $firstCh ? ($firstCh['type'] === 'one_step' ? '1-STEP' : '2-STEP') : '—';
                $ovPhase    = $firstCh && $firstCh['phase'] > 1 ? ' · PHASE ' . $firstCh['phase'] : '';
                $ovStatus   = $firstCh ? $firstCh['status'] : '';
                $ovIsEval   = in_array($ovStatus, ['active', 'passed']);
                $ovIsFunded = $ovStatus === 'funded';
                $ovTypeLabel  = $ovIsFunded ? 'FUNDED ACCOUNT' : ($ovType . $ovPhase . ' EVAL');
                $ovBestTrade  = $firstCh ? (float)($firstCh['best_trade'] ?? 0) : 0;
                $ovConsRule   = $firstCh ? max(1, (float)($firstCh['consistency_rule'] ?? 30)) : 30;
                $ovDdType     = $firstCh['daily_loss_type'] ?? 'intraday';
                $ovMdType     = $firstCh['max_loss_type']   ?? 'intraday';
                $ovConsUsed   = ($ovProfit > 0 && $ovBestTrade > 0) ? ($ovBestTrade / $ovProfit) * 100 : 0;
                $ovConsPct    = $ovConsUsed > 0 ? ($ovConsUsed / $ovConsRule) * 100 : 0;
                $cgFill  = $ovConsPct > 0 ? min(160.22 * 1.04, 160.22 * $ovConsPct / 100) : 0;
                $cgColor = $ovConsPct >= 100 ? '#D71921' : ($ovConsPct >= 80 ? '#e86820' : ($ovConsPct >= 60 ? '#D4A843' : ($ovConsPct > 0 ? '#10B981' : '#333')));
                $cgLbl   = $ovConsPct >= 100 ? 'TOO HIGH' : ($ovConsPct >= 80 ? 'WARNING' : ($ovConsPct > 0 ? 'OK' : 'N/A'));
                $cgDisp  = $ovConsPct > 0 ? round($ovConsUsed) : '—';
                ?>

                <?php if (!empty($challenges)): ?>
                <!-- ── SUMMARY BAR (overview tab) ── -->
                <div class="dash-tab-actions">
                    <div class="ch-summary">
                        <div class="ch-sum-item">
                            <span class="ch-sum-lbl">EVAL</span>
                            <span class="ch-sum-val"><?= $sumEvalCount ?></span>
                        </div>
                        <span class="ch-sum-sep">·</span>
                        <div class="ch-sum-item">
                            <span class="ch-sum-lbl">FUNDED</span>
                            <span class="ch-sum-val"><?= $sumFundedCount ?></span>
                        </div>
                        <?php if ($sumEvalAlloc > 0): ?>
                        <span class="ch-sum-sep">|</span>
                        <div class="ch-sum-item">
                            <span class="ch-sum-lbl">EVAL CAP</span>
                            <span class="ch-sum-val"><?= formatMoneyShort($sumEvalAlloc) ?></span>
                            <span class="ch-sum-perf <?= $sumEvalProfit >= 0 ? 'green' : 'red' ?>">
                                <?= ($sumEvalProfit >= 0 ? '+' : '') . formatMoney($sumEvalProfit) ?>
                                <span class="ch-sum-pct">(<?= ($sumEvalProfit >= 0 ? '+' : '') . number_format($sumEvalPct, 1) ?>%)</span>
                            </span>
                        </div>
                        <?php endif; ?>
                        <?php if ($sumFundedAlloc > 0): ?>
                        <span class="ch-sum-sep">|</span>
                        <div class="ch-sum-item">
                            <span class="ch-sum-lbl">FUNDED CAP</span>
                            <span class="ch-sum-val"><?= formatMoneyShort($sumFundedAlloc) ?></span>
                            <span class="ch-sum-perf <?= $sumFundedProfit >= 0 ? 'green' : 'red' ?>">
                                <?= ($sumFundedProfit >= 0 ? '+' : '') . formatMoney($sumFundedProfit) ?>
                                <span class="ch-sum-pct">(<?= ($sumFundedProfit >= 0 ? '+' : '') . number_format($sumFundedPct, 1) ?>%)</span>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <a href="challenges.php" class="dash-action-btn">+ New Challenge</a>
                </div>

                <!-- ── CREDENTIALS CARD (overview tab) ── -->
                <div class="cred-card" id="ovCredCard">

                    <div class="cred-header">
                        <span class="cred-card-lbl">TRADING CREDENTIALS</span>
                        <!-- Mobile: dropdown selector -->
                        <select class="cred-mobile-select cred-mobile-sel" id="ovCredMobileSelect"
                                onchange="ChallengeCredentials.select(parseInt(this.value))">
                            <?php foreach ($challenges as $ch): ?>
                            <option value="<?= (int)$ch['id'] ?>">
                                <?= htmlspecialchars(
                                    ($ch['type'] === 'one_step' ? '1-Step' : '2-Step')
                                    . ' · ' . formatMoneyShort($ch['account_size'])
                                    . ' · #' . str_pad($ch['id'], 5, '0', STR_PAD_LEFT)
                                ) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Desktop: account pills -->
                    <div class="cred-accounts" id="ovCredAccounts">
                        <?php foreach ($challenges as $i => $ch): ?>
                        <button class="cred-pill<?= $i === 0 ? ' active' : '' ?>"
                                data-cred-id="<?= (int)$ch['id'] ?>"
                                onclick="ChallengeCredentials.select(<?= (int)$ch['id'] ?>)">
                            <span class="cred-pill-dot <?= htmlspecialchars($ch['status']) ?>"></span>
                            <?= $ch['type'] === 'one_step' ? '1-Step' : '2-Step' ?>
                            <span class="cred-pill-size"><?= formatMoneyShort($ch['account_size']) ?></span>
                            <?php if ((int)$ch['phase'] > 1): ?>
                            <span class="cred-pill-phase">P<?= (int)$ch['phase'] ?></span>
                            <?php endif; ?>
                        </button>
                        <?php endforeach; ?>
                    </div>

                    <!-- Credentials fields -->
                    <div class="cred-grid">

                        <!-- LOGIN -->
                        <div class="cred-field">
                            <div class="cred-lbl">Login</div>
                            <div class="cred-row">
                                <span class="cred-val" data-cred-field="credLogin">—</span>
                                <button class="cred-btn" onclick="ChallengeCredentials.copy('login', this)" title="Copy login">
                                    <?= pix('copy', 11, 11) ?>
                                </button>
                            </div>
                        </div>

                        <!-- MASTER PASSWORD -->
                        <div class="cred-field">
                            <div class="cred-lbl">Master Password</div>
                            <div class="cred-row">
                                <span class="cred-val cred-val-pass" data-cred-field="credMasterPass">—</span>
                                <button class="cred-btn cred-master-eye-btn" id="ovCredMasterToggle"
                                        onclick="ChallengeCredentials.toggleMaster()" title="Show / hide">
                                    <?= pix('eye', 11, 11) ?>
                                </button>
                                <button class="cred-btn" onclick="ChallengeCredentials.copy('master_password', this)" title="Copy">
                                    <?= pix('copy', 11, 11) ?>
                                </button>
                                <button class="cred-btn cred-btn-reset"
                                        onclick="ChallengeCredentials.resetMaster()" title="Reset password">
                                    <?= pix('refresh', 11, 11) ?>
                                </button>
                            </div>
                        </div>

                        <!-- INVESTOR PASSWORD -->
                        <div class="cred-field">
                            <div class="cred-lbl">Investor Password</div>
                            <div class="cred-row">
                                <span class="cred-val" data-cred-field="credInvestorPass">—</span>
                                <button class="cred-btn" onclick="ChallengeCredentials.copy('investor_password', this)" title="Copy">
                                    <?= pix('copy', 11, 11) ?>
                                </button>
                            </div>
                        </div>

                        <!-- SERVER -->
                        <div class="cred-field">
                            <div class="cred-lbl">Server</div>
                            <div class="cred-row">
                                <span class="cred-val" data-cred-field="credServer">—</span>
                                <button class="cred-btn" onclick="ChallengeCredentials.copy('server', this)" title="Copy">
                                    <?= pix('copy', 11, 11) ?>
                                </button>
                            </div>
                        </div>

                    </div><!-- /.cred-grid -->
                </div><!-- /.cred-card (overview) -->
                <?php endif; ?>

                <!-- ── 6 STATS CARDS ── -->
                <?php if ($overview['total_challenges'] > 0): ?>
                <div class="stat-grid">

                    <!-- 1. BIAS -->
                    <div class="stat-card">
                        <div class="stat-card-lbl">BIAS</div>
                        <?php if ($biasTotal > 0): ?>
                        <div class="bias-db">
                            <div class="bias-db-head">
                                <span class="bias-db-dir" style="color:<?= $biasArcColor ?>"><?= $biasDir ?></span>
                                <span class="bias-db-split"><?= $biasShortPct ?>%&nbsp;&nbsp;·&nbsp;&nbsp;<?= $biasLongPct ?>%</span>
                            </div>
                            <?php
                                $_bSegs      = 20;
                                $_bShortSegs = (int)round($_bSegs * $biasShortPct / 100);
                            ?>
                            <div class="bias-db-bar">
                                <?php for ($s = 0; $s < $_bSegs; $s++):
                                    $cls = $s < $_bShortSegs ? 'bias-db-seg short' : 'bias-db-seg long';
                                    if ($s === 9)  $cls .= ' center-l';
                                    if ($s === 10) $cls .= ' center-r';
                                ?>
                                <div class="<?= $cls ?>"></div>
                                <?php endfor; ?>
                            </div>
                            <div class="bias-db-foot">
                                <div class="bias-db-side">
                                    <div class="stat-bias-count-grid">
                                        <span class="stat-bias-count-n" style="color:#D71921"><?= number_format($biasShort) ?></span>
                                        <?php if ($biasShortWR !== null): ?>
                                        <span class="stat-bias-count-wr" style="color:#D71921"><?= $biasShortWR ?>%</span>
                                        <?php else: ?><span></span><?php endif; ?>
                                        <span class="stat-bias-count-lbl">SHORT</span>
                                        <?php if ($biasShortWR !== null): ?>
                                        <span class="stat-bias-count-wr-lbl">WIN RATE</span>
                                        <?php else: ?><span></span><?php endif; ?>
                                    </div>
                                </div>
                                <div class="bias-db-side bias-db-side-r">
                                    <div class="stat-bias-count-grid bias-db-grid-r">
                                        <?php if ($biasLongWR !== null): ?>
                                        <span class="stat-bias-count-wr" style="color:#10B981"><?= $biasLongWR ?>%</span>
                                        <?php else: ?><span></span><?php endif; ?>
                                        <span class="stat-bias-count-n" style="color:#10B981"><?= number_format($biasLong) ?></span>
                                        <?php if ($biasLongWR !== null): ?>
                                        <span class="stat-bias-count-wr-lbl">WIN RATE</span>
                                        <?php else: ?><span></span><?php endif; ?>
                                        <span class="stat-bias-count-lbl">LONG</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="stat-no-data">NO DATA YET</div>
                        <div class="bias-db-bar bias-db-bar-empty">
                            <?php for ($s = 0; $s < 20; $s++): ?>
                            <div class="bias-db-seg <?= $s < 10 ? 'short' : 'long' ?><?= $s === 9 ? ' center-l' : ($s === 10 ? ' center-r' : '') ?>"></div>
                            <?php endfor; ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- 2. DAILY P&L -->
                    <div class="stat-card">
                        <div class="stat-card-lbl">DAILY P&L</div>
                        <div class="stat-daily-rows">
                            <div class="stat-daily-row">
                                <span class="stat-daily-cat">EVAL</span>
                                <?php if ($dpEvalDaily !== null): ?>
                                <span class="stat-daily-val <?= $dpEvalDaily >= 0 ? 'green' : 'red' ?>">
                                    <?= ($dpEvalDaily >= 0 ? '+' : '') . formatMoney($dpEvalDaily) ?>/day
                                </span>
                                <?php else: ?>
                                <span class="stat-daily-val na">—</span>
                                <?php endif; ?>
                            </div>
                            <div class="stat-daily-row">
                                <span class="stat-daily-cat">FUNDED</span>
                                <?php if ($dpFundedDaily !== null): ?>
                                <span class="stat-daily-val <?= $dpFundedDaily >= 0 ? 'green' : 'red' ?>">
                                    <?= ($dpFundedDaily >= 0 ? '+' : '') . formatMoney($dpFundedDaily) ?>/day
                                </span>
                                <?php else: ?>
                                <span class="stat-daily-val na">—</span>
                                <?php endif; ?>
                            </div>
                            <div class="stat-daily-row">
                                <span class="stat-daily-cat">COMPETITION</span>
                                <span class="stat-daily-val na">—</span>
                            </div>
                        </div>
                    </div>

                    <!-- 3. GRADE -->
                    <div class="stat-card">
                        <div class="stat-card-lbl">GRADE</div>
                        <div class="stat-grade-body">
                            <div class="stat-grade-letter<?= $gradeLetterLen >= 8 ? ' sz-tier-lg' : ($gradeLetterLen >= 4 ? ' sz-tier' : ($gradeLetterLen === 3 ? ' sz3' : ($gradeLetterLen === 2 ? ' sz2' : ''))) ?>" style="color:<?= $gradeColor ?>"><?= $gradeLetter ?></div>
                            <div class="stat-grade-meta">
                                <div class="stat-grade-label-row">
                                    <span class="stat-grade-label" style="color:<?= $gradeColor ?>"><?= $gradeLabel ?></span>
                                    <?php if ($gradeIsIG): ?>
                                    <span class="stat-grade-ig-badge">ELITE</span>
                                    <?php endif; ?>
                                </div>
                                <div class="stat-grade-kpis">
                                    <div class="stat-grade-kpi">
                                        <span class="stat-grade-kpi-lbl">PAYOUTS</span>
                                        <span class="stat-grade-kpi-val"><?= $gradePayoutCount ?></span>
                                    </div>
                                    <div class="stat-grade-kpi">
                                        <span class="stat-grade-kpi-lbl">TOTAL</span>
                                        <span class="stat-grade-kpi-val"><?= formatMoneyShort($gradePayoutTotal) ?></span>
                                    </div>
                                    <div class="stat-grade-kpi">
                                        <span class="stat-grade-kpi-lbl">HIGHEST</span>
                                        <span class="stat-grade-kpi-val"><?= $gradeHighest > 0 ? formatMoneyShort($gradeHighest) : '—' ?></span>
                                    </div>
                                </div>
                                <?php if ($gradeTarget > 0): ?>
                                <?php $gradeSegs = 16; $gradeSegsOn = (int)round($gradeProg / 100 * $gradeSegs); ?>
                                <div class="seg-bar">
                                    <?php for ($__i = 0; $__i < $gradeSegs; $__i++): ?>
                                    <div class="seg-bar-cell<?= $__i < $gradeSegsOn ? ' filled' : '' ?>"
                                         <?= $__i < $gradeSegsOn ? 'style="background:' . $gradeColor . '"' : '' ?>></div>
                                    <?php endfor; ?>
                                </div>
                                <div class="stat-grade-prog-lbl"><?= round($gradeProg) ?>% TO NEXT TIER</div>
                                <?php endif; ?>
                            </div>
                            <!-- Arc gauge — progress to next tier (270° sweep, r=30) -->
                            <?php
                            $_gFill = $gradeProg > 0
                                ? round(min(141.37, 141.37 * $gradeProg / 100), 2)
                                : 0;
                            ?>
                            <div class="stat-grade-gauge-wrap">
                                <svg viewBox="0 0 80 80" class="stat-grade-gauge" aria-hidden="true">
                                    <circle class="grade-gauge-track"
                                            cx="40" cy="40" r="30"
                                            fill="none" stroke-width="7"
                                            stroke-dasharray="141.37 188.50"
                                            transform="rotate(135 40 40)"/>
                                    <circle class="grade-gauge-fill"
                                            cx="40" cy="40" r="30"
                                            fill="none" stroke="<?= $gradeColor ?>" stroke-width="7"
                                            stroke-linecap="round"
                                            stroke-dasharray="<?= $_gFill ?> 188.50"
                                            transform="rotate(135 40 40)"/>
                                    <text x="40" y="37" text-anchor="middle" class="grade-gauge-pct"><?= round($gradeProg) ?></text>
                                    <text x="40" y="50" text-anchor="middle" class="grade-gauge-sub">%</text>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- 4. DOJI WALLET -->
                    <div class="stat-card">
                        <div class="stat-card-lbl">DOJI WALLET</div>
                        <div class="stat-card-split">
                            <div class="stat-card-split-l">
                                <div class="stat-wallet-bal"><?= formatMoney($walletBalance) ?></div>
                                <div class="stat-wallet-sub">AVAILABLE BALANCE</div>
                                <div class="stat-wallet-btns">
                                    <button class="stat-btn stat-btn-ghost" onclick="Dashboard.switchTab('wallet')">HISTORY</button>
                                    <button class="stat-btn stat-btn-accent" onclick="PayoutModal.open()">PAYOUT</button>
                                </div>
                            </div>
                            <?php if (!empty($walletMovements)): ?>
                            <div class="stat-card-split-r">
                                <?php foreach ($walletMovements as $_mv):
                                    $_isCredit = $_mv['amount'] > 0;
                                    $_amt = abs((float)$_mv['amount']);
                                    $_sign = $_isCredit ? '+' : '−';
                                    $_cls = $_isCredit ? 'wm-credit' : 'wm-debit';
                                    $_date = date('d M', strtotime($_mv['created_at']));
                                ?>
                                <div class="stat-wallet-move <?= $_cls ?>">
                                    <span class="swm-amt"><?= $_sign ?>$<?= number_format($_amt, 0) ?></span>
                                    <span class="swm-date"><?= $_date ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- 5. DOJI COINS -->
                    <div class="stat-card">
                        <div class="stat-card-lbl">DOJI COINS</div>
                        <div class="stat-card-split">
                            <div class="stat-card-split-l">
                                <div class="stat-coin-val"><?= number_format($dojiCoins) ?><span class="stat-coin-sym"> DC</span></div>
                                <div class="stat-coin-sub">EARNED FROM LOTS TRADED</div>
                                <button class="stat-btn stat-btn-ghost stat-btn-sm" onclick="Dashboard.switchTab('configurator')">BUY ACCOUNT</button>
                            </div>
                            <?php if (!empty($recentCoinsDays)): ?>
                            <div class="stat-card-split-r">
                                <?php foreach ($recentCoinsDays as $_cd):
                                    $_dayLabel = date('d M', strtotime($_cd['day']));
                                    $_isToday  = $_cd['day'] === date('Y-m-d');
                                ?>
                                <div class="stat-wallet-move wm-credit">
                                    <span class="swm-amt">+<?= number_format((int)$_cd['total']) ?> DC</span>
                                    <span class="swm-date"><?= $_isToday ? 'TODAY' : $_dayLabel ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- 6. TRADING DNA -->
                    <div class="stat-card stat-card--dna">
                        <div class="stat-card-lbl">TRADING DNA</div>
                        <div class="stat-dna-result stat-dna-result--flush">
                            <div class="stat-dna-letter" id="ovDnaGrade">—</div>
                            <div class="stat-dna-detail">
                                <div class="stat-dna-score" id="ovDnaGradeLbl">— / 10</div>
                                <div class="stat-dna-desc" id="ovDnaGradeDesc">—</div>
                                <div class="stat-seg-bar stat-seg-bar-sm" id="ovDnaSegs"><div class="stat-seg"></div><div class="stat-seg"></div><div class="stat-seg"></div><div class="stat-seg"></div><div class="stat-seg"></div><div class="stat-seg"></div><div class="stat-seg"></div><div class="stat-seg"></div><div class="stat-seg"></div><div class="stat-seg"></div></div>
                            </div>
                        </div>
                        <div class="stat-dna-ov-sub">$<?= number_format($gradePayoutTotal, 0) ?> TOTAL PAYOUTS · <?= $gradePayoutCount ?> PAYOUT<?= $gradePayoutCount !== 1 ? 'S' : '' ?></div>
                    </div>

                </div><!-- /.stat-grid -->
                <?php endif; ?>

            </div><!-- /tab-overview -->

            <!-- ══ TAB: MY CHALLENGES ══ -->
            <div class="dash-tab" id="tab-challenges">
                <div class="dash-tab-actions">
                    <div class="ch-summary">
                        <div class="ch-sum-item">
                            <span class="ch-sum-lbl">EVAL</span>
                            <span class="ch-sum-val"><?= $sumEvalCount ?></span>
                        </div>
                        <span class="ch-sum-sep">·</span>
                        <div class="ch-sum-item">
                            <span class="ch-sum-lbl">FUNDED</span>
                            <span class="ch-sum-val"><?= $sumFundedCount ?></span>
                        </div>
                        <?php if ($sumEvalAlloc > 0): ?>
                        <span class="ch-sum-sep">|</span>
                        <div class="ch-sum-item">
                            <span class="ch-sum-lbl">EVAL CAP</span>
                            <span class="ch-sum-val"><?= formatMoneyShort($sumEvalAlloc) ?></span>
                            <span class="ch-sum-perf <?= $sumEvalProfit >= 0 ? 'green' : 'red' ?>">
                                <?= ($sumEvalProfit >= 0 ? '+' : '') . formatMoney($sumEvalProfit) ?>
                                <span class="ch-sum-pct">(<?= ($sumEvalProfit >= 0 ? '+' : '') . number_format($sumEvalPct, 1) ?>%)</span>
                            </span>
                        </div>
                        <?php endif; ?>
                        <?php if ($sumFundedAlloc > 0): ?>
                        <span class="ch-sum-sep">|</span>
                        <div class="ch-sum-item">
                            <span class="ch-sum-lbl">FUNDED CAP</span>
                            <span class="ch-sum-val"><?= formatMoneyShort($sumFundedAlloc) ?></span>
                            <span class="ch-sum-perf <?= $sumFundedProfit >= 0 ? 'green' : 'red' ?>">
                                <?= ($sumFundedProfit >= 0 ? '+' : '') . formatMoney($sumFundedProfit) ?>
                                <span class="ch-sum-pct">(<?= ($sumFundedProfit >= 0 ? '+' : '') . number_format($sumFundedPct, 1) ?>%)</span>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <a href="challenges.php" class="dash-action-btn">+ New Challenge</a>
                </div>

                <?php if (!empty($challenges)): ?>
                <!-- ── CREDENTIALS CARD ── -->
                <div class="cred-card" id="credCard">

                    <div class="cred-header">
                        <span class="cred-card-lbl">TRADING CREDENTIALS</span>
                        <!-- Mobile: dropdown selector -->
                        <select class="cred-mobile-select cred-mobile-sel" id="credMobileSelect"
                                onchange="ChallengeCredentials.select(parseInt(this.value))">
                            <?php foreach ($challenges as $ch): ?>
                            <option value="<?= (int)$ch['id'] ?>">
                                <?= htmlspecialchars(
                                    ($ch['type'] === 'one_step' ? '1-Step' : '2-Step')
                                    . ' · ' . formatMoneyShort($ch['account_size'])
                                    . ' · #' . str_pad($ch['id'], 5, '0', STR_PAD_LEFT)
                                ) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Desktop: account pills -->
                    <div class="cred-accounts" id="credAccounts">
                        <?php foreach ($challenges as $i => $ch): ?>
                        <button class="cred-pill<?= $i === 0 ? ' active' : '' ?>"
                                data-cred-id="<?= (int)$ch['id'] ?>"
                                onclick="ChallengeCredentials.select(<?= (int)$ch['id'] ?>)">
                            <span class="cred-pill-dot <?= htmlspecialchars($ch['status']) ?>"></span>
                            <?= $ch['type'] === 'one_step' ? '1-Step' : '2-Step' ?>
                            <span class="cred-pill-size"><?= formatMoneyShort($ch['account_size']) ?></span>
                            <?php if ((int)$ch['phase'] > 1): ?>
                            <span class="cred-pill-phase">P<?= (int)$ch['phase'] ?></span>
                            <?php endif; ?>
                        </button>
                        <?php endforeach; ?>
                    </div>

                    <!-- Credentials fields -->
                    <div class="cred-grid">

                        <!-- LOGIN -->
                        <div class="cred-field">
                            <div class="cred-lbl">Login</div>
                            <div class="cred-row">
                                <span class="cred-val" id="credLogin">—</span>
                                <button class="cred-btn" onclick="ChallengeCredentials.copy('login', this)" title="Copy login">
                                    <?= pix('copy', 11, 11) ?>
                                </button>
                            </div>
                        </div>

                        <!-- MASTER PASSWORD -->
                        <div class="cred-field">
                            <div class="cred-lbl">Master Password</div>
                            <div class="cred-row">
                                <span class="cred-val cred-val-pass" id="credMasterPass">—</span>
                                <button class="cred-btn cred-master-eye-btn" id="credMasterToggle"
                                        onclick="ChallengeCredentials.toggleMaster()" title="Show / hide">
                                    <?= pix('eye', 11, 11) ?>
                                </button>
                                <button class="cred-btn" onclick="ChallengeCredentials.copy('master_password', this)" title="Copy">
                                    <?= pix('copy', 11, 11) ?>
                                </button>
                                <button class="cred-btn cred-btn-reset"
                                        onclick="ChallengeCredentials.resetMaster()" title="Reset password">
                                    <?= pix('refresh', 11, 11) ?>
                                </button>
                            </div>
                        </div>

                        <!-- INVESTOR PASSWORD -->
                        <div class="cred-field">
                            <div class="cred-lbl">Investor Password</div>
                            <div class="cred-row">
                                <span class="cred-val" id="credInvestorPass">—</span>
                                <button class="cred-btn" onclick="ChallengeCredentials.copy('investor_password', this)" title="Copy">
                                    <?= pix('copy', 11, 11) ?>
                                </button>
                            </div>
                        </div>

                        <!-- SERVER -->
                        <div class="cred-field">
                            <div class="cred-lbl">Server</div>
                            <div class="cred-row">
                                <span class="cred-val" id="credServer">—</span>
                                <button class="cred-btn" onclick="ChallengeCredentials.copy('server', this)" title="Copy">
                                    <?= pix('copy', 11, 11) ?>
                                </button>
                            </div>
                        </div>

                    </div><!-- /.cred-grid -->
                </div><!-- /.cred-card -->

                <!-- ── KPI CARDS (synced with account selector) ── -->
                <div class="ov-top-row" id="chKpiRow">

                    <!-- Account Balance -->
                    <div class="ov-card">
                        <div class="ov-card-lbl">ACCOUNT BALANCE</div>
                        <div class="ov-card-val" id="chKpiBalance">—</div>
                        <div class="ov-card-sub" id="chKpiPnl">—</div>
                    </div>

                    <!-- Profit Target -->
                    <div class="ov-card">
                        <div class="ov-card-lbl">PROFIT TARGET</div>
                        <div class="ov-card-val" id="chKpiProfitPct">—</div>
                        <div class="ov-card-convert" id="chKpiProfitConvert">—</div>
                        <div class="ov-bar-wrap">
                            <div class="ov-bar-fill ov-bar-fill-green" id="chKpiProfitBar" style="width:0%"></div>
                        </div>
                    </div>

                    <!-- Daily Drawdown -->
                    <div class="ov-card">
                        <div class="ov-card-lbl-row">
                            <span class="ov-card-lbl">DAILY DRAWDOWN</span>
                            <span class="dd-type-badge" id="chKpiDdType">—</span>
                        </div>
                        <div class="ov-card-val" id="chKpiDdPct">—</div>
                        <div class="ov-card-convert" id="chKpiDdConvert">—</div>
                        <div class="ov-bar-wrap">
                            <div class="ov-bar-fill ov-bar-fill-amber" id="chKpiDdBar" style="width:0%"></div>
                        </div>
                        <div class="dd-reset-row">
                            <span class="dd-reset-lbl">RESETS IN</span>
                            <span class="dd-reset-val" id="chDdCountdown">--:--:--</span>
                        </div>
                    </div>

                    <!-- Max Drawdown -->
                    <div class="ov-card">
                        <div class="ov-card-lbl-row">
                            <span class="ov-card-lbl">MAX DRAWDOWN</span>
                            <span class="dd-type-badge" id="chKpiMdType">—</span>
                        </div>
                        <div class="ov-card-val" id="chKpiMdPct">—</div>
                        <div class="ov-card-convert" id="chKpiMdConvert">—</div>
                        <div class="ov-bar-wrap">
                            <div class="ov-bar-fill ov-bar-fill-amber" id="chKpiMdBar" style="width:0%"></div>
                        </div>
                        <div class="ov-card-sub" style="margin-top:var(--s8);font-size:10px;color:var(--text-dis)">OVERALL · FROM PEAK</div>
                    </div>

                    <!-- Consistency -->
                    <div class="ov-card ov-card-cons">
                        <div class="ov-card-lbl">CONSISTENCY</div>
                        <div class="ov-cons-wrap">
                            <div class="cons-gauge-container">
                                <svg viewBox="0 0 100 100" class="cons-svg">
                                    <circle class="cons-track" cx="50" cy="50" r="34" fill="none" stroke-width="7" stroke-dasharray="160.22 213.63" transform="rotate(135 50 50)"/>
                                    <circle class="cons-fill" id="chKpiConsArc" cx="50" cy="50" r="34" fill="none" stroke="#333" stroke-width="7" stroke-linecap="round" stroke-dasharray="0 213.63" transform="rotate(135 50 50)"/>
                                </svg>
                                <div class="cons-center-text">
                                    <span class="cons-pct" id="chKpiConsPct">—</span>
                                </div>
                                <div class="cons-limit-text" id="chKpiConsLimit">—</div>
                            </div>
                            <div class="cons-meta">
                                <span class="cons-status" id="chKpiConsStatus">—</span>
                            </div>
                        </div>
                    </div>

                    <!-- Account Info -->
                    <div class="ov-card ov-card-info">
                        <div class="ov-card-lbl">ACCOUNT INFO</div>
                        <div class="ov-info-type" id="chKpiType">—</div>
                        <div class="ov-info-id-row">
                            <span class="ov-info-id" id="chKpiId">—</span>
                            <button class="ov-copy-btn" id="chKpiCopyBtn" onclick="ChallengeCredentials.copyChId(this)" title="Copy reference">
                                <?= pix('copy', 12, 12) ?>
                            </button>
                        </div>
                        <div class="ov-info-actions">
                            <button class="ov-action-btn ov-action-payout" id="chKpiPayoutBtn" style="display:none"
                                    onclick="ChallengeCredentials.goToPayouts()">
                                <?= pix('dollar', 11, 11) ?>
                                Payout
                            </button>
                            <button class="ov-action-btn ov-action-reset" id="chKpiResetBtn" style="display:none"
                                    onclick="ChallengeCredentials.resetChallenge()">
                                <?= pix('refresh', 11, 11) ?>
                                Reset
                            </button>
                            <button class="ov-action-btn ov-action-delete" id="chKpiDeleteBtn"
                                    onclick="ChallengeCredentials.deleteChallenge()">
                                <?= pix('trash', 11, 11) ?>
                                Delete
                            </button>
                        </div>
                    </div>

                </div><!-- /#chKpiRow -->

                <script>
                window.__credData    = <?= json_encode($credData,    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
                window.__credFirstId = <?= (int)$credFirstId ?>;
                </script>
                <?php endif; /* end credentials card */ ?>

                <div class="dash-filters">
                    <button class="dash-filter active" data-filter="all">All</button>
                    <button class="dash-filter" data-filter="active">Active</button>
                    <button class="dash-filter" data-filter="funded">Funded</button>
                    <button class="dash-filter" data-filter="passed">Passed</button>
                    <button class="dash-filter" data-filter="failed">Failed</button>
                </div>
                <?php if (!empty($challenges)): ?>
                <div class="dash-table-wrap">
                    <table class="dash-table">
                        <thead><tr><th>Challenge</th><th>Account</th><th>Progress</th><th>Balance</th><th>P&amp;L</th><th>Max Loss</th><th>Consistency</th><th>Status</th><th>Trading Days</th><th>Doji Coins</th></tr></thead>
                        <tbody>
                            <?php foreach ($challenges as $ch):
                                $pnlPct    = $ch['account_size'] > 0 ? ($ch['total_profit'] / $ch['account_size']) * 100 : 0;
                                $target    = $ch['profit_target_1'];
                                $tConsRule = max(1, (float)($ch['consistency_rule'] ?? 30));
                                $tBest     = (float)($ch['best_trade'] ?? 0);
                                $tConsUsed = ($ch['total_profit'] > 0 && $tBest > 0) ? ($tBest / $ch['total_profit']) * 100 : 0;
                                $tConsPct  = $tConsUsed > 0 ? ($tConsUsed / $tConsRule) * 100 : 0;
                                $tConsClr  = $tConsPct >= 100 ? '#D71921' : ($tConsPct >= 80 ? '#e86820' : ($tConsPct >= 60 ? '#D4A843' : ($tConsUsed > 0 ? '#10B981' : '')));
                            ?>
                            <tr class="dash-row" data-status="<?= $ch['status'] ?>"
                                data-cred-id="<?= (int)$ch['id'] ?>"
                                onclick="ChallengeCredentials.selectFromRow(this, <?= (int)$ch['id'] ?>)"
                                style="cursor:pointer">
                                <td><div class="dash-cell-type"><?= $ch['type'] === 'one_step' ? '1-Step' : '2-Step' ?></div><div class="dash-cell-sub"><?= strtoupper($ch['platform']) ?><?= $ch['phase'] > 1 ? ' · Phase ' . $ch['phase'] : '' ?></div></td>
                                <td class="mono"><?= formatMoneyShort($ch['account_size']) ?></td>
                                <td><div class="dash-mini-bar"><div class="dash-mini-bar-fill" style="width:<?= min(100, max(0, ($pnlPct / $target) * 100)) ?>%"></div></div><span class="dash-cell-sub"><?= number_format($pnlPct, 1) ?>% / <?= $target ?>%</span></td>
                                <td class="mono"><?= formatMoney($ch['current_balance']) ?></td>
                                <td class="mono <?= $ch['total_profit'] >= 0 ? 'green' : 'red' ?>"><?= $ch['total_profit'] >= 0 ? '+' : '' ?><?= formatMoney($ch['total_profit']) ?></td>
                                <?php
                                    $mlType    = $ch['max_loss_type'] ?? 'intraday';
                                    $mlPct     = (float)$ch['max_loss'];
                                    $mlSize    = (float)$ch['account_size'];
                                    $mlPeak    = max((float)$ch['peak_balance'], (float)$ch['current_balance']);
                                    $mlBalance = (float)$ch['current_balance'];
                                    $mlAmount  = $mlSize * $mlPct / 100;
                                    if ($mlType === 'static') {
                                        $mlFloor = $mlSize - $mlAmount;
                                    } elseif ($mlType === 'eod') {
                                        $mlFloor = $mlBalance - $mlAmount;
                                    } else { // intraday / trailing
                                        $mlFloor = $mlPeak - $mlAmount;
                                    }
                                ?>
                                <td>
                                    <div class="mono"><?= formatMoney($mlFloor) ?></div>
                                    <div class="dash-cell-sub"><?= lossTypeLabel($mlType) ?></div>
                                </td>
                                <td class="mono" style="<?= $tConsClr ? 'color:' . $tConsClr : '' ?>">
                                    <?php if ($ch['status'] !== 'funded'): ?>
                                        <span class="dash-cell-sub">—</span>
                                    <?php elseif ($tConsUsed > 0): ?>
                                        <?= number_format($tConsUsed, 0) ?>%<span class="dash-cell-sub"> /<?= $tConsRule ?>%</span>
                                    <?php else: ?>
                                        <span class="dash-cell-sub">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= challengeStatusBadge($ch['status']) ?></td>
                                <?php $tdDays = (int)$ch['trading_days']; $tdMin = (int)$ch['min_trading_days']; $tdClr = $tdDays >= $tdMin ? 'green' : 'warn'; ?>
                                <td class="mono <?= $tdClr ?>"><?= $tdDays ?><span class="dash-cell-sub"> /<?= $tdMin ?></span></td>
                                <?php $chCoins = (int)floor((float)($ch['lots_traded'] ?? 0)); ?>
                                <td class="mono ch-coins-cell <?= $chCoins > 0 ? 'coins-active' : '' ?>">
                                    <?php if ($chCoins > 0): ?>
                                        <span class="ch-coin-icon">◈</span><?= number_format($chCoins) ?>
                                    <?php else: ?>
                                        <span class="dash-cell-sub">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="dash-empty">
                    <?= pix('layers', 40, 40) ?>
                    <h3>No Challenges Yet</h3>
                    <p>Use the configurator to set up your first challenge</p>
                    <button class="dash-action-btn" onclick="Dashboard.switchTab('configurator')">Configure a Challenge</button>
                </div>
                <?php endif; ?>
            </div>

            <!-- ══ TAB: WALLET ══ -->
            <div class="dash-tab" id="tab-wallet">
                <?php
                $wAllTx    = getAllWalletTransactions($userId);
                $wAllCoins = getAllCoinsTransactions($userId);
                $wCoins    = (int)($overview['doji_coins'] ?? 0);
                $wNextRwd  = max(0, 5000 - ($wCoins % 5000 ?: 5000));
                $wProgPct  = $wCoins > 0 ? min(100, (($wCoins % 5000) / 5000) * 100) : 0;

                /* Badge labels per type */
                $wltBadge = [
                    'payout_transfer'   => 'PAYOUT',
                    'challenge_purchase'=> 'PURCHASE',
                ];
                $dcBadge = [
                    'volume'              => 'VOLUME',
                    'bonus'               => 'BONUS',
                    'promo'               => 'PROMO',
                    'referral'            => 'REFERRAL',
                    'discount_purchase'   => 'DISCOUNT',
                    'account_purchase'    => 'PURCHASE',
                    'profit_split_upgrade'=> 'UPGRADE',
                ];
                ?>
                <div class="wlt-grid">

                    <!-- ─ DOJI WALLET card ─ -->
                    <div class="wlt-card" style="--wlt-accent:#10B981">
                        <div class="wlt-card-head">
                            <div class="wlt-card-title">DOJI WALLET</div>
                            <div class="wlt-card-bal-row">
                                <div class="wlt-card-bal"><?= formatMoney($walletBalance) ?></div>
                                <div class="wlt-card-btns">
                                    <button class="wlt-action-btn wlt-btn-accent" onclick="PayoutModal.open()">PAYOUT</button>
                                    <button class="wlt-action-btn wlt-btn-ghost" onclick="Dashboard.switchTab('configurator')">BUY CHALLENGES</button>
                                </div>
                            </div>
                            <div class="wlt-card-sub">AVAILABLE BALANCE</div>
                            <div class="wlt-card-prog-wrap">
                                <?php
                                $wTotalIn = 0; $wTotalOut = 0;
                                foreach ($wAllTx as $_t) {
                                    if ($_t['amount'] > 0) $wTotalIn  += $_t['amount'];
                                    else                   $wTotalOut += abs($_t['amount']);
                                }
                                $wInPct   = ($wTotalIn + $wTotalOut) > 0 ? ($wTotalIn / ($wTotalIn + $wTotalOut)) * 100 : 0;
                                $_wSegs   = 20;
                                $_wOnSegs = (int)round($_wSegs * $wInPct / 100);
                                ?>
                                <div class="wlt-seg-bar">
                                    <?php for ($_s = 0; $_s < $_wSegs; $_s++): ?>
                                    <div class="wlt-seg<?= $_s < $_wOnSegs ? ' wlt-seg-on wlt-seg-green' : '' ?>"></div>
                                    <?php endfor; ?>
                                </div>
                                <div class="wlt-card-prog-labels">
                                    <span class="wlt-prog-lbl green">+<?= formatMoney($wTotalIn) ?> IN</span>
                                    <span class="wlt-prog-lbl red">−<?= formatMoney($wTotalOut) ?> OUT</span>
                                </div>
                            </div>
                        </div>
                        <div class="wlt-tx-list" id="wltTxWallet">
                            <div class="wlt-tx-header">
                                <span>TYPE</span><span>DESCRIPTION</span><span>AMOUNT</span><span>DATE</span>
                            </div>
                            <?php if (empty($wAllTx)): ?>
                            <div class="wlt-tx-empty">NO TRANSACTIONS YET</div>
                            <?php else: foreach ($wAllTx as $_i => $_tx):
                                $_credit = $_tx['amount'] > 0;
                                $_amt    = abs((float)$_tx['amount']);
                                $_sign   = $_credit ? '+' : '−';
                                $_cls    = $_credit ? 'wlt-tx-credit' : 'wlt-tx-debit';
                                $_badge  = $wltBadge[$_tx['type']] ?? strtoupper($_tx['type']);
                                $_date   = date('d M Y', strtotime($_tx['created_at']));
                            ?>
                            <div class="wlt-tx-row <?= $_cls ?>" data-wlt-idx="<?= $_i ?>">
                                <span class="wlt-tx-badge wlt-badge-<?= $_credit ? 'credit' : 'debit' ?>"><?= $_badge ?></span>
                                <span class="wlt-tx-desc"><?= htmlspecialchars($_tx['description']) ?></span>
                                <span class="wlt-tx-amt"><?= $_sign ?>$<?= number_format($_amt, 2) ?></span>
                                <span class="wlt-tx-date"><?= $_date ?></span>
                            </div>
                            <?php endforeach; endif; ?>
                        </div>
                        <?php if (count($wAllTx) > 8): ?>
                        <div class="wlt-pagination" id="wltPagWallet" data-list="wltTxWallet" data-total="<?= count($wAllTx) ?>">
                            <button class="wlt-pg-btn" data-dir="-1">&#8592;</button>
                            <span class="wlt-pg-info"></span>
                            <button class="wlt-pg-btn" data-dir="1">&#8594;</button>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- ─ DOJI COINS card ─ -->
                    <div class="wlt-card" style="--wlt-accent:#D4A843">
                        <div class="wlt-card-head">
                            <div class="wlt-card-title">DOJI COINS</div>
                            <div class="wlt-card-bal-row">
                                <div class="wlt-card-bal wlt-card-bal--coins"><?= number_format($wCoins) ?> <span class="wlt-dc-sym">DC</span></div>
                                <div class="wlt-card-btns">
                                    <button class="wlt-action-btn wlt-btn-ghost" onclick="Dashboard.switchTab('configurator')">BUY CHALLENGES</button>
                                    <button class="wlt-action-btn wlt-btn-ghost" onclick="DiscountModal.open()">DISCOUNT</button>
                                    <button class="wlt-action-btn wlt-btn-ghost" onclick="ProfitSplitModal.open()">PROFIT SPLIT</button>
                                </div>
                            </div>
                            <div class="wlt-card-sub">TOTAL BALANCE</div>
                            <div class="wlt-card-prog-wrap">
                                <?php
                                $_dcSegs   = 20;
                                $_dcOnSegs = (int)round($_dcSegs * $wProgPct / 100);
                                ?>
                                <div class="wlt-seg-bar">
                                    <?php for ($_s = 0; $_s < $_dcSegs; $_s++): ?>
                                    <div class="wlt-seg<?= $_s < $_dcOnSegs ? ' wlt-seg-on wlt-seg-amber' : '' ?>"></div>
                                    <?php endfor; ?>
                                </div>
                                <div class="wlt-card-prog-labels">
                                    <span class="wlt-prog-lbl amber"><?= number_format($wCoins % 5000 ?: ($wCoins > 0 ? 5000 : 0)) ?> / 5,000 DC</span>
                                    <span class="wlt-prog-lbl dim">NEXT REWARD <?= number_format($wNextRwd) ?> DC AWAY</span>
                                </div>
                            </div>
                        </div>
                        <div class="wlt-tx-list" id="wltTxCoins">
                            <div class="wlt-tx-header">
                                <span>TYPE</span><span>DESCRIPTION</span><span>AMOUNT</span><span>DATE</span>
                            </div>
                            <?php if (empty($wAllCoins)): ?>
                            <div class="wlt-tx-empty">NO TRANSACTIONS YET</div>
                            <?php else: foreach ($wAllCoins as $_i => $_cx):
                                $_credit = $_cx['amount'] > 0;
                                $_amt    = abs((int)$_cx['amount']);
                                $_sign   = $_credit ? '+' : '−';
                                $_cls    = $_credit ? 'wlt-tx-credit' : 'wlt-tx-debit';
                                $_badge  = $dcBadge[$_cx['source']] ?? strtoupper($_cx['source']);
                                $_date   = date('d M Y', strtotime($_cx['created_at']));
                            ?>
                            <div class="wlt-tx-row <?= $_cls ?>" data-wlt-idx="<?= $_i ?>">
                                <span class="wlt-tx-badge wlt-badge-<?= $_credit ? 'coins' : 'debit' ?>"><?= $_badge ?></span>
                                <span class="wlt-tx-desc"><?= htmlspecialchars($_cx['description']) ?></span>
                                <span class="wlt-tx-amt wlt-tx-amt--coins"><?= $_sign ?><?= number_format($_amt) ?> DC</span>
                                <span class="wlt-tx-date"><?= $_date ?></span>
                            </div>
                            <?php endforeach; endif; ?>
                        </div>
                        <?php if (count($wAllCoins) > 8): ?>
                        <div class="wlt-pagination" id="wltPagCoins" data-list="wltTxCoins" data-total="<?= count($wAllCoins) ?>">
                            <button class="wlt-pg-btn" data-dir="-1">&#8592;</button>
                            <span class="wlt-pg-info"></span>
                            <button class="wlt-pg-btn" data-dir="1">&#8594;</button>
                        </div>
                        <?php endif; ?>
                    </div>

                </div>

                <!-- ══ ACHIEVEMENTS ══ -->
                <?php
                $_achIcons = [
                    'platform'    => pix('user',       18, 18),
                    'volume'      => pix('pulse',      18, 18),
                    'challenge'   => pix('trophy',     18, 18),
                    'payout'      => pix('dollar',     18, 18),
                    'streak'      => pix('lightning',  18, 18),
                    'competition' => pix('medal',      18, 18),
                    'social'      => pix('users',      18, 18),
                    'loyalty'     => pix('shield',     18, 18),
                    'community'   => pix('heart',      18, 18),
                ];
                $_achColors = [
                    'platform'    => '#6366F1',
                    'volume'      => '#10B981',
                    'challenge'   => '#D4A843',
                    'payout'      => '#0EA5E9',
                    'streak'      => '#EF4444',
                    'competition' => '#8B5CF6',
                    'social'      => '#F97316',
                    'loyalty'     => '#EC4899',
                    'community'   => '#00B67A',
                ];
                $_achievements = [
                    /* PLATFORM */
                    ['key'=>'welcome',          'cat'=>'platform',    'title'=>'FIRST STEPS',      'desc'=>'Create your Doji Funding account',           'dc'=>50],
                    ['key'=>'profile_complete', 'cat'=>'platform',    'title'=>'READY TO TRADE',   'desc'=>'Complete your trader profile',               'dc'=>100],
                    ['key'=>'kyc_done',         'cat'=>'platform',    'title'=>'VERIFIED TRADER',  'desc'=>'Pass KYC identity verification',             'dc'=>200],
                    /* VOLUME */
                    ['key'=>'lots_10',          'cat'=>'volume',      'title'=>'FIRST LOTS',       'desc'=>'Trade 10 cumulative lots',                   'dc'=>100],
                    ['key'=>'lots_50',          'cat'=>'volume',      'title'=>'ACTIVE TRADER',    'desc'=>'Trade 50 cumulative lots',                   'dc'=>250],
                    ['key'=>'lots_100',         'cat'=>'volume',      'title'=>'HIGH VOLUME',      'desc'=>'Trade 100 cumulative lots',                  'dc'=>500],
                    ['key'=>'lots_500',         'cat'=>'volume',      'title'=>'POWER TRADER',     'desc'=>'Trade 500 cumulative lots',                  'dc'=>2500],
                    ['key'=>'lots_1000',        'cat'=>'volume',      'title'=>'ELITE VOLUME',     'desc'=>'Trade 1,000 cumulative lots',                'dc'=>5000],
                    /* CHALLENGE */
                    ['key'=>'first_challenge',  'cat'=>'challenge',   'title'=>'CHALLENGER',       'desc'=>'Purchase your first challenge',              'dc'=>100],
                    ['key'=>'pass_1step',       'cat'=>'challenge',   'title'=>'1-STEP PASSED',    'desc'=>'Pass a 1-Step evaluation',                   'dc'=>500],
                    ['key'=>'pass_2step',       'cat'=>'challenge',   'title'=>'2-STEP PASSED',    'desc'=>'Pass a 2-Step evaluation',                   'dc'=>750],
                    ['key'=>'first_funded',     'cat'=>'challenge',   'title'=>'FUNDED!',          'desc'=>'Receive your first funded account',          'dc'=>1000],
                    ['key'=>'multi_funded',     'cat'=>'challenge',   'title'=>'MULTI-ACCOUNT',    'desc'=>'Manage 3 or more funded accounts',           'dc'=>2000],
                    /* PAYOUT */
                    ['key'=>'first_payout',     'cat'=>'payout',      'title'=>'FIRST PAYMENT',    'desc'=>'Receive your first payout',                  'dc'=>250],
                    ['key'=>'payout_1k',        'cat'=>'payout',      'title'=>'$1K PAID OUT',     'desc'=>'Cumulative $1,000 paid out',                 'dc'=>500],
                    ['key'=>'payout_5k',        'cat'=>'payout',      'title'=>'$5K PAID OUT',     'desc'=>'Cumulative $5,000 paid out',                 'dc'=>1500],
                    ['key'=>'payout_10k',       'cat'=>'payout',      'title'=>'$10K PAID OUT',    'desc'=>'Cumulative $10,000 paid out',                'dc'=>3000],
                    ['key'=>'payout_50k',       'cat'=>'payout',      'title'=>'$50K PAID OUT',    'desc'=>'Cumulative $50,000 paid out',                'dc'=>10000],
                    /* STREAK */
                    ['key'=>'streak_3',         'cat'=>'streak',      'title'=>'CONSISTENT',       'desc'=>'3 consecutive profitable trading days',      'dc'=>150],
                    ['key'=>'streak_5',         'cat'=>'streak',      'title'=>'ON A ROLL',        'desc'=>'5 consecutive days (activates 3× coins)',    'dc'=>300],
                    ['key'=>'streak_10',        'cat'=>'streak',      'title'=>'UNSTOPPABLE',      'desc'=>'10 consecutive profitable trading days',     'dc'=>1000],
                    ['key'=>'streak_20',        'cat'=>'streak',      'title'=>'LEGEND',           'desc'=>'20 consecutive profitable trading days',     'dc'=>5000],
                    /* COMPETITION */
                    ['key'=>'comp_entry',       'cat'=>'competition', 'title'=>'COMPETITOR',       'desc'=>'Enter your first competition',               'dc'=>100],
                    ['key'=>'comp_top10',       'cat'=>'competition', 'title'=>'TOP 10',           'desc'=>'Finish in the top 10 of a competition',      'dc'=>1000],
                    ['key'=>'comp_podium',      'cat'=>'competition', 'title'=>'PODIUM',           'desc'=>'Finish in the top 3 of a competition',       'dc'=>2500],
                    ['key'=>'comp_winner',      'cat'=>'competition', 'title'=>'CHAMPION',         'desc'=>'Win a competition',                          'dc'=>5000],
                    /* SOCIAL */
                    ['key'=>'referral_1',       'cat'=>'social',      'title'=>'FIRST REFERRAL',   'desc'=>'Refer your first successful trader',         'dc'=>500],
                    ['key'=>'referral_5',       'cat'=>'social',      'title'=>'AMBASSADOR',       'desc'=>'Refer 5 successful traders',                 'dc'=>2500],
                    ['key'=>'referral_10',      'cat'=>'social',      'title'=>'TOP AFFILIATE',    'desc'=>'Refer 10 successful traders',                'dc'=>6000],
                    /* LOYALTY */
                    ['key'=>'days_30',          'cat'=>'loyalty',     'title'=>'MONTH IN',         'desc'=>'30 days active on the platform',             'dc'=>200],
                    ['key'=>'days_90',          'cat'=>'loyalty',     'title'=>'THREE MONTHS',     'desc'=>'90 days active on the platform',             'dc'=>500],
                    ['key'=>'days_365',         'cat'=>'loyalty',     'title'=>'ONE YEAR',         'desc'=>'One full year with Doji Funding',            'dc'=>2500],
                    /* COMMUNITY */
                    ['key'=>'pfm_vote',         'cat'=>'community',   'title'=>'PEOPLE\'S CHOICE', 'desc'=>'Vote Doji Funding as your favorite prop firm on PropFirmMatch', 'dc'=>150],
                    ['key'=>'pfm_review',       'cat'=>'community',   'title'=>'PROPFIRM VOICE',   'desc'=>'Leave a review for Doji Funding on PropFirmMatch',              'dc'=>300],
                    ['key'=>'tp_review',        'cat'=>'community',   'title'=>'TRUSTED REVIEW',   'desc'=>'Leave a review for Doji Funding on Trustpilot',                'dc'=>300],
                    ['key'=>'google_review',    'cat'=>'community',   'title'=>'GOOGLE CERTIFIED', 'desc'=>'Leave a review for Doji Funding on Google',                    'dc'=>300],
                    ['key'=>'discord_payout',   'cat'=>'community',   'title'=>'PROOF OF PAYMENT', 'desc'=>'Share your payout screenshot in the Doji Discord server',      'dc'=>200],
                    ['key'=>'discord_join',     'cat'=>'community',   'title'=>'CONNECTED',        'desc'=>'Join the Discord server and link your account to the dashboard','dc'=>250],
                ];
                $_unlockedAch = [];
                try {
                    $_achDb = getDB();
                    if ($_achDb) {
                        $_s = $_achDb->prepare('SELECT achievement_key FROM achievements WHERE user_id = ?');
                        $_s->execute([$userId]);
                        $_unlockedAch = array_column($_s->fetchAll(PDO::FETCH_ASSOC), 'achievement_key');
                    }
                } catch (Exception $_e) {}
                $_achTotal    = count($_achievements);
                $_achUnlocked = count($_unlockedAch);
                $_achPct      = $_achTotal > 0 ? round(($_achUnlocked / $_achTotal) * 100) : 0;
                $_catCounts   = [];
                foreach ($_achievements as $_a) {
                    $_catCounts[$_a['cat']] = ($_catCounts[$_a['cat']] ?? 0) + 1;
                }
                ?>
                <div class="wlt-ach-section">

                    <div class="wlt-ach-header">
                        <div>
                            <div class="wlt-ach-ttl">ACHIEVEMENTS</div>
                            <div class="wlt-ach-meta"><?= $_achUnlocked ?> / <?= $_achTotal ?> UNLOCKED · EARN DOJI COINS BY COMPLETING MILESTONES</div>
                        </div>
                        <div class="wlt-ach-progress">
                            <div class="wlt-ach-prog-bar">
                                <div class="wlt-ach-prog-fill" style="width:<?= $_achPct ?>%"></div>
                            </div>
                            <div class="wlt-ach-prog-pct"><?= $_achPct ?>%</div>
                        </div>
                    </div>

                    <div class="wlt-ach-cats" id="wltAchCats">
                        <button class="wlt-ach-cat active" data-cat="all">ALL <span><?= $_achTotal ?></span></button>
                        <?php foreach ($_catCounts as $_cat => $_cnt): ?>
                        <button class="wlt-ach-cat" data-cat="<?= $_cat ?>" style="--ach-c:<?= $_achColors[$_cat] ?>">
                            <?= strtoupper($_cat) ?> <span><?= $_cnt ?></span>
                        </button>
                        <?php endforeach; ?>
                    </div>

                    <div class="wlt-ach-grid" id="wltAchGrid">
                        <?php foreach ($_achievements as $_ach):
                            $_on    = in_array($_ach['key'], $_unlockedAch);
                            $_color = $_achColors[$_ach['cat']];
                            $_icon  = $_achIcons[$_ach['cat']];
                        ?>
                        <div class="wlt-ach-card<?= $_on ? ' wlt-ach-on' : '' ?>"
                             style="--ach-c:<?= $_color ?>"
                             data-cat="<?= $_ach['cat'] ?>">
                            <div class="wlt-ach-card-icon"><?= $_icon ?></div>
                            <div class="wlt-ach-card-cat"><?= strtoupper($_ach['cat']) ?></div>
                            <div class="wlt-ach-card-title"><?= $_ach['title'] ?></div>
                            <div class="wlt-ach-card-desc"><?= htmlspecialchars($_ach['desc']) ?></div>
                            <div class="wlt-ach-card-reward">+<?= number_format($_ach['dc']) ?> DC</div>
                            <?php if (!$_on): ?>
                            <div class="wlt-ach-card-lock"><?= pix('lock', 12, 12) ?></div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>

                </div>

            </div>

            <!-- ══ TAB: PAYOUTS ══ -->
            <div class="dash-tab" id="tab-payouts">
                <?php
                $payoutTotal = count($payouts);
                $pyoMethodInfo = function($method) {
                    $map = [
                        'rise'         => ['USD',  'Rise'],
                        'confirmo'     => ['USDT', 'Confirmo'],
                        'crypto_btc'   => ['BTC',  'Confirmo'],
                        'crypto_eth'   => ['ETH',  'Confirmo'],
                        'crypto_usdt'  => ['USDT', 'Confirmo'],
                        'bank_transfer'=> ['USD',  'Rise'],
                        'wire_transfer'=> ['USD',  'Wire'],
                        'wise'         => ['USD',  'Wise'],
                        'paypal'       => ['USD',  'PayPal'],
                    ];
                    return $map[$method] ?? ['USD', ucfirst(str_replace('_', ' ', $method ?: '—'))];
                };
                $pyoProgress = function($status, $actionDetail = '') {
                    /* seg0 = IN REVIEW, seg1 = ACTION, seg2 = COMPLETED */
                    if ($status === 'completed') {
                        $segs  = ['done', 'done', 'done'];
                        $lbl   = 'COMPLETED'; $mod = 'done';
                    } elseif ($status === 'action_required') {
                        $segs  = ['done', 'active', 'empty'];
                        $lbl   = 'ACTION REQUIRED'; $mod = 'active';
                    } elseif ($status === 'failed') {
                        $segs  = ['done', 'failed', 'empty'];
                        $lbl   = 'REJECTED'; $mod = 'failed';
                    } else {
                        $segs  = ['active', 'empty', 'empty'];
                        $lbl   = 'IN REVIEW'; $mod = 'active';
                    }
                    $h  = '<div class="pyo-seg">';
                    $h .= '<div class="pyo-seg-bar">';
                    foreach ($segs as $s) {
                        $h .= '<div class="pyo-seg-step pyo-seg-' . $s . '"></div>';
                    }
                    $h .= '</div>';
                    $h .= '<span class="pyo-seg-lbl pyo-seg-lbl--' . $mod . '">' . $lbl . '</span>';
                    if ($status === 'action_required' && $actionDetail !== '') {
                        $h .= '<span class="pyo-seg-action">' . htmlspecialchars($actionDetail) . '</span>';
                    }
                    $h .= '</div>';
                    return $h;
                };

                ?>

                <!-- Stats bar -->
                <div class="pyo-stats-bar">
                    <div class="pyo-stat" style="--ps-c:#10B981">
                        <span class="pyo-stat-lbl">TOTAL EARNED</span>
                        <span class="pyo-stat-val"><?= formatMoney($overview['total_payout_amount']) ?></span>
                    </div>
                    <div class="pyo-stat" style="--ps-c:#0EA5E9">
                        <span class="pyo-stat-lbl">PAYOUTS</span>
                        <span class="pyo-stat-val"><?= $payoutTotal ?></span>
                    </div>
                    <div class="pyo-stat" style="--ps-c:#8B5CF6">
                        <span class="pyo-stat-lbl">COMPLETED</span>
                        <span class="pyo-stat-val"><?= count(array_filter($payouts, fn($p) => $p['status'] === 'completed')) ?></span>
                    </div>
                    <div class="pyo-stat" style="--ps-c:#D4A843">
                        <span class="pyo-stat-lbl">PENDING</span>
                        <span class="pyo-stat-val"><?= count(array_filter($payouts, fn($p) => $p['status'] === 'pending')) ?></span>
                    </div>
                </div>

                <!-- Eligible Accounts Card -->
                <?php $pyoEligible = array_filter($challenges, fn($c) => $c['status'] === 'funded'); ?>
                <?php if (!empty($pyoEligible)): ?>
                <div class="pyo-eligible-card">
                    <div class="pyo-eligible-head">
                        <span class="pyo-eligible-title">FUNDED ACCOUNTS <span class="pyo-eligible-badge"><?= count($pyoEligible) ?></span></span>
                        <span class="pyo-eligible-sub">PAYOUT ELIGIBLE</span>
                    </div>
                    <?php foreach ($pyoEligible as $ea):
                        $eaRef    = challengeAcctRef($ea['type'], $ea['account_size'], $userId, $acctIndexMap[(int)$ea['id']] ?? 1);
                        $eaSplit  = (int)($ea['profit_split'] ?? 80);
                        $eaProfit = (float)($ea['total_profit'] ?? 0);
                    ?>
                    <div class="pyo-eligible-item">
                        <div class="pyo-ei-ref"><?= htmlspecialchars($eaRef) ?></div>
                        <div class="pyo-ei-meta"><?= htmlspecialchars(formatMoney($ea['account_size'])) ?> &nbsp;·&nbsp; <?= $eaSplit ?>% SPLIT</div>
                        <div class="pyo-ei-profit <?= $eaProfit >= 0 ? 'green' : '' ?>"><?= ($eaProfit >= 0 ? '+' : '') . formatMoney($eaProfit) ?></div>
                        <button class="pyo-request-btn" type="button"
                            onclick="RequestPayoutModal.open(<?= $ea['id'] ?>, '<?= htmlspecialchars($eaRef, ENT_QUOTES) ?>', <?= round($eaProfit * ($eaSplit / 100), 2) ?>)">REQUEST PAYOUT →</button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Payouts Table -->
                <?php if (!empty($payouts)): ?>
                <div class="pyo-table-wrap">
                    <table class="pyo-table">
                        <thead>
                            <tr>
                                <th class="pyo-th-num">#</th>
                                <th>SOURCE</th>
                                <th>AMOUNT</th>
                                <th>METHOD</th>
                                <th>STATUS</th>
                                <th>REQUESTED</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody id="pyoTableBody">
                            <?php foreach ($payouts as $poIdx => $po):
                                $poNum    = $payoutTotal - $poIdx;
                                $mInfo    = $pyoMethodInfo($po['method'] ?? '');
                                $poRef    = challengeAcctRef(
                                    $po['challenge_type'] ?? 'one_step',
                                    $po['account_size'] ?? 0,
                                    $userId,
                                    $acctIndexMap[(int)($po['challenge_id'] ?? 0)] ?? 1
                                );
                                $poSource = !empty($po['challenge_id']) ? $poRef : 'WALLET';
                                $poJson   = htmlspecialchars(json_encode([
                                    'num'       => $poNum,
                                    'source'    => $poSource,
                                    'amount'    => '+' . formatMoney($po['amount']),
                                    'currency'  => $mInfo[0],
                                    'provider'  => $mInfo[1],
                                    'status'    => $po['status'],
                                    'requested' => date('d M Y', strtotime($po['requested_at'])),
                                    'processed'    => !empty($po['processed_at']) ? date('d M Y', strtotime($po['processed_at'])) : null,
                                    'action_detail'=> $po['action_detail'] ?? '',
                                    'id'           => (int)$po['id'],
                                ]), ENT_QUOTES);
                            ?>
                            <tr class="pyo-row">
                                <td class="pyo-td-num">#<?= $poNum ?></td>
                                <td>
                                    <div class="pyo-source-ref"><?= htmlspecialchars($poSource) ?></div>
                                    <div class="pyo-source-type"><?= $po['challenge_type'] === 'one_step' ? '1-STEP' : '2-STEP' ?> · <?= formatMoneyShort($po['account_size'] ?? 0) ?></div>
                                </td>
                                <td class="pyo-td-amt green">+<?= formatMoney($po['amount']) ?></td>
                                <td>
                                    <div class="pyo-method-currency"><?= htmlspecialchars($mInfo[0]) ?></div>
                                    <div class="pyo-method-provider"><?= htmlspecialchars($mInfo[1]) ?></div>
                                </td>
                                <td><?= $pyoProgress($po['status'], $po['action_detail'] ?? '') ?></td>
                                <td class="pyo-td-date"><?= date('d M Y', strtotime($po['requested_at'])) ?></td>
                                <td class="pyo-td-action">
                                    <button class="pyo-act-btn pyo-act-view" type="button"
                                            data-payout="<?= $poJson ?>"
                                            onclick="PayoutDetailModal.open(this)"
                                            title="View Details">
                                        <?= pix('eye', 14, 14) ?>
                                    </button>
                                    <button class="pyo-act-btn pyo-act-dl" type="button"
                                            data-payout="<?= $poJson ?>"
                                            onclick="PayoutDetailModal.download(JSON.parse(this.dataset.payout))"
                                            title="Download Details">
                                        <?= pix('download', 14, 14) ?>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php if (count($payouts) > 5): ?>
                    <div class="pyo-pagination" id="pyoPagination">
                        <button class="wlt-pg-btn" id="pyoPrev" data-dir="-1">&#8592;</button>
                        <span class="wlt-pg-info" id="pyoPagInfo"></span>
                        <button class="wlt-pg-btn" id="pyoNext" data-dir="1">&#8594;</button>
                    </div>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="dash-empty">
                    <?= pix('dollar', 40, 40) ?>
                    <h3>No Payouts Yet</h3>
                    <p>Complete a funded challenge to request your first payout</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- ══ TAB: PROFILE ══ -->
            <div class="dash-tab" id="tab-settings">
                <div class="dash-profile-layout">

                    <!-- ── Left: User card ── -->
                    <div class="dash-profile-left">
                        <?php
                        $_profGrads = [
                            ['#10B981','#0EA5E9'],['#8B5CF6','#EC4899'],['#F59E0B','#EF4444'],
                            ['#06B6D4','#6366F1'],['#10B981','#84CC16'],['#F97316','#FBBF24'],
                            ['#6366F1','#A855F7'],['#EF4444','#F97316'],['#0EA5E9','#10B981'],
                            ['#EC4899','#8B5CF6'],['#14B8A6','#3B82F6'],['#A855F7','#06B6D4'],
                        ];
                        $_gIdx   = abs($userId) % count($_profGrads);
                        $_gStyle = 'background:linear-gradient(135deg,' . $_profGrads[$_gIdx][0] . ',' . $_profGrads[$_gIdx][1] . ')';
                    ?>
                    <div class="dash-user-card">
                            <div class="dash-user-card-av" style="<?= $_gStyle ?>"><?= $initials ?></div>
                            <div class="dash-user-card-name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
                            <div class="dash-user-card-email"><?= htmlspecialchars($profile['email'] ?? '') ?></div>
                            <div class="dash-user-card-badge <?= $kycClass[$kycStatus] ?>">
                                <?php if ($kycStatus === 'approved'): ?>
                                <?= pix('check', 11, 11) ?> Verified Trader
                                <?php elseif ($kycStatus === 'pending'): ?>
                                <?= pix('clock', 11, 11) ?> Under Review
                                <?php else: ?>
                                <?= pix('info', 11, 11) ?> Not Verified
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($user['created_at'])): ?>
                            <div class="dash-user-card-since">Member since <?= date('M Y', strtotime($user['created_at'])) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- ── Right: Sections ── -->
                    <div class="dash-profile-right">

                        <!-- Section: Personal Information -->
                        <div class="dash-psection" id="psec-profile">
                            <div class="dash-psection-head">
                                <?= pix('user', 16, 16) ?>
                                Personal Information
                            </div>
                            <div class="dash-psection-body">
                                <form id="profileForm" class="dash-form">
                                    <input type="hidden" name="csrf" value="<?= generateCsrf() ?>">
                                    <div class="dash-form-row">
                                        <div class="dash-form-group">
                                            <label>First Name <span class="req">*</span></label>
                                            <input type="text" name="first_name" value="<?= htmlspecialchars($profile['first_name'] ?? '') ?>" class="dash-input" required>
                                        </div>
                                        <div class="dash-form-group">
                                            <label>Last Name <span class="req">*</span></label>
                                            <input type="text" name="last_name" value="<?= htmlspecialchars($profile['last_name'] ?? '') ?>" class="dash-input" required>
                                        </div>
                                    </div>
                                    <div class="dash-form-row">
                                        <div class="dash-form-group">
                                            <label>Username <?php if (!empty($profile['username'])): ?><span class="dash-badge-set">✓ set</span><?php else: ?><span class="dash-badge-unset">not set</span><?php endif; ?></label>
                                            <input type="text" name="username" value="<?= htmlspecialchars($profile['username'] ?? '') ?>" class="dash-input" placeholder="your_pseudo" pattern="[a-zA-Z0-9_]{3,30}" maxlength="30">
                                            <span class="dash-form-hint">3–30 chars · letters, numbers, underscores · unique</span>
                                        </div>
                                        <div class="dash-form-group">
                                            <label>Email</label>
                                            <input type="email" value="<?= htmlspecialchars($profile['email'] ?? '') ?>" class="dash-input" disabled>
                                            <span class="dash-form-hint">Contact support to change email</span>
                                        </div>
                                    </div>
                                    <div class="dash-form-row">
                                        <div class="dash-form-group">
                                            <label>Phone</label>
                                            <input type="tel" name="phone" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>" class="dash-input" placeholder="+1 234 567 890">
                                        </div>
                                        <div class="dash-form-group">
                                            <label>Address</label>
                                            <input type="text" name="address" value="<?= htmlspecialchars($profile['address'] ?? '') ?>" class="dash-input" placeholder="Street address">
                                        </div>
                                    </div>
                                    <div class="dash-form-row">
                                        <div class="dash-form-group">
                                            <label>City</label>
                                            <input type="text" name="city" value="<?= htmlspecialchars($profile['city'] ?? '') ?>" class="dash-input">
                                        </div>
                                        <div class="dash-form-group">
                                            <label>Postal Code</label>
                                            <input type="text" name="zipcode" value="<?= htmlspecialchars($profile['zipcode'] ?? '') ?>" class="dash-input">
                                        </div>
                                    </div>
                                    <div class="dash-form-row">
                                        <div class="dash-form-group">
                                            <label>Country</label>
                                            <input type="text" name="country" value="<?= htmlspecialchars($profile['country'] ?? '') ?>" class="dash-input">
                                        </div>
                                        <div class="dash-form-group">
                                            <label>Region / State</label>
                                            <input type="text" name="region" value="<?= htmlspecialchars($profile['region'] ?? '') ?>" class="dash-input">
                                        </div>
                                    </div>
                                    <div class="dash-form-row">
                                        <div class="dash-form-group" style="grid-column:1/-1">
                                            <label>Preferred Time Zone</label>
                                            <select name="timezone" id="profileTimezone" class="dash-input">
                                                <option value="">— Select your time zone —</option>
                                                <optgroup label="UTC">
                                                    <option value="UTC">UTC — Coordinated Universal Time</option>
                                                </optgroup>
                                                <optgroup label="Americas">
                                                    <option value="America/Anchorage">America/Anchorage — AKST/AKDT</option>
                                                    <option value="America/Los_Angeles">America/Los_Angeles — PST/PDT</option>
                                                    <option value="America/Denver">America/Denver — MST/MDT</option>
                                                    <option value="America/Phoenix">America/Phoenix — MST</option>
                                                    <option value="America/Chicago">America/Chicago — CST/CDT</option>
                                                    <option value="America/New_York">America/New_York — EST/EDT</option>
                                                    <option value="America/Halifax">America/Halifax — AST/ADT</option>
                                                    <option value="America/St_Johns">America/St_Johns — NST/NDT</option>
                                                    <option value="America/Sao_Paulo">America/Sao_Paulo — BRT</option>
                                                    <option value="America/Argentina/Buenos_Aires">America/Buenos_Aires — ART</option>
                                                    <option value="America/Santiago">America/Santiago — CLT</option>
                                                    <option value="America/Bogota">America/Bogota — COT</option>
                                                    <option value="America/Lima">America/Lima — PET</option>
                                                    <option value="America/Caracas">America/Caracas — VET</option>
                                                    <option value="America/Mexico_City">America/Mexico_City — CST/CDT</option>
                                                    <option value="America/Toronto">America/Toronto — EST/EDT</option>
                                                    <option value="America/Vancouver">America/Vancouver — PST/PDT</option>
                                                </optgroup>
                                                <optgroup label="Europe">
                                                    <option value="Europe/London">Europe/London — GMT/BST</option>
                                                    <option value="Europe/Lisbon">Europe/Lisbon — WET/WEST</option>
                                                    <option value="Europe/Paris">Europe/Paris — CET/CEST</option>
                                                    <option value="Europe/Berlin">Europe/Berlin — CET/CEST</option>
                                                    <option value="Europe/Madrid">Europe/Madrid — CET/CEST</option>
                                                    <option value="Europe/Rome">Europe/Rome — CET/CEST</option>
                                                    <option value="Europe/Amsterdam">Europe/Amsterdam — CET/CEST</option>
                                                    <option value="Europe/Brussels">Europe/Brussels — CET/CEST</option>
                                                    <option value="Europe/Zurich">Europe/Zurich — CET/CEST</option>
                                                    <option value="Europe/Stockholm">Europe/Stockholm — CET/CEST</option>
                                                    <option value="Europe/Oslo">Europe/Oslo — CET/CEST</option>
                                                    <option value="Europe/Copenhagen">Europe/Copenhagen — CET/CEST</option>
                                                    <option value="Europe/Helsinki">Europe/Helsinki — EET/EEST</option>
                                                    <option value="Europe/Warsaw">Europe/Warsaw — CET/CEST</option>
                                                    <option value="Europe/Prague">Europe/Prague — CET/CEST</option>
                                                    <option value="Europe/Budapest">Europe/Budapest — CET/CEST</option>
                                                    <option value="Europe/Athens">Europe/Athens — EET/EEST</option>
                                                    <option value="Europe/Bucharest">Europe/Bucharest — EET/EEST</option>
                                                    <option value="Europe/Kiev">Europe/Kiev — EET/EEST</option>
                                                    <option value="Europe/Moscow">Europe/Moscow — MSK</option>
                                                    <option value="Europe/Istanbul">Europe/Istanbul — TRT</option>
                                                </optgroup>
                                                <optgroup label="Africa">
                                                    <option value="Africa/Casablanca">Africa/Casablanca — WET</option>
                                                    <option value="Africa/Lagos">Africa/Lagos — WAT</option>
                                                    <option value="Africa/Cairo">Africa/Cairo — EET</option>
                                                    <option value="Africa/Nairobi">Africa/Nairobi — EAT</option>
                                                    <option value="Africa/Johannesburg">Africa/Johannesburg — SAST</option>
                                                </optgroup>
                                                <optgroup label="Middle East">
                                                    <option value="Asia/Dubai">Asia/Dubai — GST</option>
                                                    <option value="Asia/Riyadh">Asia/Riyadh — AST</option>
                                                    <option value="Asia/Qatar">Asia/Qatar — AST</option>
                                                    <option value="Asia/Kuwait">Asia/Kuwait — AST</option>
                                                    <option value="Asia/Bahrain">Asia/Bahrain — AST</option>
                                                    <option value="Asia/Tehran">Asia/Tehran — IRST</option>
                                                    <option value="Asia/Beirut">Asia/Beirut — EET/EEST</option>
                                                    <option value="Asia/Jerusalem">Asia/Jerusalem — IST/IDT</option>
                                                </optgroup>
                                                <optgroup label="Asia">
                                                    <option value="Asia/Karachi">Asia/Karachi — PKT</option>
                                                    <option value="Asia/Kolkata">Asia/Kolkata — IST</option>
                                                    <option value="Asia/Colombo">Asia/Colombo — IST</option>
                                                    <option value="Asia/Dhaka">Asia/Dhaka — BST</option>
                                                    <option value="Asia/Kathmandu">Asia/Kathmandu — NPT</option>
                                                    <option value="Asia/Almaty">Asia/Almaty — ALMT</option>
                                                    <option value="Asia/Tashkent">Asia/Tashkent — UZT</option>
                                                    <option value="Asia/Rangoon">Asia/Rangoon — MMT</option>
                                                    <option value="Asia/Bangkok">Asia/Bangkok — ICT</option>
                                                    <option value="Asia/Ho_Chi_Minh">Asia/Ho_Chi_Minh — ICT</option>
                                                    <option value="Asia/Jakarta">Asia/Jakarta — WIB</option>
                                                    <option value="Asia/Shanghai">Asia/Shanghai — CST</option>
                                                    <option value="Asia/Hong_Kong">Asia/Hong_Kong — HKT</option>
                                                    <option value="Asia/Singapore">Asia/Singapore — SGT</option>
                                                    <option value="Asia/Taipei">Asia/Taipei — CST</option>
                                                    <option value="Asia/Kuala_Lumpur">Asia/Kuala_Lumpur — MYT</option>
                                                    <option value="Asia/Manila">Asia/Manila — PST</option>
                                                    <option value="Asia/Seoul">Asia/Seoul — KST</option>
                                                    <option value="Asia/Tokyo">Asia/Tokyo — JST</option>
                                                    <option value="Asia/Yakutsk">Asia/Yakutsk — YAKT</option>
                                                    <option value="Asia/Vladivostok">Asia/Vladivostok — VLAT</option>
                                                </optgroup>
                                                <optgroup label="Pacific &amp; Oceania">
                                                    <option value="Australia/Perth">Australia/Perth — AWST</option>
                                                    <option value="Australia/Darwin">Australia/Darwin — ACST</option>
                                                    <option value="Australia/Adelaide">Australia/Adelaide — ACST/ACDT</option>
                                                    <option value="Australia/Brisbane">Australia/Brisbane — AEST</option>
                                                    <option value="Australia/Sydney">Australia/Sydney — AEST/AEDT</option>
                                                    <option value="Australia/Melbourne">Australia/Melbourne — AEST/AEDT</option>
                                                    <option value="Pacific/Auckland">Pacific/Auckland — NZST/NZDT</option>
                                                    <option value="Pacific/Fiji">Pacific/Fiji — FJT</option>
                                                    <option value="Pacific/Honolulu">Pacific/Honolulu — HST</option>
                                                    <option value="Pacific/Guam">Pacific/Guam — ChST</option>
                                                </optgroup>
                                            </select>
                                            <span class="dash-form-hint">Used for your local clock in the topbar. Auto-detected from your country if not set.</span>
                                        </div>
                                    </div>
                                    <div id="profileMsg" class="dash-form-msg"></div>
                                    <button type="submit" class="dash-btn">Save Changes</button>
                                </form>
                            </div>
                        </div>

                        <!-- Section: Security -->
                        <div class="dash-psection" id="psec-security">
                            <div class="dash-psection-head">
                                <?= pix('shield', 16, 16) ?>
                                Security
                            </div>
                            <div class="dash-psection-body">

                                <!-- 2FA Banner (emphasized) -->
                                <div class="dash-2fa-banner">
                                    <div class="dash-2fa-left">
                                        <div class="dash-2fa-icon">
                                            <?= pix('lock', 22, 22) ?>
                                        </div>
                                        <div>
                                            <div class="dash-2fa-title">Two-Factor Authentication (2FA)</div>
                                            <div class="dash-2fa-desc">Protect your account with an authenticator app. We strongly recommend enabling 2FA — it is your first line of defense against unauthorized access.</div>
                                        </div>
                                    </div>
                                    <div class="dash-2fa-right">
                                        <span class="dash-2fa-status">Not enabled</span>
                                        <button class="dash-2fa-btn" onclick="alert('2FA setup coming soon. We will notify you by email when available.')">Enable 2FA</button>
                                    </div>
                                </div>

                                <!-- Change Password -->
                                <div class="dash-subsection-title">Change Password</div>
                                <form id="passwordForm" class="dash-form">
                                    <input type="hidden" name="csrf" value="<?= generateCsrf() ?>">
                                    <div class="dash-form-row">
                                        <div class="dash-form-group">
                                            <label>Current Password</label>
                                            <input type="password" name="current_password" class="dash-input" required>
                                        </div>
                                        <div class="dash-form-group">
                                            <label>New Password</label>
                                            <input type="password" name="new_password" class="dash-input" required minlength="8" placeholder="Min. 8 characters">
                                        </div>
                                    </div>
                                    <div class="dash-form-group" style="max-width:50%">
                                        <label>Confirm New Password</label>
                                        <input type="password" name="confirm_password" class="dash-input" required minlength="8">
                                    </div>
                                    <div id="passwordMsg" class="dash-form-msg"></div>
                                    <button type="submit" class="dash-btn">Update Password</button>
                                </form>
                            </div>
                        </div>

                        <!-- Section: Public Profile -->
                        <div class="dash-psection" id="psec-public">
                            <div class="dash-psection-head">
                                <?= pix('globe', 16, 16) ?>
                                Public Profile
                            </div>
                            <div class="dash-psection-body">
                                <input type="hidden" id="publicProfileCsrf" value="<?= generateCsrf() ?>">
                                <div class="dash-pub-row">
                                    <div class="dash-pub-info">
                                        <div class="dash-pub-title">Leaderboard Visibility</div>
                                        <div class="dash-pub-desc">When public, your trading stats, rank, and username appear in the community leaderboard. Your personal details remain private.</div>
                                    </div>
                                    <div class="dash-pub-ctrl">
                                        <button class="dash-pub-toggle<?= ($profile['is_public'] ?? 0) ? ' is-on' : '' ?>"
                                                id="pubProfileToggle"
                                                onclick="Dashboard.togglePublicProfile()"
                                                aria-pressed="<?= ($profile['is_public'] ?? 0) ? 'true' : 'false' ?>">
                                            <span class="dash-pub-toggle-knob"></span>
                                        </button>
                                        <span class="dash-pub-status" id="pubProfileStatus"><?= ($profile['is_public'] ?? 0) ? 'PUBLIC' : 'PRIVATE' ?></span>
                                    </div>
                                </div>
                                <div class="dash-pub-msg" id="pubProfileMsg"></div>
                            </div>
                        </div>

                        <!-- Section: KYC Documents -->
                        <div class="dash-psection" id="psec-verification">
                            <div class="dash-psection-head">
                                <?= pix('file', 16, 16) ?>
                                My Documents
                                <span class="dash-psection-badge <?= $kycClass[$kycStatus] ?>"><?= $kycLabels[$kycStatus] ?></span>
                            </div>
                            <div class="dash-psection-body">

                                <div class="dash-docs-grid">

                                    <!-- ID Document — Front -->
                                    <div class="dash-doc-card">
                                        <div class="dash-doc-card-head">
                                            <div>
                                                <div class="dash-doc-card-title">ID Document (Front)</div>
                                                <div class="dash-doc-card-sub">Front side of document</div>
                                            </div>
                                            <?php if ($kycStatus === 'approved'): ?>
                                            <span class="dash-doc-verified"><?= pix('check', 11, 11) ?> Verified</span>
                                            <?php elseif ($kycStatus === 'pending'): ?>
                                            <span class="dash-doc-pending">Under Review</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="dash-doc-accepted">
                                            <div class="dash-doc-accepted-title">Accepted documents:</div>
                                            <ul class="dash-doc-list">
                                                <li>National ID card</li>
                                                <li>Passport</li>
                                                <li>Residence permit</li>
                                            </ul>
                                        </div>
                                        <?php if ($kycStatus !== 'approved' && $kycStatus !== 'pending'): ?>
                                        <form class="dash-doc-form" enctype="multipart/form-data" onsubmit="Dashboard.submitKycDoc(event, 'id_front')">
                                            <input type="hidden" name="csrf" value="<?= generateCsrf() ?>">
                                            <input type="hidden" name="doc_type" value="id_front">
                                            <div class="dash-upload">
                                                <input type="file" name="kyc_document" accept=".jpg,.jpeg,.png,.pdf" required>
                                                <div class="dash-upload-label">
                                                    <?= pix('upload', 20, 20) ?>
                                                    <span>Click to upload or drag &amp; drop</span>
                                                    <span class="dash-upload-sub">JPG, PNG, PDF — Max 5MB</span>
                                                </div>
                                            </div>
                                            <div class="dash-doc-form-msg dash-form-msg"></div>
                                            <button type="submit" class="dash-btn dash-btn-sm">Submit</button>
                                        </form>
                                        <?php endif; ?>
                                    </div>

                                    <!-- ID Document — Back -->
                                    <div class="dash-doc-card">
                                        <div class="dash-doc-card-head">
                                            <div>
                                                <div class="dash-doc-card-title">ID Document (Back)</div>
                                                <div class="dash-doc-card-sub">Back side of document</div>
                                            </div>
                                            <?php if ($kycStatus === 'approved'): ?>
                                            <span class="dash-doc-verified"><?= pix('check', 11, 11) ?> Verified</span>
                                            <?php elseif ($kycStatus === 'pending'): ?>
                                            <span class="dash-doc-pending">Under Review</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="dash-doc-accepted">
                                            <div class="dash-doc-accepted-title">Required for:</div>
                                            <ul class="dash-doc-list">
                                                <li>National ID card</li>
                                                <li>Residence permit</li>
                                            </ul>
                                        </div>
                                        <?php if ($kycStatus !== 'approved' && $kycStatus !== 'pending'): ?>
                                        <form class="dash-doc-form" enctype="multipart/form-data" onsubmit="Dashboard.submitKycDoc(event, 'id_back')">
                                            <input type="hidden" name="csrf" value="<?= generateCsrf() ?>">
                                            <input type="hidden" name="doc_type" value="id_back">
                                            <div class="dash-upload">
                                                <input type="file" name="kyc_document" accept=".jpg,.jpeg,.png,.pdf" required>
                                                <div class="dash-upload-label">
                                                    <?= pix('upload', 20, 20) ?>
                                                    <span>Click to upload or drag &amp; drop</span>
                                                    <span class="dash-upload-sub">JPG, PNG, PDF — Max 5MB</span>
                                                </div>
                                            </div>
                                            <div class="dash-doc-form-msg dash-form-msg"></div>
                                            <button type="submit" class="dash-btn dash-btn-sm">Submit</button>
                                        </form>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Proof of Address -->
                                    <div class="dash-doc-card dash-doc-card-full">
                                        <div class="dash-doc-card-head">
                                            <div>
                                                <div class="dash-doc-card-title">Proof of Address</div>
                                                <div class="dash-doc-card-sub">Less than 90 days old</div>
                                            </div>
                                            <?php if ($kycStatus === 'approved'): ?>
                                            <span class="dash-doc-verified"><?= pix('check', 11, 11) ?> Verified</span>
                                            <?php elseif ($kycStatus === 'pending'): ?>
                                            <span class="dash-doc-pending">Under Review</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="dash-doc-accepted">
                                            <div class="dash-doc-accepted-title">Accepted documents:</div>
                                            <ul class="dash-doc-list">
                                                <li>Water / electricity / gas bill</li>
                                                <li>Bank statement</li>
                                            </ul>
                                            <div class="dash-doc-rejected-item">
                                                <?= pix('close', 12, 12) ?>
                                                Internet/phone bills not accepted
                                            </div>
                                        </div>
                                        <?php if ($kycStatus !== 'approved' && $kycStatus !== 'pending'): ?>
                                        <form class="dash-doc-form" enctype="multipart/form-data" onsubmit="Dashboard.submitKycDoc(event, 'proof_address')">
                                            <input type="hidden" name="csrf" value="<?= generateCsrf() ?>">
                                            <input type="hidden" name="doc_type" value="proof_address">
                                            <div class="dash-upload">
                                                <input type="file" name="kyc_document" accept=".jpg,.jpeg,.png,.pdf" required>
                                                <div class="dash-upload-label">
                                                    <?= pix('upload', 20, 20) ?>
                                                    <span>Click to upload or drag &amp; drop</span>
                                                    <span class="dash-upload-sub">JPG, PNG, PDF — Max 5MB</span>
                                                </div>
                                            </div>
                                            <div class="dash-doc-form-msg dash-form-msg"></div>
                                            <button type="submit" class="dash-btn dash-btn-sm">Submit</button>
                                        </form>
                                        <?php endif; ?>
                                    </div>

                                </div><!-- .dash-docs-grid -->
                            </div>
                        </div>

                        <!-- Section: Referral -->
                        <div class="dash-psection">
                            <div class="dash-psection-head">
                                <?= pix('users', 16, 16) ?>
                                Referral Program
                            </div>
                            <div class="dash-psection-body">
                                <p class="dash-psection-desc">Share your referral code and earn Doji Coins for every friend who purchases a challenge.</p>
                                <?php if (!empty($profile['referral_code'])): ?>
                                <div class="dash-referral-code">
                                    <span class="mono"><?= htmlspecialchars($profile['referral_code']) ?></span>
                                    <button class="dash-copy-btn" onclick="Dashboard.copyReferral('<?= htmlspecialchars($profile['referral_code']) ?>')">Copy</button>
                                </div>
                                <?php else: ?>
                                <p class="dash-form-hint">Your referral code will be generated after your first challenge purchase.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Section: Bank Accounts -->
                        <div class="dash-psection" id="psec-bank">
                            <div class="dash-psection-head">
                                <?= pix('briefcase', 16, 16) ?>
                                Bank Accounts
                                <span class="dash-psection-badge" style="background:rgba(255,255,255,0.06);color:var(--dash-text3);margin-left:auto">Coming Soon</span>
                            </div>
                            <div class="dash-psection-body">
                                <p class="dash-psection-desc">Connect your bank account to receive payouts via bank transfer. This feature will be available shortly.</p>
                            </div>
                        </div>

                        <!-- Section: Credit Cards -->
                        <div class="dash-psection" id="psec-cards">
                            <div class="dash-psection-head">
                                <?= pix('credit-card', 16, 16) ?>
                                Credit Cards
                                <span class="dash-psection-badge" style="background:rgba(255,255,255,0.06);color:var(--dash-text3);margin-left:auto">Coming Soon</span>
                            </div>
                            <div class="dash-psection-body">
                                <p class="dash-psection-desc">Manage your saved credit and debit cards for purchases. This feature will be available shortly.</p>
                            </div>
                        </div>

                        <!-- Section: Crypto Wallets -->
                        <div class="dash-psection" id="psec-crypto">
                            <div class="dash-psection-head">
                                <?= pix('dollar', 16, 16) ?>
                                Crypto Wallets
                                <span class="dash-psection-badge" style="background:rgba(255,255,255,0.06);color:var(--dash-text3);margin-left:auto">Coming Soon</span>
                            </div>
                            <div class="dash-psection-body">
                                <p class="dash-psection-desc">Add your crypto wallet addresses (BTC, ETH, USDT) to receive payout withdrawals. This feature will be available shortly.</p>
                            </div>
                        </div>

                        <!-- Section: Payment History -->
                        <div class="dash-psection" id="psec-payments">
                            <div class="dash-psection-head">
                                <?= pix('dollar', 16, 16) ?>
                                Payment History
                                <span class="dash-psection-badge" style="background:rgba(255,255,255,0.06);color:var(--dash-text3);margin-left:auto">Coming Soon</span>
                            </div>
                            <div class="dash-psection-body">
                                <p class="dash-psection-desc">View a full history of your purchases and transactions. This feature will be available shortly.</p>
                            </div>
                        </div>

                        <!-- Section: Discord -->
                        <div class="dash-psection" id="psec-discord">
                            <div class="dash-psection-head">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M20.317 4.492c-1.53-.69-3.17-1.2-4.885-1.49a.075.075 0 00-.079.036c-.21.369-.444.85-.608 1.23a18.566 18.566 0 00-5.487 0 12.36 12.36 0 00-.617-1.23A.077.077 0 008.562 3c-1.714.29-3.354.8-4.885 1.491a.07.07 0 00-.032.027C.533 9.093-.32 13.555.099 17.961a.08.08 0 00.031.055 20.03 20.03 0 005.993 2.98.078.078 0 00.084-.026 13.83 13.83 0 001.226-1.963.074.074 0 00-.041-.104 13.201 13.201 0 01-1.872-.878.075.075 0 01-.008-.125c.126-.093.252-.19.372-.287a.075.075 0 01.078-.01c3.927 1.764 8.18 1.764 12.061 0a.075.075 0 01.079.009c.12.098.245.195.372.288a.075.075 0 01-.006.125c-.598.344-1.22.635-1.873.877a.075.075 0 00-.041.105c.36.687.772 1.341 1.225 1.962a.077.077 0 00.084.028 19.963 19.963 0 006.002-2.981.076.076 0 00.032-.054c.5-5.094-.838-9.52-3.549-13.442a.06.06 0 00-.031-.028zM8.02 15.278c-1.182 0-2.157-1.069-2.157-2.38 0-1.312.956-2.38 2.157-2.38 1.21 0 2.176 1.077 2.157 2.38 0 1.312-.956 2.38-2.157 2.38zm7.975 0c-1.183 0-2.157-1.069-2.157-2.38 0-1.312.955-2.38 2.157-2.38 1.21 0 2.176 1.077 2.157 2.38 0 1.312-.946 2.38-2.157 2.38z"/></svg>
                                Discord
                            </div>
                            <div class="dash-psection-body">
                                <?php
                                $discMsg = $_GET['discord'] ?? '';
                                ?>
                                <?php if (!empty($profile['discord_id'])): ?>
                                <div class="disc-linked">
                                    <?= pix('check', 14, 14) ?>
                                    <span>Account linked — <strong><?= htmlspecialchars($discordUser['username'] ?? 'Discord ID ' . $profile['discord_id']) ?></strong></span>
                                </div>
                                <?php else: ?>
                                <?php if ($discMsg === 'already_linked'): ?>
                                <div class="disc-error"><?= pix('info', 13, 13) ?> This Discord account is already linked to another Doji Funding account.</div>
                                <?php elseif ($discMsg === 'token_error' || $discMsg === 'user_error'): ?>
                                <div class="disc-error"><?= pix('info', 13, 13) ?> Connection failed. Please try again.</div>
                                <?php endif; ?>
                                <p class="disc-desc">Link your Discord account to automatically receive your roles on the Doji Funding server.</p>
                                <div class="disc-steps">
                                    <div class="disc-step"><span class="disc-step-n">1</span>Click the button below</div>
                                    <div class="disc-step"><span class="disc-step-n">2</span>Authorize Doji Bot on Discord</div>
                                    <div class="disc-step"><span class="disc-step-n">3</span>You're redirected back — roles assigned instantly</div>
                                </div>
                                <div class="disc-actions">
                                    <a href="api/discord-auth.php" class="dash-btn disc-oauth-btn">
                                        <svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M20.317 4.492c-1.53-.69-3.17-1.2-4.885-1.49a.075.075 0 00-.079.036c-.21.369-.444.85-.608 1.23a18.566 18.566 0 00-5.487 0 12.36 12.36 0 00-.617-1.23A.077.077 0 008.562 3c-1.714.29-3.354.8-4.885 1.491a.07.07 0 00-.032.027C.533 9.093-.32 13.555.099 17.961a.08.08 0 00.031.055 20.03 20.03 0 005.993 2.98.078.078 0 00.084-.026 13.83 13.83 0 001.226-1.963.074.074 0 00-.041-.104 13.201 13.201 0 01-1.872-.878.075.075 0 01-.008-.125c.126-.093.252-.19.372-.287a.075.075 0 01.078-.01c3.927 1.764 8.18 1.764 12.061 0a.075.075 0 01.079.009c.12.098.245.195.372.288a.075.075 0 01-.006.125c-.598.344-1.22.635-1.873.877a.075.075 0 00-.041.105c.36.687.772 1.341 1.225 1.962a.077.077 0 00.084.028 19.963 19.963 0 006.002-2.981.076.076 0 00.032-.054c.5-5.094-.838-9.52-3.549-13.442a.06.06 0 00-.031-.028zM8.02 15.278c-1.182 0-2.157-1.069-2.157-2.38 0-1.312.956-2.38 2.157-2.38 1.21 0 2.176 1.077 2.157 2.38 0 1.312-.956 2.38-2.157 2.38zm7.975 0c-1.183 0-2.157-1.069-2.157-2.38 0-1.312.955-2.38 2.157-2.38 1.21 0 2.176 1.077 2.157 2.38 0 1.312-.946 2.38-2.157 2.38z"/></svg>
                                        Connect Discord
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Section: Feature Suggestions -->
                        <div class="dash-psection" id="psec-suggestions">
                            <div class="dash-psection-head">
                                <?= pix('info', 16, 16) ?>
                                Feature Suggestions
                                <span class="dash-psection-badge" style="background:rgba(255,255,255,0.06);color:var(--dash-text3);margin-left:auto">Coming Soon</span>
                            </div>
                            <div class="dash-psection-body">
                                <p class="dash-psection-desc">Submit ideas and vote on features you'd like to see in Doji Funding. Help us shape the platform. This feature will be available shortly.</p>
                            </div>
                        </div>

                        <!-- Section: Preferences -->
                        <div class="dash-psection" id="psec-preferences">
                            <div class="dash-psection-head">
                                <?= pix('wifi', 16, 16) ?>
                                Preferences
                                <span class="dash-psection-badge" style="background:rgba(255,255,255,0.06);color:var(--dash-text3);margin-left:auto">Coming Soon</span>
                            </div>
                            <div class="dash-psection-body">
                                <p class="dash-psection-desc">Customize your dashboard experience — notifications, language, display settings, and more. This feature will be available shortly.</p>
                            </div>
                        </div>

                    </div><!-- .dash-profile-right -->
                </div><!-- .dash-profile-layout -->
            </div>

            <!-- ══ TAB: CONFIGURATOR ══ -->
            <div class="dash-tab" id="tab-configurator">

                <!-- CHALLENGE TYPE TABS -->
                <div class="type-tabs">
                    <button class="type-tab disabled" disabled>
                        Instant Funding
                        <div class="type-tab-sub">Coming Soon</div>
                    </button>
                    <button class="type-tab active" id="tab-onestep" onclick="Configurator.setTab('onestep')">
                        1 Step
                        <div class="type-tab-sub">Fast Track</div>
                    </button>
                    <button class="type-tab" id="tab-twostep" onclick="Configurator.setTab('twostep')">
                        2 Step
                        <div class="type-tab-sub">Classic</div>
                    </button>
                </div>

                <!-- MODE PICKER -->
                <div class="mode-strip" id="modeStrip">
                    <div class="mode-strip-label">Quick Setup</div>
                    <div class="mode-cards">
                        <button class="mode-card" data-mode="cheap" onclick="Configurator.applyMode('cheap')">
                            <span class="mode-tag">BUDGET</span>
                            <span class="mode-name">Cheap</span>
                            <span class="mode-desc">Lowest entry price</span>
                        </button>
                        <button class="mode-card" data-mode="po" onclick="Configurator.applyMode('po')">
                            <span class="mode-tag">POWER</span>
                            <span class="mode-name">Pro</span>
                            <span class="mode-desc">Max split &amp; freedom</span>
                        </button>
                        <button class="mode-card" data-mode="beginner" onclick="Configurator.applyMode('beginner')">
                            <span class="mode-tag">EASY</span>
                            <span class="mode-name">Beginner</span>
                            <span class="mode-desc">Forgiving rules</span>
                        </button>
                        <div class="mode-card mode-card-presets" id="modeMyPresets" data-mode="mypresets" onclick="DashPresets.toggle(event)">
                            <span class="mode-tag">SAVED</span>
                            <span class="mode-name">My Presets</span>
                            <span class="mode-desc">Save &amp; load configs</span>
                            <div class="mode-presets-drop" onclick="event.stopPropagation()">
                                <div class="mp-save-row">
                                    <input class="mp-name-input" id="mpNameInput" placeholder="Name this config..." maxlength="50">
                                    <button class="mp-save-btn" onclick="DashPresets.save()">Save</button>
                                </div>
                                <div class="mp-list" id="mpList"><div class="mp-empty">No saved presets yet.</div></div>
                            </div>
                        </div>
                        <button class="mode-card mode-affiliate" data-mode="affiliate" id="modeAffiliate" onclick="Configurator.applyMode('affiliate')">
                            <span class="mode-tag">AFFILIATE</span>
                            <span class="mode-name">Affiliate</span>
                            <span class="mode-desc mode-desc-locked">Unlocks with sales</span>
                            <span class="mode-lock">
                                <?= pix('lock', 11, 11) ?>
                            </span>
                        </button>
                        <div class="mode-card mode-card-competitor" data-mode="competitor" onclick="Configurator.applyMode('competitor')">
                            <span class="mode-tag">COMPARE</span>
                            <span class="mode-name">Competitor</span>
                            <span class="mode-desc">Load a rival preset</span>
                            <div class="mode-competitor-drop" onclick="event.stopPropagation()">
                                <select class="preset-select" id="presetSelect" aria-label="Compare with other prop firms" onchange="this.classList.toggle('has-value',!!this.value);if(this.value){Configurator.loadPreset(this.value)}">
                                    <option value="">Compare with other firms...</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CONFIGURATOR LAYOUT -->
                <div class="cfg-layout">

                    <!-- LEFT PANEL: Parameters -->
                    <div class="cfg-panel">
                        <div class="cfg-header">
                            <h2 class="cfg-title">Challenge Configurator</h2>
                            <span class="cfg-badge" id="cfgBadge">FAST TRACK</span>
                        </div>
                        <div id="slidersContainer"></div>
                        <button class="reset-btn" onclick="Configurator.reset()">↺ Reset to Defaults</button>
                    </div>

                    <!-- RIGHT PANEL: Summary + Price -->
                    <div class="cfg-panel">
                        <div class="cfg-header">
                            <h2 class="cfg-title">Your Configuration</h2>
                        </div>
                        <div class="summary-box">
                            <div class="summary-title">Configuration Summary</div>
                            <div class="summary-grid" id="summaryGrid"></div>
                        </div>
                        <div class="price-box">
                            <div class="price-label">Total Price</div>
                            <div id="priceDisplay">
                                <div class="price-val" id="priceVal">$249</div>
                            </div>
                        </div>
                        <div class="promo-row">
                            <input class="promo-input" id="promoInput"
                                   placeholder="Enter promo code"
                                   onkeydown="if(event.key==='Enter')Configurator.applyPromo()">
                            <button class="promo-btn" id="promoBtn" onclick="Configurator.applyPromo()">Apply</button>
                        </div>
                        <div id="promoMsg"></div>
                        <button class="share-btn" onclick="Configurator.share()">
                            <?= pix('link', 14, 14) ?>
                            Share Configuration
                        </button>
                        <div id="shareMsg"></div>
                        <button class="purchase-btn" id="purchaseBtn" onclick="Configurator.purchase()">
                            Purchase Challenge — $249
                        </button>
                        <div class="note-box" id="noteBox">
                            <strong class="green">Note:</strong>
                            Consistency Rule and Payout Frequency apply to Funded accounts only.
                        </div>
                        <div class="note-box note-disclaimer">
                            Doji Funding® is a simulated trading platform designed for performance evaluation and skill development. No real capital is deployed or at risk. Program fees provide access to our simulation and assessment tools. Performance-based payouts are discretionary and subject to compliance with all program rules and Doji Funding®'s <a href="terms.php" class="disclaimer-link">Terms&nbsp;of&nbsp;Service</a>.
                        </div>
                    </div>
                </div>

                <!-- Hidden stubs keep configurator.js references valid -->
                <div id="objectivesSection" style="display:none">
                    <div id="objEvalCard"><div id="objEvalNum"></div><div id="objEvalTitle"></div><div id="objTargetBox"></div><div id="objDailyVal"></div><div id="objMaxVal"></div><div id="objChartDaily"><svg id="objChartDailySvg"></svg></div><div id="objChartMax"><svg id="objChartMaxSvg"></svg></div><div id="objGuidelines"></div><div id="objTags"></div><div id="objFlow"></div><div id="objLimitDaily"></div><div id="objLimitMax"></div></div>
                    <div id="objFundedCard"><div id="objFundedNum"></div><div id="objFDailyVal"></div><div id="objFMaxVal"></div><div id="objFConsVal"></div><div id="objChartFdaily"><svg id="objChartFdailySvg"></svg></div><div id="objChartFmax"><svg id="objChartFmaxSvg"></svg></div><div id="objFGuidelines"></div><div id="objRewards"></div></div>
                </div>

            </div>

            <!-- ══ TAB: STATISTICS ══ -->
            <!-- ══ TAB: STATISTICS ══ -->
            <div class="dash-tab" id="tab-statistics">
                <?php
                $statAccounts = array_map(function($c) use ($userId, $acctIndexMap) {
                    return [
                        'id'             => (int)$c['id'],
                        'ref'            => challengeAcctRef($c['type'], $c['account_size'], $userId, $acctIndexMap[(int)$c['id']] ?? 1),
                        'type'           => $c['type'],
                        'size'           => (float)$c['account_size'],
                        'profit'         => (float)$c['total_profit'],
                        'status'         => $c['status'],
                        'lots'           => (float)($c['lots_traded']      ?? 0),
                        'nTrades'        => (int)($c['total_trades']       ?? 0),
                        'nWins'          => (int)($c['winning_trades']     ?? 0),
                        'dailyLossPct'   => (float)($c['daily_loss']       ?? 5),
                        'maxLossPct'     => (float)($c['max_loss']         ?? 10),
                        'dailyLossType'  => $c['daily_loss_type']          ?? 'static',
                        'maxLossType'    => $c['max_loss_type']            ?? 'static',
                    ];
                }, $challenges);
                $challengeAssets = function_exists('getUserChallengeAssets') ? getUserChallengeAssets($userId) : [];
                ?>
                <script>
                window.DojiStatAccounts = <?= json_encode($statAccounts) ?>;
                window.DojiStatAssets   = <?= json_encode($challengeAssets) ?>;
                </script>

                <!-- Filter bar -->
                <?php
                $statDotClass = [
                    'active'  => 'stat-dot-amber',
                    'funded'  => 'stat-dot-green',
                    'passed'  => 'stat-dot-blue',
                    'failed'  => 'stat-dot-red',
                    'expired' => 'stat-dot-gray',
                ];
                ?>
                <div class="stat-filterbar">
                    <button class="stat-filter-btn stat-filter-active" data-filter="all">ALL</button>
                    <button class="stat-filter-btn" data-filter="evaluation">EVALUATION</button>
                    <button class="stat-filter-btn" data-filter="funded">FUNDED</button>
                    <?php if (!empty($challenges)): ?>
                    <div class="stat-filter-sep"></div>
                    <?php foreach ($challenges as $sc):
                        $dotCls = $statDotClass[$sc['status']] ?? 'stat-dot-gray';
                    ?>
                    <button class="stat-filter-btn" data-filter="acct-<?= (int)$sc['id'] ?>">
                        <span class="stat-acct-dot <?= $dotCls ?>"></span><?= htmlspecialchars(challengeAcctRef($sc['type'], $sc['account_size'], $userId, $acctIndexMap[(int)$sc['id']] ?? 1)) ?>
                    </button>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    <div class="stat-filter-spacer"></div>
                    <div class="stat-period-btns">
                        <button class="stat-period-btn" data-period="1">1D</button>
                        <button class="stat-period-btn" data-period="7">1W</button>
                        <button class="stat-period-btn" data-period="30">1M</button>
                        <button class="stat-period-btn" data-period="90">3M</button>
                        <button class="stat-period-btn stat-period-active" data-period="180">6M</button>
                        <button class="stat-period-btn" data-period="365">1Y</button>
                    </div>
                </div>

                <!-- KPI strip (8 draggable cards) -->
                <div class="stat-kpi-grid" id="statKpiGrid">

                    <div class="stat-kpi-card" draggable="true">
                        <div class="stat-kpi-drag">⠿</div>
                        <div class="stat-kpi-lbl">NET P&L</div>
                        <div class="stat-kpi-val green" id="skPnl">—</div>
                        <div class="stat-kpi-sub" id="skPnlSub">—</div>
                    </div>

                    <div class="stat-kpi-card" draggable="true">
                        <div class="stat-kpi-drag">⠿</div>
                        <div class="stat-kpi-lbl">TRADE WIN %</div>
                        <div class="kpi-gauge-body">
                            <div class="stat-kpi-val" id="skWr">—</div>
                            <div class="kpi-gauge-arc-col">
                                <svg viewBox="0 0 100 56" class="kpi-gauge-svg" aria-hidden="true">
                                    <defs>
                                        <filter id="gfWr" x="-30%" y="-60%" width="160%" height="220%">
                                            <feDropShadow dx="0" dy="1.5" stdDeviation="1.5" flood-color="#000" flood-opacity="0.55"/>
                                        </filter>
                                    </defs>
                                    <path class="kpi-gauge-track" d="M 10 52 A 40 40 0 0 1 50 12 A 40 40 0 0 1 90 52"/>
                                    <path class="kpi-gauge-green" id="skWrGaugeGn" d="M 10 52 A 40 40 0 0 1 50 12" filter="url(#gfWr)"/>
                                    <path class="kpi-gauge-red"   id="skWrGaugeRd" d="M 50 12 A 40 40 0 0 1 90 52" filter="url(#gfWr)"/>
                                    <path class="kpi-gauge-hl"    d="M 10 52 A 40 40 0 0 1 50 12 A 40 40 0 0 1 90 52"/>
                                    <circle class="kpi-gauge-dot" id="skWrGaugeDt" cx="50" cy="12" r="3"/>
                                </svg>
                                <div class="kpi-gauge-counts">
                                    <span class="kpi-gc-win"  id="skWrWins">—</span>
                                    <span class="kpi-gc-be"   id="skWrBe">—</span>
                                    <span class="kpi-gc-loss" id="skWrLosses">—</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="stat-kpi-card" draggable="true">
                        <div class="stat-kpi-drag">⠿</div>
                        <div class="stat-kpi-lbl">PROFIT FACTOR</div>
                        <div class="kpi-gauge-body">
                            <div class="stat-kpi-val" id="skPf">—</div>
                            <div class="kpi-circ-ring">
                                <svg viewBox="0 0 100 100" class="kpi-circ-svg" aria-hidden="true">
                                    <defs>
                                        <filter id="gfPf" x="-20%" y="-20%" width="140%" height="140%">
                                            <feDropShadow dx="0" dy="0" stdDeviation="2" flood-color="#000" flood-opacity="0.65"/>
                                        </filter>
                                    </defs>
                                    <circle class="kpi-circ-track" cx="50" cy="50" r="38"/>
                                    <circle class="kpi-circ-red"   id="skPfCircRd" cx="50" cy="50" r="38"
                                            stroke-dasharray="119.38 238.76" stroke-dashoffset="-59.69"/>
                                    <circle class="kpi-circ-green" id="skPfCircGn" cx="50" cy="50" r="38"
                                            stroke-dasharray="119.38 238.76" stroke-dashoffset="-59.69"/>
                                    <circle class="kpi-circ-hl"    cx="50" cy="50" r="38"
                                            stroke-dasharray="29.85 208.91" stroke-dashoffset="14.92"/>
                                </svg>
                            </div>
                        </div>
                        <div class="kpi-gauge-counts">
                            <span class="kpi-gc-win"  id="skPfGrossWin">—</span>
                            <span class="kpi-gc-loss" id="skPfGrossLoss">—</span>
                        </div>
                    </div>

                    <div class="stat-kpi-card" draggable="true">
                        <div class="stat-kpi-drag">⠿</div>
                        <div class="stat-kpi-lbl">DAY WIN %</div>
                        <div class="kpi-gauge-body">
                            <div class="stat-kpi-val" id="skDwr">—</div>
                            <div class="kpi-gauge-arc-col">
                                <svg viewBox="0 0 100 56" class="kpi-gauge-svg" aria-hidden="true">
                                    <defs>
                                        <filter id="gfDwr" x="-30%" y="-60%" width="160%" height="220%">
                                            <feDropShadow dx="0" dy="1.5" stdDeviation="1.5" flood-color="#000" flood-opacity="0.55"/>
                                        </filter>
                                    </defs>
                                    <path class="kpi-gauge-track" d="M 10 52 A 40 40 0 0 1 50 12 A 40 40 0 0 1 90 52"/>
                                    <path class="kpi-gauge-green" id="skDwrGaugeGn" d="M 10 52 A 40 40 0 0 1 50 12" filter="url(#gfDwr)"/>
                                    <path class="kpi-gauge-red"   id="skDwrGaugeRd" d="M 50 12 A 40 40 0 0 1 90 52" filter="url(#gfDwr)"/>
                                    <path class="kpi-gauge-hl"    d="M 10 52 A 40 40 0 0 1 50 12 A 40 40 0 0 1 90 52"/>
                                    <circle class="kpi-gauge-dot" id="skDwrGaugeDt" cx="50" cy="12" r="3"/>
                                </svg>
                                <div class="kpi-gauge-counts">
                                    <span class="kpi-gc-win"  id="skDwrWins">—</span>
                                    <span class="kpi-gc-be"   id="skDwrBe">—</span>
                                    <span class="kpi-gc-loss" id="skDwrLosses">—</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="stat-kpi-card" draggable="true">
                        <div class="stat-kpi-drag">⠿</div>
                        <div class="stat-kpi-lbl">SHARPE RATIO</div>
                        <div class="stat-kpi-val" id="skSharpe">—</div>
                        <div class="stat-kpi-sub" id="skSharpeSub">—</div>
                    </div>

                    <div class="stat-kpi-card" draggable="true">
                        <div class="stat-kpi-drag">⠿</div>
                        <div class="stat-kpi-lbl">MAX DRAWDOWN</div>
                        <div class="stat-kpi-val amber" id="skMdd">—</div>
                        <div class="stat-seg-bar" id="skMddSegs"><div class="stat-seg"></div><div class="stat-seg"></div><div class="stat-seg"></div><div class="stat-seg"></div><div class="stat-seg"></div><div class="stat-seg"></div><div class="stat-seg"></div><div class="stat-seg"></div><div class="stat-seg"></div><div class="stat-seg"></div></div>
                        <div class="stat-kpi-sub" id="skMddSub">—</div>
                    </div>

                    <div class="stat-kpi-card" draggable="true">
                        <div class="stat-kpi-drag">⠿</div>
                        <div class="stat-kpi-lbl">EXPECTANCY</div>
                        <div class="stat-kpi-val" id="skExpect">—</div>
                        <div class="stat-kpi-sub" id="skExpectSub">EXPECTED PER TRADE</div>
                    </div>

                    <div class="stat-kpi-card" draggable="true">
                        <div class="stat-kpi-drag">⠿</div>
                        <div class="stat-kpi-lbl">CONSISTENCY</div>
                        <div class="stat-kpi-val" id="skConsist">—</div>
                        <div class="stat-seg-bar" id="skConsistSegs"><div class="stat-seg"></div><div class="stat-seg"></div><div class="stat-seg"></div><div class="stat-seg"></div><div class="stat-seg"></div><div class="stat-seg"></div><div class="stat-seg"></div><div class="stat-seg"></div><div class="stat-seg"></div><div class="stat-seg"></div></div>
                        <div class="stat-kpi-sub" id="skConsistSub">—</div>
                    </div>

                    <div class="stat-kpi-card" draggable="true">
                        <div class="stat-kpi-drag">⠿</div>
                        <div class="stat-kpi-lbl">CALMAR RATIO</div>
                        <div class="stat-kpi-val" id="skCalmar">—</div>
                        <div class="stat-kpi-sub" id="skCalmarSub">ANNUAL / MAX DD</div>
                    </div>

                    <div class="stat-kpi-card" draggable="true">
                        <div class="stat-kpi-drag">⠿</div>
                        <div class="stat-kpi-lbl">TOTAL LOTS</div>
                        <div class="stat-kpi-val" id="skLots">—</div>
                        <div class="stat-kpi-sub" id="skLotsSub">—</div>
                        <div class="stat-kpi-sub" id="skFeesSub">—</div>
                    </div>

                    <div class="stat-kpi-card" draggable="true">
                        <div class="stat-kpi-drag">⠿</div>
                        <div class="stat-kpi-lbl">AVG WIN/LOSS</div>
                        <div class="stat-kpi-val" id="skRR">—</div>
                        <div class="kpi-rr-bar">
                            <div class="kpi-rr-green" id="skRRBarGreen"><span id="skRRAvgWin">—</span></div>
                            <div class="kpi-rr-red"   id="skRRBarRed"><span id="skRRAvgLoss">—</span></div>
                        </div>
                    </div>

                    <div class="stat-kpi-card" draggable="true">
                        <div class="stat-kpi-drag">⠿</div>
                        <div class="stat-kpi-lbl">BEST TRADE</div>
                        <div class="stat-kpi-val green" id="skBest">—</div>
                        <div class="stat-kpi-sub" id="skBestSub">—</div>
                    </div>

                    <div class="stat-kpi-card" draggable="true">
                        <div class="stat-kpi-drag">⠿</div>
                        <div class="stat-kpi-lbl">WORST TRADE</div>
                        <div class="stat-kpi-val red" id="skWorst">—</div>
                        <div class="stat-kpi-sub" id="skWorstSub">—</div>
                    </div>

                    <div class="stat-kpi-card stat-kpi-wide" draggable="true">
                        <div class="stat-kpi-drag">⠿</div>
                        <div class="stat-kpi-lbl">BIAS</div>
                        <div class="bias-db">
                            <div class="bias-db-head">
                                <span class="bias-db-dir" id="skBiasDir">—</span>
                                <span class="bias-db-split" id="skBiasSplit">—</span>
                            </div>
                            <div class="bias-db-bar" id="skBiasBar"></div>
                            <div class="bias-db-foot">
                                <div class="bias-db-side">
                                    <div class="stat-bias-count-grid">
                                        <span class="stat-bias-count-n" id="skBiasShortN" style="color:#D71921">—</span>
                                        <span class="stat-bias-count-wr" id="skBiasShortWR" style="color:#D71921">—</span>
                                        <span class="stat-bias-count-lbl">SHORT</span>
                                        <span class="stat-bias-count-wr-lbl">WIN RATE</span>
                                    </div>
                                </div>
                                <div class="bias-db-side bias-db-side-r">
                                    <div class="stat-bias-count-grid bias-db-grid-r">
                                        <span class="stat-bias-count-wr" id="skBiasLongWR" style="color:#10B981">—</span>
                                        <span class="stat-bias-count-n" id="skBiasLongN" style="color:#10B981">—</span>
                                        <span class="stat-bias-count-wr-lbl">WIN RATE</span>
                                        <span class="stat-bias-count-lbl">LONG</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div><!-- /.stat-kpi-grid -->

                <!-- Charts grid (8 draggable cards) -->
                <div class="stat-chart-grid" id="statChartGrid">

                    <!-- 1. Equity Curve + Drawdown — combined full width -->
                    <div class="stat-chart-card stat-chart-full" draggable="true">
                        <div class="stat-chart-head">
                            <span class="stat-chart-title">EQUITY CURVE</span>
                            <span class="stat-chart-hint">DRAWDOWN PERIODS</span>
                            <div class="stat-chart-controls">
                                <button class="stat-ctrl-btn stat-ctrl-active" data-granularity="daily">DAILY</button>
                                <button class="stat-ctrl-btn" data-granularity="weekly">WEEKLY</button>
                            </div>
                        </div>
                        <div class="stat-chart-body" style="height:310px"><canvas id="chartEquity"></canvas></div>
                        <div class="stat-eq-footer">
                            <div class="stat-eq-stat">
                                <div class="stat-eq-lbl">CURRENT P&L</div>
                                <div class="stat-eq-val" id="eqCurrent">—</div>
                            </div>
                            <div class="stat-eq-stat">
                                <div class="stat-eq-lbl">PEAK</div>
                                <div class="stat-eq-val green" id="eqHigh">—</div>
                            </div>
                            <div class="stat-eq-stat">
                                <div class="stat-eq-lbl">TROUGH</div>
                                <div class="stat-eq-val red" id="eqLow">—</div>
                            </div>
                            <div class="stat-eq-stat">
                                <div class="stat-eq-lbl">TOTAL TRADES</div>
                                <div class="stat-eq-val" id="eqTrades">—</div>
                            </div>
                            <div class="stat-eq-stat">
                                <div class="stat-eq-lbl">TOTAL LOTS</div>
                                <div class="stat-eq-val" id="eqLots">—</div>
                            </div>
                            <div class="stat-eq-stat">
                                <div class="stat-eq-lbl">EXPECTANCY</div>
                                <div class="stat-eq-val" id="eqExpect">—</div>
                            </div>
                            <div id="eqStopSection" style="display:none">
                                <div class="stat-eq-stat">
                                    <div class="stat-eq-lbl">DAILY STOP <span class="stat-eq-stop-type" id="eqDailyStopType">—</span></div>
                                    <div class="stat-eq-val amber" id="eqDailyDist">—</div>
                                </div>
                                <div class="stat-eq-stat">
                                    <div class="stat-eq-lbl">MAX STOP <span class="stat-eq-stop-type" id="eqMaxStopType">—</span></div>
                                    <div class="stat-eq-val" id="eqMaxDist">—</div>
                                </div>
                            </div>
                        </div>
                        <div class="stat-kpi-drag">⠿</div>
                    </div>

                    <!-- 2. Monthly P&L -->
                    <div class="stat-chart-card" draggable="true">
                        <div class="stat-chart-head"><span class="stat-chart-title">MONTHLY P&L</span></div>
                        <div class="stat-chart-body" style="height:178px"><canvas id="chartMonthly"></canvas></div>
                        <div class="stat-kpi-drag">⠿</div>
                    </div>

                    <!-- 3. Trade Outcome Donut -->
                    <div class="stat-chart-card" draggable="true">
                        <div class="stat-chart-head"><span class="stat-chart-title">TRADE OUTCOME</span></div>
                        <div class="stat-chart-body" style="height:178px"><canvas id="chartWinloss"></canvas></div>
                        <div class="stat-kpi-drag">⠿</div>
                    </div>

                    <!-- 4. Trading DNA Radar -->
                    <div class="stat-chart-card" draggable="true">
                        <div class="stat-chart-head">
                            <span class="stat-chart-title">TRADING DNA</span>
                            <span class="stat-chart-hint">TRADER PROFILE</span>
                        </div>
                        <div class="stat-chart-body" style="height:170px"><canvas id="chartDna"></canvas></div>
                        <div class="stat-dna-result stat-dna-result-v2">
                            <div class="stat-dna-v2-score">
                                <div class="stat-dna-v2-lbl">YOUR DNA SCORE</div>
                                <div class="stat-dna-v2-val" id="statDnaScoreVal">—</div>
                            </div>
                            <div class="stat-dna-v2-bar-col">
                                <div class="stat-dna-v2-track">
                                    <div class="stat-dna-v2-marker" id="statDnaBarMarker" style="left:2%"></div>
                                </div>
                                <div class="stat-dna-v2-ticks">
                                    <span>0</span><span>20</span><span>40</span><span>60</span><span>80</span><span>100</span>
                                </div>
                            </div>
                        </div>
                        <div class="stat-kpi-drag">⠿</div>
                    </div>

                    <!-- 5. Session Performance -->
                    <div class="stat-chart-card" draggable="true">
                        <div class="stat-chart-head"><span class="stat-chart-title">SESSION PERFORMANCE</span></div>
                        <div class="stat-chart-body" style="height:196px"><canvas id="chartSession"></canvas></div>
                        <div class="stat-kpi-drag">⠿</div>
                    </div>

                    <!-- 6. Day of Week P&L -->
                    <div class="stat-chart-card" draggable="true">
                        <div class="stat-chart-head"><span class="stat-chart-title">P&L BY DAY OF WEEK</span></div>
                        <div class="stat-chart-body" style="height:174px"><canvas id="chartDow"></canvas></div>
                        <div class="stat-kpi-drag">⠿</div>
                    </div>

                    <!-- 7. Trade Duration Profile -->
                    <div class="stat-chart-card" draggable="true">
                        <div class="stat-chart-head"><span class="stat-chart-title">TRADE DURATION PROFILE</span></div>
                        <div class="stat-chart-body" style="height:174px"><canvas id="chartDuration"></canvas></div>
                        <div class="stat-kpi-drag">⠿</div>
                    </div>

                    <!-- 8. Traded Assets — full width -->
                    <div class="stat-chart-card stat-chart-full" draggable="true">
                        <div class="stat-chart-head">
                            <span class="stat-chart-title">TRADED ASSETS</span>
                            <span class="stat-chart-hint">P&amp;L · WIN RATE · LOTS · TRADES</span>
                        </div>
                        <div id="statAssetBars" class="stat-asset-wrap"></div>
                        <div class="stat-kpi-drag">⠿</div>
                    </div>

                </div><!-- /.stat-chart-grid -->
            </div>

            <!-- ══ TAB: COMPETITIONS ══ -->
            <div class="dash-tab" id="tab-competitions">
                <script>
                window.DojiCompData        = <?= json_encode(array_values($competitions_all), JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>;
                window.DojiCompJoined      = <?= json_encode($comp_joined_ids) ?>;
                window.DojiCompLeaderboards= <?= json_encode($comp_leaderboards, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>;
                </script>

                <?php
                /* Pick the active (live or upcoming) free and paid competition */
                $_compFree = null; $_compPaid = null;
                foreach ($competitions_all as $_c) {
                    if (!$_compFree && $_c['type']==='free' && $_c['status']!=='ended') $_compFree = $_c;
                    if (!$_compPaid && $_c['type']==='paid' && $_c['status']!=='ended') $_compPaid = $_c;
                    if ($_compFree && $_compPaid) break;
                }
                $__blocks = array_filter([$_compFree, $_compPaid]);
                ?>
                <?php
                $_cLiveCount = count(array_filter($competitions_all, fn($_c) => $_c['status']==='live'));
                $_cUpCount   = count(array_filter($competitions_all, fn($_c) => $_c['status']==='upcoming'));
                $_cParts     = array_sum(array_column($competitions_all, 'participants'));
                $_cTotal     = count($competitions_all);
                ?>
                <div class="comp-stats-strip">
                    <div class="comp-stat" style="--cs-c:#10B981">
                        <div class="comp-stat-val"><?= $_cLiveCount ?></div>
                        <div class="comp-stat-lbl">LIVE NOW</div>
                    </div>
                    <div class="comp-stat" style="--cs-c:#D4A017">
                        <div class="comp-stat-val"><?= $_cUpCount ?></div>
                        <div class="comp-stat-lbl">UPCOMING</div>
                    </div>
                    <div class="comp-stat" style="--cs-c:#0EA5E9">
                        <div class="comp-stat-val"><?= number_format($_cParts) ?></div>
                        <div class="comp-stat-lbl">PARTICIPANTS</div>
                    </div>
                    <div class="comp-stat" style="--cs-c:#8B5CF6">
                        <div class="comp-stat-val"><?= $_cTotal ?></div>
                        <div class="comp-stat-lbl">TOTAL EDITIONS</div>
                    </div>
                </div>

                <!-- Two competition blocks -->
                <div class="comp-blocks" id="compBlocks">
                <?php foreach ($__blocks as $_b):
                    $_bCdEnd = $_b['status']==='live' ? $_b['ends'] : ($_b['status']==='upcoming' ? $_b['starts'] : '');
                    $_bCdLbl = $_b['status']==='live' ? 'ENDING IN' : ($_b['status']==='upcoming' ? 'STARTS IN' : 'ENDED');
                    $_bCdVal = $_b['status']==='live' ? compTimeLeft($_b['ends']) : ($_b['status']==='upcoming' ? compTimeUntil($_b['starts']) : '00:00:00');
                    $_bJoined = in_array($_b['id'], $comp_joined_ids);
                ?>
                <?php $_bAccent = $_b['status']==='live' ? '#10B981' : ($_b['status']==='upcoming' ? '#D4A017' : '#6B7280'); ?>
                <div class="comp-block comp-block--<?= $_b['status'] ?>" style="--comp-accent:<?= $_bAccent ?>">
                    <!-- Header -->
                    <div class="comp-block-hdr">
                        <div class="comp-block-hdr-left">
                            <div class="comp-block-name"><?= htmlspecialchars($_b['name']) ?></div>
                            <div class="comp-block-edition"><?= htmlspecialchars($_b['edition']) ?><?= $_bJoined ? ' <span class="comp-block-joined-tag">JOINED</span>' : '' ?></div>
                        </div>
                        <span class="comp-status comp-status--<?= $_b['status'] ?>">
                            <span class="comp-status-dot comp-status-dot--<?= $_b['status'] ?>"></span>
                            <?= $_b['status']==='live' ? 'ONGOING' : strtoupper($_b['status']) ?>
                        </span>
                    </div>
                    <!-- Body -->
                    <div class="comp-block-body">
                        <!-- Left: countdown clock + entry type -->
                        <div class="comp-block-left">
                            <div class="comp-block-cd-wrap"<?= $_bCdEnd ? ' data-flip-end="'.htmlspecialchars($_bCdEnd).'"' : '' ?> data-flip-status="<?= $_b['status'] ?>">
                                <div class="comp-block-cd comp-block-cd--<?= $_b['status'] ?>"><?= $_bCdVal ?></div>
                                <div class="comp-block-cd-lbl"><?= $_bCdLbl ?></div>
                            </div>
                            <div class="comp-block-entry-badge comp-block-entry-badge--<?= $_b['type'] ?>">
                                <?= $_b['type']==='free' ? 'FREE' : '$'.number_format($_b['entry'], 0) ?>
                            </div>
                            <div class="comp-block-entry-sub"><?= $_b['type']==='free' ? 'FREE ENTRY' : 'ENTRY FEE' ?></div>
                        </div>
                        <!-- Separator -->
                        <div class="comp-block-sep"></div>
                        <!-- Right: info rows -->
                        <div class="comp-block-right">
                            <div class="comp-block-row"><span class="comp-block-lbl">STARTS</span><span class="comp-block-val"><?= date('M d, Y', strtotime($_b['starts'])) ?></span></div>
                            <div class="comp-block-row"><span class="comp-block-lbl">ENDS</span><span class="comp-block-val"><?= date('M d, Y', strtotime($_b['ends'])) ?></span></div>
                            <div class="comp-block-row"><span class="comp-block-lbl">ENTRY</span><span class="comp-block-val <?= $_b['type']==='free' ? 'comp-entry--free' : '' ?>"><?= $_b['type']==='free' ? 'FREE' : '$'.number_format($_b['entry'],2) ?></span></div>
                            <div class="comp-block-row"><span class="comp-block-lbl">PARTICIPANTS</span><span class="comp-block-val"><?= number_format($_b['participants']) ?></span></div>
                            <div class="comp-block-row"><span class="comp-block-lbl">PLATFORM</span><span class="comp-block-val"><?= htmlspecialchars($_b['platform']) ?></span></div>
                            <div class="comp-block-row"><span class="comp-block-lbl">ORGANIZER</span><span class="comp-block-val"><?= htmlspecialchars($_b['organizer']) ?></span></div>
                            <div class="comp-block-row"><span class="comp-block-lbl">STATUS</span><span class="comp-block-val"><span class="comp-status comp-status--<?= $_b['status'] ?>"><span class="comp-status-dot comp-status-dot--<?= $_b['status'] ?>"></span><?= $_b['status']==='live' ? 'ONGOING' : strtoupper($_b['status']) ?></span></span></div>
                        </div>
                    </div>
                    <!-- Footer: 3 buttons -->
                    <div class="comp-block-footer">
                        <button class="comp-btn comp-btn--primary" onclick="CompTab.openView(<?= $_b['id'] ?>)">
                            <?= pix('eye', 11, 11) ?>
                            VIEW
                        </button>
                        <button class="comp-btn" onclick="CompTab.openPrizepool(<?= $_b['id'] ?>)">
                            <?= pix('dollar', 11, 11) ?>
                            PRIZEPOOL
                        </button>
                        <button class="comp-btn" onclick="CompTab.openInfo(<?= $_b['id'] ?>)">
                            <?= pix('info', 11, 11) ?>
                            INFO
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
                </div><!-- /.comp-blocks -->

                <?php
                /* ── Drawer data ── */
                $_dJoined  = array_values(array_filter($competitions_all, function($_c) use ($comp_joined_ids) {
                    return in_array($_c['id'], $comp_joined_ids);
                }));
                $_dExpired = array_values(array_filter($competitions_all, function($_c) {
                    return $_c['status'] === 'ended';
                }));
                // Sort expired: free first, then paid
                usort($_dExpired, function($a,$b){ return strcmp($a['type'],$b['type']); });
                $__chevron = pix('chevron-down', 12, 12, 'comp-drawer-chev');
                ?>
                <!-- Collapsible drawers -->
                <div class="comp-drawers" id="compDrawers">

                <?php if (!empty($_dJoined)): ?>
                <div class="comp-drawer" id="drawerJoined">
                    <button class="comp-drawer-btn" onclick="CompTab.toggleDrawer('drawerJoined')">
                        <?= pix('user', 12, 12) ?>
                        JOINED
                        <span class="comp-drawer-count"><?= count($_dJoined) ?></span>
                        <?= $__chevron ?>
                    </button>
                    <div class="comp-drawer-body" hidden>
                        <div class="comp-cgrid">
                        <?php foreach ($_dJoined as $_dc):
                            $_dcEntry  = $_dc['type']==='free' ? 'FREE' : '$'.number_format($_dc['entry'],0);
                            $_dcStLbl  = $_dc['status']==='live' ? 'ONGOING' : ($_dc['status']==='upcoming' ? 'UPCOMING' : 'ENDED');
                            $_dcColor  = $_dc['status']==='live' ? '#10B981' : ($_dc['status']==='upcoming' ? '#D4A017' : '#6B7280');
                        ?>
                        <div class="comp-card" style="--comp-c:<?= $_dcColor ?>">
                            <div class="comp-card-top">
                                <span class="comp-status comp-status--<?= $_dc['status'] ?>"><span class="comp-status-dot comp-status-dot--<?= $_dc['status'] ?>"></span><?= $_dcStLbl ?></span>
                                <span class="comp-block-joined-tag" style="margin-left:auto">JOINED</span>
                            </div>
                            <div class="comp-card-name"><?= htmlspecialchars($_dc['name']) ?></div>
                            <div class="comp-card-edition"><?= htmlspecialchars($_dc['edition']) ?></div>
                            <div class="comp-card-meta">
                                <span class="comp-card-tag<?= $_dc['type']==='free' ? ' comp-card-tag--free' : '' ?>"><?= $_dcEntry ?></span>
                                <span class="comp-card-dot">·</span>
                                <span><?= number_format($_dc['participants']) ?> TRADERS</span>
                                <span class="comp-card-dot">·</span>
                                <span><?= htmlspecialchars($_dc['platform']) ?></span>
                            </div>
                            <div class="comp-card-foot">
                                <span class="comp-card-dates"><?= date('M d', strtotime($_dc['starts'])) ?> – <?= date('M d, Y', strtotime($_dc['ends'])) ?></span>
                                <button class="comp-btn comp-btn--sm comp-btn--primary" onclick="CompTab.openView(<?= $_dc['id'] ?>)">VIEW</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($_dExpired)): ?>
                <div class="comp-drawer" id="drawerExpired">
                    <button class="comp-drawer-btn" onclick="CompTab.toggleDrawer('drawerExpired')">
                        <?= pix('clock', 12, 12) ?>
                        EXPIRED
                        <span class="comp-drawer-count"><?= count($_dExpired) ?></span>
                        <?= $__chevron ?>
                    </button>
                    <div class="comp-drawer-body" hidden>
                        <div class="comp-cgrid">
                        <?php foreach ($_dExpired as $_dc):
                            $_dcEntry  = $_dc['type']==='free' ? 'FREE' : '$'.number_format($_dc['entry'],0);
                            $_dcJoined = in_array($_dc['id'], $comp_joined_ids);
                        ?>
                        <div class="comp-card" style="--comp-c:#6B7280">
                            <div class="comp-card-top">
                                <span class="comp-status comp-status--ended"><span class="comp-status-dot comp-status-dot--ended"></span>ENDED</span>
                                <?php if ($_dcJoined): ?><span class="comp-block-joined-tag" style="margin-left:auto">JOINED</span><?php endif; ?>
                            </div>
                            <div class="comp-card-name"><?= htmlspecialchars($_dc['name']) ?></div>
                            <div class="comp-card-edition"><?= htmlspecialchars($_dc['edition']) ?></div>
                            <div class="comp-card-meta">
                                <span class="comp-card-tag<?= $_dc['type']==='free' ? ' comp-card-tag--free' : '' ?>"><?= $_dcEntry ?></span>
                                <span class="comp-card-dot">·</span>
                                <span><?= number_format($_dc['participants']) ?> TRADERS</span>
                                <span class="comp-card-dot">·</span>
                                <span><?= htmlspecialchars($_dc['platform']) ?></span>
                            </div>
                            <div class="comp-card-foot">
                                <span class="comp-card-dates"><?= date('M d', strtotime($_dc['starts'])) ?> – <?= date('M d, Y', strtotime($_dc['ends'])) ?></span>
                                <button class="comp-btn comp-btn--sm comp-btn--primary" onclick="CompTab.openView(<?= $_dc['id'] ?>)">VIEW</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                </div><!-- /.comp-drawers -->

                <!-- Detail view (injected by CompTab.openView) -->
                <div id="compDetailView" hidden></div>

            </div>

            <!-- ══ TAB: LEADERBOARD ══ -->
            <div class="dash-tab" id="tab-leaderboard">

                <!-- Header -->
                <div class="lb-hdr">
                    <div class="lb-hdr-left">
                        <div class="lb-live-dot"></div>
                        <div>
                            <div class="lb-hdr-ttl">GLOBAL TRADER RANKINGS</div>
                            <div class="lb-hdr-sub" id="lbHdrSub">TOP 50 FUNDED ACCOUNTS · RANKED BY PROFIT % · UPDATED DAILY</div>
                        </div>
                    </div>
                </div>

                <!-- Stats strip -->
                <div class="lb-stats">
                    <div class="lb-stat lb-stat--1">
                        <div class="lb-stat-lbl">HIGHEST TOTAL PAYOUT</div>
                        <div class="lb-stat-val" id="lbStatTotalPayout">—</div>
                        <div class="lb-stat-sub" id="lbStatTotalPayoutName">—</div>
                    </div>
                    <div class="lb-stat lb-stat--2">
                        <div class="lb-stat-lbl">LONGEST FUNDED</div>
                        <div class="lb-stat-val" id="lbStatDuration">—</div>
                        <div class="lb-stat-sub" id="lbStatDurationName">—</div>
                    </div>
                    <div class="lb-stat lb-stat--3">
                        <div class="lb-stat-lbl">HIGHEST PAYOUT</div>
                        <div class="lb-stat-val" id="lbStatHighPayout">—</div>
                        <div class="lb-stat-sub" id="lbStatHighPayoutName">—</div>
                    </div>
                    <div class="lb-stat lb-stat--4">
                        <div class="lb-stat-lbl">HIGHEST PAYOUT COUNT</div>
                        <div class="lb-stat-val" id="lbStatPayoutCount">—</div>
                        <div class="lb-stat-sub" id="lbStatPayoutCountName">—</div>
                    </div>
                </div>

                <!-- Tier filter -->
                <div class="lb-filter-bar lb-filter-bar--tier">
                    <span class="lb-filter-lbl">TIER</span>
                    <div class="lb-pills" id="lbTierPills">
                        <button class="lb-pill lb-tier-pill active" data-tier="all">All Tiers</button>
                        <button class="lb-pill lb-tier-pill" data-tier="legend"   style="--tier-c:#EC4899">LEGEND</button>
                        <button class="lb-pill lb-tier-pill" data-tier="masters"  style="--tier-c:#F97316">MASTERS</button>
                        <button class="lb-pill lb-tier-pill" data-tier="diamond"  style="--tier-c:#06B6D4">DIAMOND</button>
                        <button class="lb-pill lb-tier-pill" data-tier="platinum" style="--tier-c:#8B5CF6">PLATINUM</button>
                        <button class="lb-pill lb-tier-pill" data-tier="gold"     style="--tier-c:#D4A843">GOLD</button>
                        <button class="lb-pill lb-tier-pill" data-tier="silver"   style="--tier-c:#9CA3AF">SILVER</button>
                        <button class="lb-pill lb-tier-pill" data-tier="bronze"   style="--tier-c:#CD7F32">BRONZE</button>
                    </div>
                </div>

                <!-- Size filter -->
                <div class="lb-filter-bar">
                    <span class="lb-filter-lbl">ACCOUNT SIZE</span>
                    <div class="lb-pills" id="lbSizePills">
                        <button class="lb-pill active" data-size="all">All Sizes</button>
                        <button class="lb-pill" data-size="5000">$5K</button>
                        <button class="lb-pill" data-size="10000">$10K</button>
                        <button class="lb-pill" data-size="15000">$15K</button>
                        <button class="lb-pill" data-size="20000">$20K</button>
                        <button class="lb-pill" data-size="25000">$25K</button>
                        <button class="lb-pill" data-size="30000">$30K</button>
                        <button class="lb-pill" data-size="35000">$35K</button>
                        <button class="lb-pill" data-size="40000">$40K</button>
                        <button class="lb-pill" data-size="45000">$45K</button>
                        <button class="lb-pill" data-size="50000">$50K</button>
                        <button class="lb-pill" data-size="60000">$60K</button>
                        <button class="lb-pill" data-size="70000">$70K</button>
                        <button class="lb-pill" data-size="80000">$80K</button>
                        <button class="lb-pill" data-size="90000">$90K</button>
                        <button class="lb-pill" data-size="100000">$100K</button>
                        <button class="lb-pill" data-size="125000">$125K</button>
                        <button class="lb-pill" data-size="150000">$150K</button>
                        <button class="lb-pill" data-size="175000">$175K</button>
                        <button class="lb-pill" data-size="200000">$200K</button>
                    </div>
                </div>

                <!-- Table -->
                <div class="lb-scroll">
                    <table class="lb-table">
                        <thead>
                            <tr>
                                <th class="lb-th lb-th-rank">RANK</th>
                                <th class="lb-th lb-th-trader">TRADER</th>
                                <th class="lb-th">COUNTRY</th>
                                <th class="lb-th lb-td-tier">TIER</th>
                                <th class="lb-th lb-th-r lb-th-score">SCORE</th>
                                <th class="lb-th lb-th-r">PROFIT</th>
                                <th class="lb-th lb-th-r">PROFIT %</th>
                                <th class="lb-th lb-th-r">WIN RATE</th>
                                <th class="lb-th">ASSET</th>
                                <th class="lb-th lb-th-r">AVG. WIN</th>
                                <th class="lb-th lb-th-r">AVG. LOSS</th>
                                <th class="lb-th lb-th-r">AVG. HOLD</th>
                                <th class="lb-th lb-th-r">AVG. R:R</th>
                                <th class="lb-th lb-th-r">TRADES</th>
                            </tr>
                        </thead>
                        <tbody id="lbBody"></tbody>
                    </table>
                    <div class="lb-empty" id="lbEmpty" style="display:none">[ NO TRADERS FOR THIS ACCOUNT SIZE ]</div>
                </div>

                <!-- My rank bar (visible when user is filtered out) -->
                <div class="lb-my-bar" id="lbMyBar" style="display:none"></div>

            </div>

            <!-- ══ TAB: CERTIFICATES ══ -->
            <!-- ══ TAB: CERTIFICATES ══ -->
            <div class="dash-tab" id="tab-certificates">
                <?php
                $certEvals   = array_values(array_filter($challenges, fn($c) => in_array($c['status'], ['passed', 'funded'])));
                $certFunded  = array_values(array_filter($challenges, fn($c) => $c['status'] === 'funded'));
                $certDone    = array_values(array_filter($payouts ?? [], fn($p) => $p['status'] === 'completed'));
                $certTotal   = array_sum(array_column($certDone, 'amount'));
                $certCompEnd = array_values(array_filter($competitions_all, fn($c) => in_array($c['id'], $comp_joined_ids) && $c['status'] === 'ended'));
                $certCount   = count($certEvals) + count($certFunded) + count($certDone) + count($certCompEnd) + 1;
                ?>

                <!-- Header -->
                <div class="cert-hdr">
                    <div class="cert-hdr-left">
                        <div class="cert-hdr-title">ACHIEVEMENT CERTIFICATES</div>
                        <div class="cert-hdr-sub">Official documentation of your trading milestones</div>
                    </div>
                    <div class="cert-hdr-count">
                        <span class="cert-hdr-count-val"><?= $certCount ?></span>
                        <span class="cert-hdr-count-lbl">CERTIFICATES</span>
                    </div>
                </div>

                <!-- ── EVALUATIONS ── -->
                <?php if (!empty($certEvals)): ?>
                <div class="cert-section">
                    <div class="cert-section-hdr">
                        <span class="cert-section-dot" style="background:#10B981"></span>
                        EVALUATIONS
                        <span class="cert-section-count"><?= count($certEvals) ?></span>
                    </div>
                    <div class="cert-grid">
                        <?php foreach ($certEvals as $c):
                            $szLbl  = '$' . number_format((int)$c['account_size']);
                            $tyLbl  = $c['type'] === 'one_step' ? '1-STEP' : '2-STEP';
                            $dtLbl  = date('d M Y', strtotime($c['created_at'] ?? 'now'));
                            $cid    = 'EVAL-' . str_pad($c['id'], 4, '0', STR_PAD_LEFT);
                        ?>
                        <div class="cert-card" style="--cert-accent:#10B981">
                            <div class="cert-card-top">
                                <span class="cert-badge" style="color:#10B981;border-color:rgba(16,185,129,.28);background:rgba(16,185,129,.07)">
                                    <?= pix('check', 9, 9) ?>
                                    EVALUATION PASSED
                                </span>
                                <span class="cert-card-id"><?= $cid ?></span>
                            </div>
                            <div class="cert-card-val"><?= formatMoneyShort($c['account_size']) ?></div>
                            <div class="cert-card-type"><?= $tyLbl ?> EVALUATION</div>
                            <div class="cert-card-meta">
                                <span><?= strtoupper($c['platform']) ?></span>
                                <span class="cert-meta-sep">·</span>
                                <span><?= $dtLbl ?></span>
                            </div>
                            <div class="cert-card-actions">
                                <button class="cert-btn cert-btn--share" onclick="CertTab.share('eval',<?= $c['id'] ?>,'<?= addslashes($szLbl) ?> Evaluation Passed','<?= addslashes($tyLbl) ?> · <?= addslashes(strtoupper($c['platform'])) ?> · <?= addslashes($dtLbl) ?>')">
                                    <?= pix('share', 11, 11) ?>
                                    SHARE
                                </button>
                                <button class="cert-btn cert-btn--pdf" onclick="CertTab.download('eval',<?= $c['id'] ?>,'<?= addslashes($szLbl) ?> Evaluation Passed','<?= addslashes($tyLbl) ?> · <?= addslashes(strtoupper($c['platform'])) ?> · <?= addslashes($dtLbl) ?>')">
                                    <?= pix('download', 11, 11) ?>
                                    DOWNLOAD PDF
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- ── FUNDED ACCOUNTS ── -->
                <?php if (!empty($certFunded)): ?>
                <div class="cert-section">
                    <div class="cert-section-hdr">
                        <span class="cert-section-dot" style="background:#0EA5E9"></span>
                        FUNDED ACCOUNTS
                        <span class="cert-section-count"><?= count($certFunded) ?></span>
                    </div>
                    <div class="cert-grid">
                        <?php foreach ($certFunded as $c):
                            $szLbl  = '$' . number_format((int)$c['account_size']);
                            $tyLbl  = $c['type'] === 'one_step' ? '1-STEP' : '2-STEP';
                            $pfmt   = ($c['total_profit'] >= 0 ? '+' : '') . formatMoney($c['total_profit']);
                            $dtLbl  = date('d M Y', strtotime($c['created_at'] ?? 'now'));
                            $cid    = 'FUND-' . str_pad($c['id'], 4, '0', STR_PAD_LEFT);
                        ?>
                        <div class="cert-card" style="--cert-accent:#0EA5E9">
                            <div class="cert-card-top">
                                <span class="cert-badge" style="color:#0EA5E9;border-color:rgba(14,165,233,.28);background:rgba(14,165,233,.07)">
                                    <?= pix('layers', 9, 9) ?>
                                    FUNDED ACCOUNT
                                </span>
                                <span class="cert-card-id"><?= $cid ?></span>
                            </div>
                            <div class="cert-card-val"><?= formatMoneyShort($c['account_size']) ?></div>
                            <div class="cert-card-type"><?= $tyLbl ?> FUNDED</div>
                            <div class="cert-card-meta">
                                <span><?= strtoupper($c['platform']) ?></span>
                                <span class="cert-meta-sep">·</span>
                                <span class="cert-meta-green"><?= $pfmt ?></span>
                            </div>
                            <div class="cert-card-actions">
                                <button class="cert-btn cert-btn--share" onclick="CertTab.share('funded',<?= $c['id'] ?>,'<?= addslashes($szLbl) ?> Funded Account','<?= addslashes($tyLbl) ?> · P&amp;L <?= addslashes($pfmt) ?>')">
                                    <?= pix('share', 11, 11) ?>
                                    SHARE
                                </button>
                                <button class="cert-btn cert-btn--pdf" onclick="CertTab.download('funded',<?= $c['id'] ?>,'<?= addslashes($szLbl) ?> Funded Account','<?= addslashes($tyLbl) ?> · <?= addslashes(strtoupper($c['platform'])) ?> · P&amp;L <?= addslashes($pfmt) ?>')">
                                    <?= pix('download', 11, 11) ?>
                                    DOWNLOAD PDF
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- ── COMPETITIONS ── -->
                <?php if (!empty($certCompEnd)): ?>
                <div class="cert-section">
                    <div class="cert-section-hdr">
                        <span class="cert-section-dot" style="background:#F59E0B"></span>
                        COMPETITIONS
                        <span class="cert-section-count"><?= count($certCompEnd) ?></span>
                    </div>
                    <div class="cert-grid">
                        <?php foreach ($certCompEnd as $comp):
                            $compLb  = $comp_leaderboards[$comp['id']] ?? [];
                            $myRank  = null;
                            foreach ($compLb as $entry) { if (!empty($entry['me'])) { $myRank = $entry['rank']; break; } }
                            $sfx     = $myRank ? ($myRank===1?'ST':($myRank===2?'ND':($myRank===3?'RD':'TH'))) : '';
                            $edLbl   = htmlspecialchars($comp['edition']);
                            $dtLbl   = date('d M Y', strtotime($comp['ends']));
                            $cid     = 'COMP-' . str_pad($comp['id'], 4, '0', STR_PAD_LEFT);
                        ?>
                        <div class="cert-card" style="--cert-accent:#F59E0B">
                            <div class="cert-card-top">
                                <span class="cert-badge" style="color:#F59E0B;border-color:rgba(245,158,11,.28);background:rgba(245,158,11,.07)">
                                    <?= pix('medal', 9, 9) ?>
                                    COMPETITION
                                </span>
                                <span class="cert-card-id"><?= $cid ?></span>
                            </div>
                            <?php if ($myRank): ?>
                            <div class="cert-card-val cert-rank-val"><?= $myRank ?><sup class="cert-rank-sfx"><?= $sfx ?></sup></div>
                            <div class="cert-card-type">PLACE · <?= count($compLb) ?> TRADERS</div>
                            <?php else: ?>
                            <div class="cert-card-val" style="font-size:22px">PARTICIPANT</div>
                            <div class="cert-card-type"><?= count($compLb) ?> TRADERS</div>
                            <?php endif; ?>
                            <div class="cert-card-meta">
                                <span><?= $edLbl ?></span>
                                <span class="cert-meta-sep">·</span>
                                <span><?= $dtLbl ?></span>
                            </div>
                            <div class="cert-card-actions">
                                <button class="cert-btn cert-btn--share" onclick="CertTab.share('comp',<?= $comp['id'] ?>,'<?= addslashes($comp['name']) ?>','<?= addslashes($edLbl) ?><?= $myRank ? ' · Rank #'.$myRank : '' ?>')">
                                    <?= pix('share', 11, 11) ?>
                                    SHARE
                                </button>
                                <button class="cert-btn cert-btn--pdf" onclick="CertTab.download('comp',<?= $comp['id'] ?>,'<?= addslashes($comp['name']) ?>','<?= addslashes($edLbl) ?><?= $myRank ? ' · Rank #'.$myRank : '' ?>')">
                                    <?= pix('download', 11, 11) ?>
                                    DOWNLOAD PDF
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- ── PAYOUTS ── -->
                <?php if (!empty($certDone)): ?>
                <div class="cert-section">
                    <div class="cert-section-hdr">
                        <span class="cert-section-dot" style="background:#8B5CF6"></span>
                        PAYOUTS
                        <span class="cert-section-count"><?= count($certDone) ?></span>
                    </div>
                    <div class="cert-grid">
                        <?php foreach ($certDone as $p):
                            $amtLbl = '$' . number_format($p['amount'], 0);
                            $acLbl  = '$' . number_format((int)$p['account_size']);
                            $dtLbl  = date('d M Y', strtotime($p['processed_at'] ?? $p['requested_at']));
                            $tyLbl  = ($p['challenge_type'] ?? '') === 'one_step' ? '1-STEP' : '2-STEP';
                            $mtLbl  = strtoupper(str_replace('_', ' ', $p['method'] ?? ''));
                            $cid    = 'PAY-' . str_pad($p['id'], 4, '0', STR_PAD_LEFT);
                        ?>
                        <div class="cert-card" style="--cert-accent:#8B5CF6">
                            <div class="cert-card-top">
                                <span class="cert-badge" style="color:#8B5CF6;border-color:rgba(139,92,246,.28);background:rgba(139,92,246,.07)">
                                    <?= pix('dollar', 9, 9) ?>
                                    PAYOUT COMPLETED
                                </span>
                                <span class="cert-card-id"><?= $cid ?></span>
                            </div>
                            <div class="cert-card-val"><?= $amtLbl ?></div>
                            <div class="cert-card-type"><?= $tyLbl ?> · <?= $acLbl ?> ACCOUNT</div>
                            <div class="cert-card-meta">
                                <span><?= $mtLbl ?></span>
                                <span class="cert-meta-sep">·</span>
                                <span><?= $dtLbl ?></span>
                            </div>
                            <div class="cert-card-actions">
                                <button class="cert-btn cert-btn--share" onclick="CertTab.share('payout',<?= $p['id'] ?>,'Payout <?= addslashes($amtLbl) ?>','<?= addslashes($amtLbl) ?> received on <?= addslashes($dtLbl) ?>')">
                                    <?= pix('share', 11, 11) ?>
                                    SHARE
                                </button>
                                <button class="cert-btn cert-btn--pdf" onclick="CertTab.download('payout',<?= $p['id'] ?>,'Payout <?= addslashes($amtLbl) ?>','<?= addslashes($tyLbl) ?> · <?= addslashes($acLbl) ?> · <?= addslashes($dtLbl) ?>')">
                                    <?= pix('download', 11, 11) ?>
                                    DOWNLOAD PDF
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- ── LIFETIME PAYOUT ── -->
                <div class="cert-section">
                    <?php
                    $ltLocked = $certTotal <= 0;
                    $ltAccent = $ltLocked ? 'rgba(255,255,255,0.25)' : $gradeColor;
                    ?>
                    <div class="cert-section-hdr">
                        <span class="cert-section-dot" style="background:<?= $ltAccent ?>"></span>
                        LIFETIME PAYOUT
                        <span class="cert-section-count">1</span>
                    </div>
                    <div class="cert-grid">
                        <div class="cert-card<?= $ltLocked ? ' cert-card--locked' : '' ?>" style="--cert-accent:<?= $ltAccent ?>">
                            <div class="cert-card-top">
                                <span class="cert-badge" style="color:<?= $ltAccent ?>;border-color:<?= $ltLocked ? 'rgba(255,255,255,0.12)' : 'rgba(16,185,129,.28)' ?>;background:<?= $ltLocked ? 'rgba(255,255,255,0.04)' : 'rgba(16,185,129,.07)' ?>">
                                    <?= pix('star', 9, 9) ?>
                                    LIFETIME PAYOUT
                                </span>
                                <span class="cert-card-id">LT-0001</span>
                            </div>
                            <?php if ($ltLocked): ?>
                            <div class="cert-card-val" style="font-size:22px;opacity:.35">LOCKED</div>
                            <div class="cert-card-type" style="opacity:.35">NO COMPLETED PAYOUTS</div>
                            <?php else: ?>
                            <div class="cert-card-val">$<?= number_format($certTotal, 0) ?></div>
                            <div class="cert-card-type"><?= $gradeLetter ?> · <?= $gradeLabel ?></div>
                            <?php endif; ?>
                            <div class="cert-card-meta">
                                <span><?= count($certDone) ?> PAYOUT<?= count($certDone)!==1?'S':'' ?> COMPLETED</span>
                                <?php if (!$ltLocked): ?>
                                <span class="cert-meta-sep">·</span>
                                <span>GRADE <?= $gradeLetter ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="cert-card-actions">
                                <button class="cert-btn cert-btn--share" <?= $ltLocked?'disabled':'' ?>
                                    onclick="CertTab.share('lifetime',0,'Lifetime Payout $<?= number_format($certTotal,0) ?>','Grade <?= $gradeLetter ?> — <?= addslashes($gradeLabel) ?> · <?= count($certDone) ?> payouts')">
                                    <?= pix('share', 11, 11) ?>
                                    SHARE
                                </button>
                                <button class="cert-btn cert-btn--pdf" <?= $ltLocked?'disabled':'' ?>
                                    onclick="CertTab.download('lifetime',0,'Lifetime Payout $<?= number_format($certTotal,0) ?>','Grade <?= $gradeLetter ?> · <?= addslashes($gradeLabel) ?>')">
                                    <?= pix('download', 11, 11) ?>
                                    DOWNLOAD PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ══ TAB: CALENDAR ══ -->
            <div class="dash-tab" id="tab-calendar">
            <div class="cal-split-layout">

                <!-- ══ LEFT: TRADING JOURNAL ══ -->
                <div class="cal-split-left">
                <!-- ══ TRADING JOURNAL ══ -->
                <div class="cal-section-hdr">
                    <div>
                        <div class="cal-section-title">TRADING JOURNAL</div>
                        <div class="cal-section-sub">[ DEMO DATA — REAL TRADES FEED COMING SOON ]</div>
                    </div>
                </div>

                <!-- Filter bar -->
                <div class="cal-filterbar">
                    <button class="cal-filter-btn cal-filter-active" data-filter="all">ALL</button>
                    <button class="cal-filter-btn" data-filter="evaluation">EVALUATION</button>
                    <button class="cal-filter-btn" data-filter="funded">FUNDED</button>
                    <?php if (!empty($challenges)): ?>
                    <div class="cal-filter-sep"></div>
                    <?php foreach ($challenges as $sc):
                        $dotCls = $statDotClass[$sc['status']] ?? 'stat-dot-gray';
                    ?>
                    <button class="cal-filter-btn" data-filter="acct-<?= (int)$sc['id'] ?>">
                        <span class="stat-acct-dot <?= $dotCls ?>"></span><?= htmlspecialchars(challengeAcctRef($sc['type'], $sc['account_size'], $userId, $acctIndexMap[(int)$sc['id']] ?? 1)) ?>
                    </button>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Monthly KPI strip -->
                <div class="cal-kpis">
                    <div class="cal-kpi">
                        <div class="cal-kpi-lbl">MONTHLY P&amp;L</div>
                        <div class="cal-kpi-val" id="calKpiPnl">—</div>
                    </div>
                    <div class="cal-kpi">
                        <div class="cal-kpi-lbl">WIN RATE</div>
                        <div class="cal-kpi-val" id="calKpiWr">—</div>
                    </div>
                    <div class="cal-kpi">
                        <div class="cal-kpi-lbl">BEST DAY</div>
                        <div class="cal-kpi-val" id="calKpiBest">—</div>
                    </div>
                    <div class="cal-kpi">
                        <div class="cal-kpi-lbl">WORST DAY</div>
                        <div class="cal-kpi-val" id="calKpiWorst">—</div>
                    </div>
                    <div class="cal-kpi">
                        <div class="cal-kpi-lbl">TRADING DAYS</div>
                        <div class="cal-kpi-val" id="calKpiDays">—</div>
                    </div>
                    <div class="cal-kpi">
                        <div class="cal-kpi-lbl">TOTAL TRADES</div>
                        <div class="cal-kpi-val" id="calKpiTrades">—</div>
                    </div>
                </div>

                <!-- Month navigation -->
                <div class="cal-month-nav">
                    <button class="cal-nav-btn" id="calPrev">&#9668;</button>
                    <div class="cal-month-title" id="calMonthTitle">—</div>
                    <button class="cal-nav-btn" id="calNext">&#9658;</button>
                </div>

                <!-- Day-of-week header -->
                <div class="cal-dow-hdr">
                    <div class="cal-dow">MON</div>
                    <div class="cal-dow">TUE</div>
                    <div class="cal-dow">WED</div>
                    <div class="cal-dow">THU</div>
                    <div class="cal-dow">FRI</div>
                    <div class="cal-dow cal-dow--we">SAT</div>
                    <div class="cal-dow cal-dow--we">SUN</div>
                    <div class="cal-dow cal-dow--wk">WEEK</div>
                </div>

                <!-- Calendar grid (rendered by calendar.js) -->
                <div class="cal-grid" id="calGrid"></div>

                <!-- Day detail panel -->
                <div class="cal-detail" id="calDetail" style="display:none">
                    <div class="cal-detail-hdr">
                        <div class="cal-detail-title" id="calDetailTitle">—</div>
                        <button class="cal-detail-close" id="calDetailClose">&#215;</button>
                    </div>
                    <div class="cal-detail-list" id="calDetailList"></div>
                    <div class="cal-detail-summary" id="calDetailSummary"></div>

                    <!-- Journal section -->
                    <div class="cal-journal">
                        <div class="cal-journal-hdr">
                            <span class="cal-journal-lbl">JOURNAL</span>
                            <span class="cal-journal-saved" id="calJournalSaved"></span>
                        </div>
                        <div class="cal-journal-moods">
                            <!-- Mood 1 — Terrible -->
                            <button class="cal-mood-btn" data-mood="1" title="Terrible">
                                <svg viewBox="0 0 22 22" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="9.5"/>
                                    <circle cx="8" cy="9" r="0.8" fill="currentColor" stroke="none"/>
                                    <circle cx="14" cy="9" r="0.8" fill="currentColor" stroke="none"/>
                                    <path d="M6.5 15 Q11 11 15.5 15"/>
                                </svg>
                            </button>
                            <!-- Mood 2 — Bad -->
                            <button class="cal-mood-btn" data-mood="2" title="Bad">
                                <svg viewBox="0 0 22 22" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="9.5"/>
                                    <circle cx="8" cy="9" r="0.8" fill="currentColor" stroke="none"/>
                                    <circle cx="14" cy="9" r="0.8" fill="currentColor" stroke="none"/>
                                    <path d="M7.5 14.5 Q11 12.5 14.5 14.5"/>
                                </svg>
                            </button>
                            <!-- Mood 3 — Neutral -->
                            <button class="cal-mood-btn" data-mood="3" title="Neutral">
                                <svg viewBox="0 0 22 22" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="9.5"/>
                                    <circle cx="8" cy="9" r="0.8" fill="currentColor" stroke="none"/>
                                    <circle cx="14" cy="9" r="0.8" fill="currentColor" stroke="none"/>
                                    <path d="M7.5 13.5 L14.5 13.5"/>
                                </svg>
                            </button>
                            <!-- Mood 4 — Good -->
                            <button class="cal-mood-btn" data-mood="4" title="Good">
                                <svg viewBox="0 0 22 22" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="9.5"/>
                                    <circle cx="8" cy="9" r="0.8" fill="currentColor" stroke="none"/>
                                    <circle cx="14" cy="9" r="0.8" fill="currentColor" stroke="none"/>
                                    <path d="M7.5 13 Q11 15.5 14.5 13"/>
                                </svg>
                            </button>
                            <!-- Mood 5 — Great -->
                            <button class="cal-mood-btn" data-mood="5" title="Great">
                                <svg viewBox="0 0 22 22" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="9.5"/>
                                    <path d="M6.5 9 Q8 7.5 9.5 9"/>
                                    <path d="M12.5 9 Q14 7.5 15.5 9"/>
                                    <path d="M6 13 Q11 17.5 16 13"/>
                                </svg>
                            </button>
                        </div>
                        <textarea class="cal-journal-note" id="calJournalNote" rows="3" placeholder="WRITE YOUR TRADING NOTES FOR THIS DAY..."></textarea>
                        <div class="cal-journal-footer">
                            <button class="cal-journal-save-btn" id="calJournalSave">SAVE</button>
                        </div>
                    </div>
                </div>

                </div><!-- /cal-split-left -->

                <!-- ══ RIGHT: ECONOMIC CALENDAR ══ -->
                <div class="cal-split-right">
                <div class="econ-cal" id="econCal">

                    <!-- Header: title + week navigation -->
                    <div class="econ-cal-hdr">
                        <span class="econ-cal-title">ECONOMIC CALENDAR</span>
                        <div class="econ-week-nav">
                            <button class="econ-nav-btn" id="econPrevWeek">&#9668;</button>
                            <span class="econ-week-label" id="econWeekLabel">—</span>
                            <button class="econ-nav-btn" id="econNextWeek">&#9658;</button>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="econ-cal-filters">

                        <!-- Row 1: impact -->
                        <div class="econ-filter-row">
                            <span class="econ-filter-lbl">IMPACT</span>
                            <div class="econ-impact-filters">
                                <button class="econ-impact-btn econ-impact-active" data-impact="high">HIGH</button>
                                <button class="econ-impact-btn econ-impact-active" data-impact="medium">MED</button>
                                <button class="econ-impact-btn econ-impact-active" data-impact="low">LOW</button>
                                <button class="econ-impact-btn econ-impact-active" data-impact="non-economic">N/ECO</button>
                            </div>
                        </div>

                        <!-- Row 2: currency -->
                        <div class="econ-filter-row">
                            <span class="econ-filter-lbl">CCY</span>
                            <div class="econ-currency-filters">
                                <button class="econ-filter-btn econ-filter-active" data-currency="ALL">ALL</button>
                                <button class="econ-filter-btn" data-currency="USD">USD</button>
                                <button class="econ-filter-btn" data-currency="EUR">EUR</button>
                                <button class="econ-filter-btn" data-currency="GBP">GBP</button>
                                <button class="econ-filter-btn" data-currency="JPY">JPY</button>
                                <button class="econ-filter-btn" data-currency="AUD">AUD</button>
                                <button class="econ-filter-btn" data-currency="CAD">CAD</button>
                                <button class="econ-filter-btn" data-currency="CHF">CHF</button>
                                <button class="econ-filter-btn" data-currency="NZD">NZD</button>
                                <button class="econ-filter-btn" data-currency="CNY">CNY</button>
                            </div>
                        </div>

                        <!-- Row 3: event types -->
                        <div class="econ-filter-row">
                            <span class="econ-filter-lbl">TYPE</span>
                            <div class="econ-type-filters">
                                <button class="econ-type-btn econ-type-active" data-type="growth">GROWTH</button>
                                <button class="econ-type-btn econ-type-active" data-type="inflation">INFLATION</button>
                                <button class="econ-type-btn econ-type-active" data-type="employment">EMPLOYMENT</button>
                                <button class="econ-type-btn econ-type-active" data-type="central-bank">CENTRAL BANK</button>
                                <button class="econ-type-btn econ-type-active" data-type="bonds">BONDS</button>
                                <button class="econ-type-btn econ-type-active" data-type="housing">HOUSING</button>
                                <button class="econ-type-btn econ-type-active" data-type="consumer-surveys">CONSUMER</button>
                                <button class="econ-type-btn econ-type-active" data-type="business-surveys">BUSINESS</button>
                                <button class="econ-type-btn econ-type-active" data-type="speeches">SPEECHES</button>
                                <button class="econ-type-btn econ-type-active" data-type="misc">MISC</button>
                            </div>
                        </div>

                    </div>

                    <!-- Column headers -->
                    <div class="econ-col-hdrs">
                        <span class="econ-col-hdr">TIME</span>
                        <span class="econ-col-hdr">CURRENCY</span>
                        <span class="econ-col-hdr"></span>
                        <span class="econ-col-hdr">EVENT</span>
                        <span class="econ-col-hdr econ-col-hdr--right">FORECAST</span>
                        <span class="econ-col-hdr econ-col-hdr--right">PREVIOUS</span>
                        <span class="econ-col-hdr econ-col-hdr--right">ACTUAL</span>
                    </div>

                    <!-- Dynamic event list -->
                    <div class="econ-cal-body" id="econCalBody">
                        <div class="econ-loading">[ LOADING... ]</div>
                    </div>

                    <div class="econ-cal-source">DATA · FOREX FACTORY</div>
                </div>
                </div><!-- /cal-split-right -->

            </div><!-- /cal-split-layout -->

            <!-- ══ MARKET INTELLIGENCE ══ -->
            <div class="mkt-panel" id="mktPanel">
                <div class="mkt-header">
                    <div class="mkt-header-left">
                        <span class="mkt-title">MARKET INTELLIGENCE</span>
                        <span class="mkt-subtitle">AI MULTI-AGENT ANALYSIS · 6 SPECIALISTS</span>
                    </div>
                    <div class="mkt-header-right">
                        <span class="mkt-stale-badge" id="mktStaleBadge" style="display:none">STALE</span>
                        <span class="mkt-age" id="mktAge">—</span>
                        <button class="mkt-refresh-btn" id="mktRefresh" onclick="MarketOverview.refresh()">
                            <?= pix('refresh', 11, 11) ?> REFRESH
                        </button>
                    </div>
                </div>

                <!-- Loading -->
                <div class="mkt-loading" id="mktLoading">
                    <span class="mkt-loading-dot"></span>
                    <span class="mkt-loading-txt">ANALYZING MARKET CONDITIONS...</span>
                </div>

                <!-- Content -->
                <div id="mktContent" style="display:none">
                    <div class="mkt-verdict-row">
                        <div class="mkt-regime-col">
                            <div class="mkt-col-label">REGIME</div>
                            <div class="mkt-regime-val" id="mktRegime">—</div>
                            <div class="mkt-col-label" style="margin-top:18px">CONVICTION</div>
                            <div class="mkt-seg-bar" id="mktConvictionSegs"></div>
                            <div class="mkt-conviction-score" id="mktConvictionScore">—</div>
                        </div>
                        <div class="mkt-reasoning-col">
                            <div class="mkt-col-label">ANALYSIS</div>
                            <p class="mkt-reasoning-txt" id="mktReasoning">—</p>
                        </div>
                    </div>

                    <div class="mkt-agents-hdr">
                        <span class="mkt-col-label">AGENT OPINIONS</span>
                    </div>
                    <div class="mkt-agents-grid" id="mktAgentsGrid"></div>

                    <div class="mkt-footer-row">
                        DATA · YAHOO FINANCE · FEAR &amp; GREED INDEX · REFRESHES EVERY <?= AI_MARKET_CACHE_MIN ?> MIN
                    </div>
                </div>

                <!-- Error -->
                <div class="mkt-error" id="mktError" style="display:none">
                    <span class="mkt-error-txt" id="mktErrorTxt">Analysis unavailable.</span>
                </div>
            </div><!-- /mkt-panel -->

            </div><!-- /tab-calendar -->

            <!-- ══ TAB: AFFILIATE ══ -->
            <div class="dash-tab" id="tab-affiliate">
<?php
// Demo affiliate data (replace with real DB data when live)
$aff_status      = 'active';   // 'none' | 'pending' | 'active'
$aff_tier        = 'standard'; // 'standard' | 'silver' | 'gold'
$aff_rate        = 15;
$aff_link        = 'https://dojifunding.com?ref=DOJI-' . strtoupper(substr($profile['referral_code'] ?? 'XXXXX', 0, 8));
$aff_clicks      = 284;
$aff_conversions = 12;
$aff_earnings    = 448.20;
$aff_pending     = 112.05;
$aff_cookie_days = 30;

// Referral (parrainage) demo data
$ref_count       = 7;
$ref_coins       = 7;
$ref_referrals   = [
    ['name'=>'T. Martin',   'date'=>'2025-04-18', 'status'=>'purchased', 'coins'=>1],
    ['name'=>'A. Dupont',   'date'=>'2025-03-29', 'status'=>'purchased', 'coins'=>1],
    ['name'=>'K. Osei',     'date'=>'2025-03-11', 'status'=>'purchased', 'coins'=>1],
    ['name'=>'L. Ferreira', 'date'=>'2025-02-27', 'status'=>'purchased', 'coins'=>1],
    ['name'=>'M. Fontaine', 'date'=>'2025-02-03', 'status'=>'purchased', 'coins'=>1],
    ['name'=>'C. Bouchard', 'date'=>'2025-01-20', 'status'=>'purchased', 'coins'=>1],
    ['name'=>'R. Nkosi',    'date'=>'2025-01-07', 'status'=>'purchased', 'coins'=>1],
];
$tier_map = [
    'standard' => ['label'=>'STANDARD', 'rate'=>15, 'min'=>0,   'max'=>50,  'color'=>'#6B7280'],
    'silver'   => ['label'=>'SILVER',   'rate'=>20, 'min'=>50,  'max'=>150, 'color'=>'#9BA8B5'],
    'gold'     => ['label'=>'GOLD',     'rate'=>25, 'min'=>150, 'max'=>null,'color'=>'#D4A843'],
];
$tier = $tier_map[$aff_tier];
?>
                <div class="wlt-grid">

                    <!-- ─ PARRAINAGE card ─ -->
                    <div class="wlt-card" style="--wlt-accent:#6366F1">
                        <div class="wlt-card-head">
                            <div class="wlt-card-title">PARRAINAGE</div>
                            <div class="wlt-card-bal-row">
                                <div class="wlt-card-bal wlt-card-bal--coins"><?= $ref_coins ?> <span class="wlt-dc-sym">DC</span></div>
                            </div>
                            <div class="wlt-card-sub">REFER FRIENDS · EARN DOJI COINS</div>
                            <div class="wlt-card-prog-wrap">
                                <?php
                                $_refSegs   = 20;
                                $_refTarget = 20;
                                $_refPct    = min(100, ($ref_count / $_refTarget) * 100);
                                $_refOnSegs = (int)round($_refSegs * $_refPct / 100);
                                ?>
                                <div class="wlt-seg-bar">
                                    <?php for ($_s = 0; $_s < $_refSegs; $_s++): ?>
                                    <div class="wlt-seg<?= $_s < $_refOnSegs ? ' wlt-seg-on wlt-seg-indigo' : '' ?>"></div>
                                    <?php endfor; ?>
                                </div>
                                <div class="wlt-card-prog-labels">
                                    <span class="wlt-prog-lbl indigo"><?= $ref_count ?> REFERRALS</span>
                                    <span class="wlt-prog-lbl dim">1 DC / REFERRAL · NO LIMIT</span>
                                </div>
                            </div>
                        </div>
                        <div class="aff-card-body">

                            <!-- Stats row -->
                            <div class="aff-kpi-row">
                                <div class="aff-kpi" style="--kpi-c:#0EA5E9">
                                    <div class="aff-kpi-val"><?= $ref_count ?></div>
                                    <div class="aff-kpi-lbl">FRIENDS REFERRED</div>
                                </div>
                                <div class="aff-kpi" style="--kpi-c:#D4A843">
                                    <div class="aff-kpi-val"><?= $ref_coins ?></div>
                                    <div class="aff-kpi-lbl">COINS EARNED</div>
                                </div>
                                <div class="aff-kpi" style="--kpi-c:#8B5CF6">
                                    <div class="aff-kpi-val">1:1</div>
                                    <div class="aff-kpi-lbl">COIN / REFERRAL</div>
                                </div>
                            </div>

                            <!-- Referral code block -->
                            <div class="aff-block">
                                <div class="aff-block-lbl">YOUR REFERRAL CODE</div>
                                <?php if (!empty($profile['referral_code'])): ?>
                                <div class="aff-code-row">
                                    <span class="aff-code-val" id="refCodeText"><?= htmlspecialchars($profile['referral_code']) ?></span>
                                    <button class="aff-copy-btn" onclick="AffDash.copyText('refCodeText', this)">COPY</button>
                                </div>
                                <div class="aff-block-hint">Share this code — your friend enters it at checkout and you both benefit.</div>
                                <?php else: ?>
                                <div class="aff-block-hint" style="color:var(--text-dis)">Your code will be generated after your first challenge purchase.</div>
                                <?php endif; ?>
                            </div>

                            <!-- Share link -->
                            <?php if (!empty($profile['referral_code'])): ?>
                            <div class="aff-block">
                                <div class="aff-block-lbl">SHARE LINK</div>
                                <div class="aff-code-row">
                                    <span class="aff-code-val aff-code-url" id="refLinkText">dojifunding.com?ref=<?= htmlspecialchars($profile['referral_code']) ?></span>
                                    <button class="aff-copy-btn" onclick="AffDash.copyText('refLinkText', this)">COPY</button>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- How it works -->
                            <div class="aff-block">
                                <div class="aff-block-lbl">HOW IT WORKS</div>
                                <div class="aff-steps-list">
                                    <div class="aff-step-row">
                                        <div class="aff-step-num">01</div>
                                        <div class="aff-step-txt">Share your referral code or link with friends</div>
                                    </div>
                                    <div class="aff-step-row">
                                        <div class="aff-step-num">02</div>
                                        <div class="aff-step-txt">Friend enters the code at checkout when purchasing a challenge</div>
                                    </div>
                                    <div class="aff-step-row">
                                        <div class="aff-step-num">03</div>
                                        <div class="aff-step-txt">You receive <span style="color:#6366F1">+1 Doji Coin</span> automatically — no limit</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Referral history -->
                            <div class="aff-block">
                                <div class="aff-block-lbl">REFERRAL HISTORY</div>
                                <div class="aff-history">
                                    <?php foreach ($ref_referrals as $r): ?>
                                    <div class="aff-hist-row">
                                        <div class="aff-hist-name"><?= htmlspecialchars($r['name']) ?></div>
                                        <div class="aff-hist-date"><?= date('d M Y', strtotime($r['date'])) ?></div>
                                        <div class="aff-hist-status aff-hist-ok">PURCHASED</div>
                                        <div class="aff-hist-coin" style="color:var(--accent)">+<?= $r['coins'] ?> <?= pix('coin', 10, 10) ?></div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                        </div>
                    </div><!-- /parrainage card -->

                    <!-- ─ AFFILIATION card ─ -->
                    <div class="wlt-card" style="--wlt-accent:#10B981">
                        <div class="wlt-card-head">
                            <div class="wlt-card-title" style="display:flex;align-items:center;gap:8px">
                                AFFILIATION
                                <?php if ($aff_status === 'active'): ?>
                                <span class="aff-status-badge aff-status-active">ACTIVE</span>
                                <?php elseif ($aff_status === 'pending'): ?>
                                <span class="aff-status-badge aff-status-pending">PENDING</span>
                                <?php else: ?>
                                <span class="aff-status-badge">NOT APPLIED</span>
                                <?php endif; ?>
                            </div>
                            <div class="wlt-card-bal-row">
                                <div class="wlt-card-bal">$<?= number_format($aff_earnings, 2) ?></div>
                                <div class="wlt-card-btns">
                                    <?php if ($aff_status !== 'active'): ?>
                                    <a href="affiliates.php" class="wlt-action-btn wlt-btn-accent" style="text-decoration:none">APPLY</a>
                                    <a href="mailto:affiliates@dojifunding.com" class="wlt-action-btn wlt-btn-ghost" style="text-decoration:none">CONTACT</a>
                                    <?php else: ?>
                                    <a href="affiliates.php" class="wlt-action-btn wlt-btn-ghost" style="text-decoration:none">PUBLIC PAGE</a>
                                    <a href="mailto:affiliates@dojifunding.com" class="wlt-action-btn wlt-btn-ghost" style="text-decoration:none">CONTACT</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="wlt-card-sub">TOTAL EARNED · <?= $tier['rate'] ?>% COMMISSION · $<?= number_format($aff_pending, 2) ?> PENDING</div>
                            <div class="wlt-card-prog-wrap">
                                <?php
                                $_tierMin    = (int)$tier['min'];
                                $_tierMax    = (int)($tier['max'] ?? 200);
                                $_tierRange  = max(1, $_tierMax - $_tierMin);
                                $_tierPct    = max(0, min(100, (($aff_conversions - $_tierMin) / $_tierRange) * 100));
                                $_tierSegs   = 20;
                                $_tierOnSegs = (int)round($_tierSegs * $_tierPct / 100);
                                ?>
                                <div class="wlt-seg-bar">
                                    <?php for ($_s = 0; $_s < $_tierSegs; $_s++): ?>
                                    <div class="wlt-seg<?= $_s < $_tierOnSegs ? ' wlt-seg-on wlt-seg-green' : '' ?>"></div>
                                    <?php endfor; ?>
                                </div>
                                <div class="wlt-card-prog-labels">
                                    <span class="wlt-prog-lbl green"><?= $aff_conversions ?> CONVERSIONS</span>
                                    <span class="wlt-prog-lbl dim"><?= $tier['label'] ?> TIER</span>
                                </div>
                            </div>
                        </div>
                        <div class="aff-card-body">

                            <!-- Tier strip -->
                            <div class="aff-tier-strip">
                                <?php foreach ($tier_map as $k => $t): ?>
                                <div class="aff-tier-item <?= $k === $aff_tier ? 'aff-tier-active' : '' ?>" style="--tier-c:<?= $t['color'] ?>">
                                    <div class="aff-tier-name"><?= $t['label'] ?></div>
                                    <div class="aff-tier-rate"><?= $t['rate'] ?>%</div>
                                    <div class="aff-tier-range"><?= $t['min'] ?><?= $t['max'] ? '–'.$t['max'] : '+' ?> REF/MO</div>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- KPIs -->
                            <div class="aff-kpi-row">
                                <div class="aff-kpi" style="--kpi-c:#06B6D4">
                                    <div class="aff-kpi-val"><?= number_format($aff_clicks) ?></div>
                                    <div class="aff-kpi-lbl">LINK CLICKS</div>
                                </div>
                                <div class="aff-kpi" style="--kpi-c:#10B981">
                                    <div class="aff-kpi-val"><?= $aff_conversions ?></div>
                                    <div class="aff-kpi-lbl">CONVERSIONS</div>
                                </div>
                                <div class="aff-kpi" style="--kpi-c:#8B5CF6">
                                    <div class="aff-kpi-val"><?= $aff_cookie_days ?>D</div>
                                    <div class="aff-kpi-lbl">COOKIE</div>
                                </div>
                            </div>

                            <!-- Affiliate link -->
                            <div class="aff-block">
                                <div class="aff-block-lbl">YOUR AFFILIATE LINK</div>
                                <div class="aff-code-row">
                                    <span class="aff-code-val aff-code-url" id="affLinkText"><?= htmlspecialchars(str_replace('https://', '', $aff_link)) ?></span>
                                    <button class="aff-copy-btn" onclick="AffDash.copyText('affLinkText', this)">COPY</button>
                                </div>
                                <div class="aff-block-hint">30-day cookie tracking · works on all challenge purchases</div>
                            </div>

                            <!-- Promo assets -->
                            <div class="aff-block">
                                <div class="aff-block-lbl">PROMO ASSETS</div>
                                <div class="aff-assets-grid">
                                    <div class="aff-asset-item">
                                        <?= pix('table',   16, 16) ?>
                                        <span>Banners</span>
                                        <span class="aff-asset-tag">SOON</span>
                                    </div>
                                    <div class="aff-asset-item">
                                        <?= pix('monitor', 16, 16) ?>
                                        <span>Social Kit</span>
                                        <span class="aff-asset-tag">SOON</span>
                                    </div>
                                    <div class="aff-asset-item">
                                        <?= pix('tag',     16, 16) ?>
                                        <span>Promo Codes</span>
                                        <span class="aff-asset-tag">SOON</span>
                                    </div>
                                    <div class="aff-asset-item">
                                        <?= pix('pen',     16, 16) ?>
                                        <span>Ad Copy</span>
                                        <span class="aff-asset-tag">SOON</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Commission details -->
                            <div class="aff-block">
                                <div class="aff-block-lbl">COMMISSION DETAILS</div>
                                <div class="aff-comm-rows">
                                    <div class="aff-comm-row"><span>Current rate</span><span style="color:#10B981"><?= $tier['rate'] ?>%</span></div>
                                    <div class="aff-comm-row"><span>Cookie duration</span><span><?= $aff_cookie_days ?> days</span></div>
                                    <div class="aff-comm-row"><span>Payout cycle</span><span>Weekly</span></div>
                                    <div class="aff-comm-row"><span>Min. withdrawal</span><span>None</span></div>
                                    <div class="aff-comm-row"><span>Methods</span><span>Rise · Confirmo</span></div>
                                </div>
                            </div>

                        </div>
                    </div><!-- /affiliation card -->

                </div><!-- /wlt-grid -->
            </div>

            <!-- ══ TAB: TESTIMONIALS ══ -->
            <div class="dash-tab" id="tab-testimonials">
                <!-- Stats strip (injected by JS) -->
                <div class="tm-stats-strip" id="tmStatsStrip"></div>
                <!-- Header + sort -->
                <div class="tm-hdr">
                    <div class="tm-hdr-left">
                        <div class="tm-hdr-ttl">FUNDED TRADER PAYOUTS</div>
                        <div class="tm-hdr-sub">VERIFIED WITHDRAWALS FROM REAL FUNDED ACCOUNTS · CLICK ANY CARD TO VIEW PROFILE</div>
                    </div>
                    <div class="tm-sort-pills" id="tmSortPills">
                        <button class="tm-pill active" data-sort="amount">BY AMOUNT</button>
                        <button class="tm-pill" data-sort="recent">MOST RECENT</button>
                        <button class="tm-pill" data-sort="tier">BY TIER</button>
                    </div>
                </div>
                <!-- Grid (injected by JS) -->
                <div class="tm-grid" id="tmGrid">
                    <div class="tm-empty">[ LOADING... ]</div>
                </div>
            </div>

            <!-- ══ TAB: SUPPORT ══ -->
            <div class="dash-tab" id="tab-support">

                <!-- Stats strip -->
                <div class="sp-stats-strip">
                    <div class="sp-stat" style="--sp-sc:#10B981">
                        <div class="sp-stat-val">98.7%</div>
                        <div class="sp-stat-lbl">RESOLUTION RATE</div>
                    </div>
                    <div class="sp-stat" style="--sp-sc:#0EA5E9">
                        <div class="sp-stat-val">&lt; 2h</div>
                        <div class="sp-stat-lbl">RESPONSE TIME</div>
                    </div>
                    <div class="sp-stat" style="--sp-sc:#D4A843">
                        <div class="sp-stat-val">4.8 / 5</div>
                        <div class="sp-stat-lbl">SATISFACTION</div>
                    </div>
                    <div class="sp-stat" style="--sp-sc:#8B5CF6">
                        <div class="sp-stat-val">24 / 7</div>
                        <div class="sp-stat-lbl">AVAILABILITY</div>
                    </div>
                </div>

                <!-- Header -->
                <div class="sp-hdr">
                    <div class="sp-hdr-left">
                        <div class="sp-hdr-ttl">GET HELP</div>
                        <div class="sp-hdr-sub">CONTACT OUR TEAM · REPORT ISSUES · LEAVE A REVIEW</div>
                    </div>
                </div>

                <!-- Quick access -->
                <div class="sp-quick-grid">

                    <button class="sp-quick-card" style="--sp-c:#10B981" onclick="SupportTab.openChat()">
                        <div class="sp-quick-icon">
                            <?= pix('message', 20, 20) ?>
                        </div>
                        <div class="sp-quick-body">
                            <div class="sp-quick-ttl">LIVE CHAT</div>
                            <div class="sp-quick-sub">Average response &lt; 2 min</div>
                        </div>
                        <div class="sp-quick-badge sp-badge--online">● ONLINE</div>
                    </button>

                    <button class="sp-quick-card" style="--sp-c:#8B5CF6" onclick="document.getElementById('spTicketsAnchor').scrollIntoView({behavior:'smooth'})">
                        <div class="sp-quick-icon">
                            <?= pix('calendar', 20, 20) ?>
                        </div>
                        <div class="sp-quick-body">
                            <div class="sp-quick-ttl">MY TICKETS</div>
                            <div class="sp-quick-sub">2 active · 4 total</div>
                        </div>
                        <div class="sp-quick-badge">2 OPEN</div>
                    </button>

                    <a class="sp-quick-card" style="--sp-c:#D4A843" href="<?= SITE_URL ?>/faq" target="_blank" rel="noopener">
                        <div class="sp-quick-icon">
                            <?= pix('question', 20, 20) ?>
                        </div>
                        <div class="sp-quick-body">
                            <div class="sp-quick-ttl">BROWSE FAQ</div>
                            <div class="sp-quick-sub">Instant answers to common questions</div>
                        </div>
                        <?= pix('arrow-ur', 14, 14) ?>
                    </a>

                </div>

                <!-- Contact + Bug grid -->
                <div class="sp-form-grid">

                    <!-- Contact Support -->
                    <div class="sp-card" style="--sp-c:#0EA5E9" id="spContactCard">
                        <div class="sp-card-hdr">
                            <?= pix('message', 16, 16, 'sp-card-icon') ?>
                            <div>
                                <div class="sp-card-ttl">CONTACT SUPPORT</div>
                                <div class="sp-card-sub">Questions, account issues, payouts</div>
                            </div>
                        </div>
                        <form class="sp-form" id="spContactForm" autocomplete="off">
                            <div class="sp-row">
                                <label class="sp-lbl">CATEGORY</label>
                                <select class="sp-select" name="category">
                                    <option value="account">Account &amp; Profile</option>
                                    <option value="payout">Payout &amp; Withdrawals</option>
                                    <option value="trading">Trading Rules &amp; Performance</option>
                                    <option value="billing">Billing &amp; Challenges</option>
                                    <option value="technical">Technical Issues</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="sp-row">
                                <label class="sp-lbl">SUBJECT</label>
                                <input type="text" class="sp-input" name="subject" placeholder="Brief summary of your issue…" maxlength="120">
                            </div>
                            <div class="sp-row">
                                <label class="sp-lbl">MESSAGE</label>
                                <textarea class="sp-textarea" name="message" rows="5" placeholder="Describe your issue in detail…" maxlength="2000"></textarea>
                            </div>
                            <div class="sp-form-foot">
                                <button type="submit" class="sp-btn sp-btn--primary">SEND MESSAGE</button>
                                <div class="sp-feedback" id="spContactFeedback"></div>
                            </div>
                        </form>
                    </div>

                    <!-- Bug Report -->
                    <div class="sp-card" style="--sp-c:#EF4444" id="spBugCard">
                        <div class="sp-card-hdr">
                            <?= pix('info', 16, 16, 'sp-card-icon') ?>
                            <div>
                                <div class="sp-card-ttl">REPORT A BUG</div>
                                <div class="sp-card-sub">Dashboard errors, display issues, glitches</div>
                            </div>
                        </div>
                        <form class="sp-form" id="spBugForm" autocomplete="off">
                            <div class="sp-row">
                                <label class="sp-lbl">TYPE</label>
                                <select class="sp-select" name="bug_type">
                                    <option value="bug">Bug / Error</option>
                                    <option value="ui">UI / Display Issue</option>
                                    <option value="data">Data Discrepancy</option>
                                    <option value="feature">Feature Request</option>
                                </select>
                            </div>
                            <div class="sp-row">
                                <label class="sp-lbl">WHERE</label>
                                <input type="text" class="sp-input" name="area" placeholder="e.g. Overview tab, Payouts section…" maxlength="120">
                            </div>
                            <div class="sp-row">
                                <label class="sp-lbl">DESCRIPTION</label>
                                <textarea class="sp-textarea" name="description" rows="5" placeholder="Steps to reproduce, expected vs actual behaviour…" maxlength="2000"></textarea>
                            </div>
                            <div class="sp-form-foot">
                                <button type="submit" class="sp-btn sp-btn--danger">REPORT BUG</button>
                                <div class="sp-feedback" id="spBugFeedback"></div>
                            </div>
                        </form>
                    </div>

                </div>

                <!-- My Tickets -->
                <div id="spTicketsAnchor" style="scroll-margin-top:72px"></div>
                <div class="sp-tickets-hdr-row">
                    <div>
                        <div class="sp-tickets-ttl">MY TICKETS</div>
                        <div class="sp-tickets-meta">4 TOTAL · 2 ACTIVE · 2 CLOSED</div>
                    </div>
                </div>
                <div class="sp-tickets-list">

                    <div class="sp-tkt-row">
                        <div class="sp-tkt-id">#TKT-0042</div>
                        <div class="sp-tkt-subj">Payout delay on Phase 2 funded account</div>
                        <div class="sp-tkt-cat">PAYOUTS</div>
                        <div class="sp-tkt-status sp-tkt-s--resolved">RESOLVED</div>
                        <div class="sp-tkt-date">APR 28</div>
                    </div>

                    <div class="sp-tkt-row">
                        <div class="sp-tkt-id">#TKT-0043</div>
                        <div class="sp-tkt-subj">Clarification on max daily drawdown calculation</div>
                        <div class="sp-tkt-cat">TRADING RULES</div>
                        <div class="sp-tkt-status sp-tkt-s--resolved">CLOSED</div>
                        <div class="sp-tkt-date">MAY 1</div>
                    </div>

                    <div class="sp-tkt-row">
                        <div class="sp-tkt-id">#TKT-0044</div>
                        <div class="sp-tkt-subj">Equity balance displaying incorrect value on dashboard</div>
                        <div class="sp-tkt-cat">TECHNICAL</div>
                        <div class="sp-tkt-status sp-tkt-s--progress">IN PROGRESS</div>
                        <div class="sp-tkt-date">MAY 5</div>
                    </div>

                    <div class="sp-tkt-row">
                        <div class="sp-tkt-id">#TKT-0045</div>
                        <div class="sp-tkt-subj">Challenge fee charged twice for the same purchase</div>
                        <div class="sp-tkt-cat">BILLING</div>
                        <div class="sp-tkt-status sp-tkt-s--open">OPEN</div>
                        <div class="sp-tkt-date">MAY 7</div>
                    </div>

                </div>

                <!-- Rate us -->
                <div class="sp-reviews-hdr">RATE DOJI FUNDING</div>
                <div class="sp-reviews">
                    <a href="https://www.trustpilot.com/review/dojifunding.com" target="_blank" rel="noopener" class="sp-review-card" style="--sp-c:#00B67A">
                        <div class="sp-review-top">
                            <div class="sp-review-name">Trustpilot</div>
                            <div class="sp-stars"><?= pix('heart',14,14) ?><?= pix('heart',14,14) ?><?= pix('heart',14,14) ?><?= pix('heart',14,14) ?><?= pix('heart',14,14) ?></div>
                        </div>
                        <div class="sp-review-desc">Share your experience and help other traders discover Doji Funding.</div>
                        <div class="sp-review-cta">LEAVE A REVIEW ↗</div>
                    </a>
                    <a href="https://propfirmmatch.com/prop-firms/doji-funding" target="_blank" rel="noopener" class="sp-review-card" style="--sp-c:#6366F1">
                        <div class="sp-review-top">
                            <div class="sp-review-name">PropFirmMatch</div>
                            <div class="sp-stars"><?= pix('heart',14,14) ?><?= pix('heart',14,14) ?><?= pix('heart',14,14) ?><?= pix('heart',14,14) ?><?= pix('heart',14,14) ?></div>
                        </div>
                        <div class="sp-review-desc">Rate us on the leading prop firm comparison platform.</div>
                        <div class="sp-review-cta">RATE US ↗</div>
                    </a>
                </div>

            </div>

        </main>
    </div>

    <!-- ══ TRADER PROFILE OVERLAY ══ -->
    <div class="tp-overlay" id="tpOverlay" style="display:none" onclick="TraderProfile.close(event)">
        <div class="tp-panel">
            <button class="tp-close" onclick="TraderProfile.close(null)">&times;</button>
            <div class="tp-body" id="tpBody"></div>
        </div>
    </div>

    <!-- Mobile tab bar -->
    <div class="dash-mobile-tabs">
        <button class="dash-mobile-tab active" data-tab="overview">
            <?= pix('grid', 20, 20) ?>
            <span>Overview</span>
        </button>
        <button class="dash-mobile-tab" data-tab="challenges">
            <?= pix('layers', 20, 20) ?>
            <span>Challenges</span>
        </button>
        <button class="dash-mobile-tab" data-tab="payouts">
            <?= pix('dollar', 20, 20) ?>
            <span>Payouts</span>
        </button>
        <button class="dash-mobile-tab" data-tab="settings">
            <?= pix('user', 20, 20) ?>
            <span>Profile</span>
        </button>
        <button class="dash-mobile-tab" id="dashMobileMore">
            <?= pix('more', 20, 20) ?>
            <span>More</span>
        </button>
    </div>

    <!-- Sidebar overlay (mobile drawer) -->
    <div class="dash-sidebar-overlay" id="dashSidebarOverlay"></div>

</div>

<!-- ══ REQUEST PAYOUT TO WALLET MODAL ══ -->
<div class="modal-overlay" id="requestPayoutModal" role="dialog" aria-modal="true">
    <div class="modal modal-payout">
        <canvas class="modal-dot-canvas" aria-hidden="true"></canvas>
        <div class="modal-content">
            <button class="modal-close" onclick="RequestPayoutModal.close()" aria-label="Close">&times;</button>

            <!-- ─ STEP 1 ─ -->
            <div id="rptStep1">
                <div class="pyt-header">
                    <div class="pyt-avail-label">SOURCE ACCOUNT</div>
                    <div class="pyt-avail-val" id="rptAccountRef" style="font-size:15px">—</div>
                </div>
                <div class="pyt-header" style="margin-top:12px">
                    <div class="pyt-avail-label">AVAILABLE FOR PAYOUT</div>
                    <div class="pyt-avail-val" id="rptMaxDisplay">—</div>
                </div>

                <div class="form-group" style="margin-top:20px">
                    <label class="form-label">Amount <span class="form-req">*</span></label>
                    <div class="pyt-amount-wrap">
                        <span class="pyt-currency">$</span>
                        <input class="form-input pyt-amount-input" type="number" id="rptAmount"
                               placeholder="0.00" min="1" step="0.01"
                               oninput="RequestPayoutModal.validate()">
                        <button class="pyt-max-btn" type="button" onclick="RequestPayoutModal.setMax()">MAX</button>
                    </div>
                    <div class="form-error" id="rptAmountErr"></div>
                </div>

                <div class="pyt-disclaimer">
                    <div class="pyt-disclaimer-title">ℹ PAYOUT TO WALLET</div>
                    <p class="pyt-disclaimer-body">The requested amount will be credited to your Doji Wallet. From the Wallet tab you can then initiate a withdrawal to your preferred payment method (Rise, Confirmo, etc.).</p>
                    <label class="pyt-check-lbl">
                        <input type="checkbox" id="rptAck" onchange="RequestPayoutModal.validate()">
                        <span>I confirm the amount and agree to credit it to my Doji Wallet.</span>
                    </label>
                </div>

                <button class="form-btn" id="rptSubmitBtn" type="button" onclick="RequestPayoutModal.submit()" disabled>
                    CREDIT TO WALLET →
                </button>
            </div>

            <!-- ─ STEP 2: CONFIRMATION ─ -->
            <div id="rptStep2" style="display:none">
                <div class="pyt-success-block">
                    <div class="pyt-success-icon">✓</div>
                    <h2 class="modal-title">Credited to Wallet</h2>
                    <p class="modal-sub">Your payout has been credited to your Doji Wallet.</p>
                </div>
                <div class="pyt-recap">
                    <div class="pyt-recap-row">
                        <span class="pyt-recap-lbl">AMOUNT CREDITED</span>
                        <span class="pyt-recap-val" id="rptRecapAmt">—</span>
                    </div>
                    <div class="pyt-recap-row">
                        <span class="pyt-recap-lbl">SOURCE</span>
                        <span class="pyt-recap-val" id="rptRecapSource">—</span>
                    </div>
                    <div class="pyt-recap-row">
                        <span class="pyt-recap-lbl">DESTINATION</span>
                        <span class="pyt-recap-val" style="color:var(--accent)">DOJI WALLET</span>
                    </div>
                    <div class="pyt-recap-row">
                        <span class="pyt-recap-lbl">STATUS</span>
                        <span class="pyt-recap-val" style="color:var(--success)">CREDITED</span>
                    </div>
                </div>
                <button class="form-btn" type="button" onclick="RequestPayoutModal.goToWallet()">
                    GO TO WALLET →
                </button>
            </div>

        </div>
    </div>
</div>

<!-- ══ PAYOUT MODAL ══ -->
<div class="modal-overlay" id="payoutModal" role="dialog" aria-modal="true" aria-labelledby="pytTitle">
    <div class="modal modal-payout">
        <canvas class="modal-dot-canvas" aria-hidden="true"></canvas>
        <div class="modal-content">
            <button class="modal-close" onclick="PayoutModal.close()" aria-label="Close">&times;</button>

            <!-- ─ STEP 1: REQUEST ─ -->
            <div id="pytStep1">
                <div class="pyt-header">
                    <div class="pyt-avail-label">AVAILABLE BALANCE</div>
                    <div class="pyt-avail-val"><?= formatMoney($walletBalance) ?></div>
                </div>

                <div class="form-group">
                    <label class="form-label">Payout Method</label>
                    <div class="pyt-method-row">
                        <button class="pyt-method-btn pyt-method-active" type="button" data-method="rise" onclick="PayoutModal.setMethod(this)">
                            <span class="pyt-method-name">RISE</span>
                            <span class="pyt-method-sub">rise.com</span>
                        </button>
                        <button class="pyt-method-btn" type="button" data-method="confirmo" onclick="PayoutModal.setMethod(this)">
                            <span class="pyt-method-name">CONFIRMO</span>
                            <span class="pyt-method-sub">Crypto</span>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Amount <span class="form-req">*</span></label>
                    <div class="pyt-amount-wrap">
                        <span class="pyt-currency">$</span>
                        <input class="form-input pyt-amount-input" type="number" id="pytAmount"
                               placeholder="0.00" min="1" step="0.01"
                               max="<?= htmlspecialchars((string)$walletBalance) ?>"
                               oninput="PayoutModal.validate()">
                        <button class="pyt-max-btn" type="button" onclick="PayoutModal.setMax()">MAX</button>
                    </div>
                    <div class="form-error" id="pytAmountErr"></div>
                </div>

                <div class="pyt-disclaimer">
                    <div class="pyt-disclaimer-title">⚠ IMPORTANT NOTICE</div>
                    <p class="pyt-disclaimer-body">This payout transfer is definitive and irreversible once initiated. Doji Funding cannot cancel or reverse a transfer after submission. Processing may take up to 7 business days depending on your selected method and jurisdiction. Ensure your payout details are correct before confirming.</p>
                    <label class="pyt-check-lbl">
                        <input type="checkbox" id="pytAck" onchange="PayoutModal.validate()">
                        <span>I acknowledge that this transfer is definitive and irreversible.</span>
                    </label>
                </div>

                <button class="form-btn" id="pytSubmitBtn" type="button" onclick="PayoutModal.submit()" disabled>
                    SUBMIT PAYOUT →
                </button>
            </div>

            <!-- ─ STEP 2: RECAP ─ -->
            <div id="pytStep2" style="display:none">
                <div class="pyt-success-block">
                    <div class="pyt-success-icon">✓</div>
                    <h2 class="modal-title" id="pytTitle">Request Submitted</h2>
                    <p class="modal-sub">Your payout request has been received and is being processed.</p>
                </div>

                <div class="pyt-recap">
                    <div class="pyt-recap-row">
                        <span class="pyt-recap-lbl">AMOUNT REQUESTED</span>
                        <span class="pyt-recap-val" id="pytRecapAmt">—</span>
                    </div>
                    <div class="pyt-recap-row">
                        <span class="pyt-recap-lbl">DESTINATION</span>
                        <span class="pyt-recap-val" id="pytRecapDest">—</span>
                    </div>
                    <div class="pyt-recap-row">
                        <span class="pyt-recap-lbl">STATUS</span>
                        <span class="pyt-recap-val pyt-status-pending">PENDING</span>
                    </div>
                    <div class="pyt-recap-row">
                        <span class="pyt-recap-lbl">REFERENCE</span>
                        <span class="pyt-recap-val pyt-recap-ref" id="pytRecapRef">—</span>
                    </div>
                </div>

                <div class="pyt-reminder">
                    This transfer is definitive and irreversible. Processing can take up to 7 business days. You will receive a notification once the transfer is completed.
                </div>

                <button class="form-btn" type="button" onclick="PayoutModal.close()">CLOSE</button>
            </div>

        </div><!-- /.modal-content -->
    </div>
</div>

<!-- ══ DISCOUNT COUPON MODAL ══ -->
<div class="modal-overlay" id="discountModal" role="dialog" aria-modal="true"
     data-dc="<?= (int)$dojiCoins ?>">
    <div class="modal modal-psl">
        <canvas class="modal-dot-canvas" aria-hidden="true"></canvas>
        <div class="modal-content">
            <button class="modal-close" onclick="DiscountModal.close()" aria-label="Close">&times;</button>

            <!-- ─ STEP 1 ─ -->
            <div id="discStep1">
                <div class="pyt-header">
                    <div class="pyt-avail-label">DOJI COINS BALANCE</div>
                    <div class="pyt-avail-val pyt-avail-coins"><?= number_format((int)$dojiCoins) ?><span class="pyt-avail-coins-sym">DC</span></div>
                </div>

                <div class="form-group">
                    <label class="form-label">Select Discount Tier</label>
                    <div class="disc-tiers">
                        <button class="disc-tier-btn" type="button" data-pct="5"  data-cost="100"  onclick="DiscountModal.setTier(this)">
                            <span class="disc-tier-pct">5%</span><span class="disc-tier-off">OFF</span><span class="disc-tier-cost">100 DC</span>
                        </button>
                        <button class="disc-tier-btn" type="button" data-pct="10" data-cost="200"  onclick="DiscountModal.setTier(this)">
                            <span class="disc-tier-pct">10%</span><span class="disc-tier-off">OFF</span><span class="disc-tier-cost">200 DC</span>
                        </button>
                        <button class="disc-tier-btn" type="button" data-pct="15" data-cost="350"  onclick="DiscountModal.setTier(this)">
                            <span class="disc-tier-pct">15%</span><span class="disc-tier-off">OFF</span><span class="disc-tier-cost">350 DC</span>
                        </button>
                        <button class="disc-tier-btn" type="button" data-pct="20" data-cost="500"  onclick="DiscountModal.setTier(this)">
                            <span class="disc-tier-pct">20%</span><span class="disc-tier-off">OFF</span><span class="disc-tier-cost">500 DC</span>
                        </button>
                    </div>
                    <div class="psl-result-line" id="discResultLine" style="display:none"></div>
                </div>

                <div class="pyt-disclaimer">
                    <div class="pyt-disclaimer-title">⚠ IMPORTANT NOTICE</div>
                    <p class="pyt-disclaimer-body">The use of Doji Coins for discount coupons is definitive and irreversible. Once confirmed, the Doji Coins will be permanently deducted from your balance and a single-use coupon code generated. Coupons apply to one evaluation purchase only and cannot be refunded or transferred.</p>
                    <label class="pyt-check-lbl">
                        <input type="checkbox" id="discAck" onchange="DiscountModal.validate()">
                        <span>I acknowledge that the use of Doji Coins is definitive and irreversible.</span>
                    </label>
                </div>

                <div class="form-error" id="discErr"></div>

                <button class="form-btn" id="discSubmitBtn" type="button" onclick="DiscountModal.submit()" disabled>
                    GENERATE COUPON →
                </button>
            </div>

            <!-- ─ STEP 2: RECAP ─ -->
            <div id="discStep2" style="display:none">
                <div class="pyt-success-block">
                    <div class="pyt-success-icon" style="font-size:16px;font-family:var(--f-mono)">%</div>
                    <h2 class="modal-title">Coupon Generated</h2>
                    <p class="modal-sub">Your discount coupon is ready to use at checkout.</p>
                </div>

                <div class="pyt-recap">
                    <div class="pyt-recap-row">
                        <span class="pyt-recap-lbl">COUPON CODE</span>
                        <span class="pyt-recap-val disc-coupon-code" id="discRecapCode">—</span>
                    </div>
                    <div class="pyt-recap-row">
                        <span class="pyt-recap-lbl">DISCOUNT</span>
                        <span class="pyt-recap-val" id="discRecapPct" style="color:var(--accent)">—</span>
                    </div>
                    <div class="pyt-recap-row">
                        <span class="pyt-recap-lbl">DC SPENT</span>
                        <span class="pyt-recap-val pyt-status-pending" id="discRecapCost">—</span>
                    </div>
                    <div class="pyt-recap-row">
                        <span class="pyt-recap-lbl">VALIDITY</span>
                        <span class="pyt-recap-val" style="color:var(--text-sec);font-size:10px">Single use · Evaluation only</span>
                    </div>
                </div>

                <div class="pyt-reminder">
                    Your coupon will be automatically applied when you proceed to checkout from the Configurator. Valid for one evaluation purchase only.
                </div>

                <div class="disc-cta-row">
                    <button class="form-btn disc-btn-ghost" type="button" onclick="DiscountModal.close()">CLOSE</button>
                    <button class="form-btn" type="button" onclick="DiscountModal.goCheckout()">BUY CHALLENGE →</button>
                </div>
            </div>

        </div><!-- /.modal-content -->
    </div>
</div>

<!-- ══ CHECKOUT MODAL ══ -->
<div class="modal-overlay" id="purchaseModal" role="dialog" aria-modal="true">
    <div class="modal modal-checkout">
        <canvas class="modal-dot-canvas" aria-hidden="true"></canvas>
        <div class="modal-content">
            <button class="modal-close" onclick="PurchaseModal.close()" aria-label="Close">&times;</button>

            <!-- ─ STEP 1: SUMMARY + PAYMENT ─ -->
            <div id="coStep1">
                <div class="pyt-header">
                    <div class="pyt-avail-label">CHECKOUT</div>
                    <div class="co-challenge-label" id="coChallengeLbl">—</div>
                </div>

                <div class="co-summary">
                    <div class="co-summary-row">
                        <span class="co-summary-lbl">CHALLENGE</span>
                        <span class="co-summary-val" id="coSumType">—</span>
                    </div>
                    <div class="co-summary-row">
                        <span class="co-summary-lbl">ACCOUNT SIZE</span>
                        <span class="co-summary-val" id="coSumSize">—</span>
                    </div>
                    <div class="co-summary-row" id="coSumOptionsRow" style="display:none">
                        <span class="co-summary-lbl">OPTIONS</span>
                        <span class="co-summary-val co-summary-options" id="coSumOptions">—</span>
                    </div>
                    <div class="co-summary-row co-summary-sub">
                        <span class="co-summary-lbl">SUBTOTAL</span>
                        <span class="co-summary-val" id="coSumSubtotal">—</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Discount Coupon</label>
                    <div class="co-coupon-wrap">
                        <input class="form-input co-coupon-input" type="text" id="coCouponInput"
                               placeholder="Enter coupon code"
                               oninput="this.value=this.value.toUpperCase()">
                        <button class="co-apply-btn" type="button" onclick="PurchaseModal.applyCoupon()">APPLY</button>
                    </div>
                    <div id="coCouponMsg" class="co-coupon-msg"></div>
                </div>

                <div class="co-price-block">
                    <div class="co-price-row" id="coDiscountRow" style="display:none">
                        <span class="co-price-lbl">DISCOUNT</span>
                        <span class="co-price-val co-discount-val" id="coDiscountVal">—</span>
                    </div>
                    <div class="co-price-row co-total-row">
                        <span class="co-price-lbl">TOTAL</span>
                        <span class="co-price-total" id="coTotal">—</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Payment Method</label>
                    <div class="pyt-method-row">
                        <button class="pyt-method-btn pyt-method-active" type="button" data-pay="stripe" onclick="PurchaseModal.setPayment(this)">
                            <span class="pyt-method-name">STRIPE</span>
                            <span class="pyt-method-sub">Card · Bank</span>
                        </button>
                        <button class="pyt-method-btn" type="button" data-pay="confirmo" onclick="PurchaseModal.setPayment(this)">
                            <span class="pyt-method-name">CONFIRMO</span>
                            <span class="pyt-method-sub">Crypto</span>
                        </button>
                    </div>
                </div>

                <button class="form-btn" id="coSubmitBtn" type="button" onclick="PurchaseModal.confirm()">
                    PROCEED TO PAYMENT →
                </button>
            </div>

            <!-- ─ STEP 2: RECAP ─ -->
            <div id="coStep2" style="display:none">
                <div class="pyt-success-block">
                    <div class="pyt-success-icon">✓</div>
                    <h2 class="modal-title">Order Confirmed</h2>
                    <p class="modal-sub">Your challenge purchase request has been submitted.</p>
                </div>

                <div class="pyt-recap">
                    <div class="pyt-recap-row">
                        <span class="pyt-recap-lbl">CHALLENGE</span>
                        <span class="pyt-recap-val" id="coRecapChallenge">—</span>
                    </div>
                    <div class="pyt-recap-row">
                        <span class="pyt-recap-lbl">ACCOUNT SIZE</span>
                        <span class="pyt-recap-val" id="coRecapSize">—</span>
                    </div>
                    <div class="pyt-recap-row">
                        <span class="pyt-recap-lbl">AMOUNT</span>
                        <span class="pyt-recap-val" id="coRecapTotal" style="color:var(--accent)">—</span>
                    </div>
                    <div class="pyt-recap-row">
                        <span class="pyt-recap-lbl">PAYMENT</span>
                        <span class="pyt-recap-val" id="coRecapPayment">—</span>
                    </div>
                    <div class="pyt-recap-row">
                        <span class="pyt-recap-lbl">STATUS</span>
                        <span class="pyt-recap-val pyt-status-pending">PENDING</span>
                    </div>
                    <div class="pyt-recap-row">
                        <span class="pyt-recap-lbl">REFERENCE</span>
                        <span class="pyt-recap-val pyt-recap-ref" id="coRecapRef">—</span>
                    </div>
                </div>

                <div class="pyt-reminder">
                    Your order has been received and is awaiting payment confirmation. Your challenge account will be activated within 1–2 business days after payment is confirmed. You will receive a notification by email.
                </div>

                <button class="form-btn" type="button" onclick="PurchaseModal.close()">CLOSE</button>
            </div>

        </div><!-- /.modal-content -->
    </div>
</div>

<!-- ══ PROFIT SPLIT MODAL ══ -->
<div class="modal-overlay" id="profitSplitModal" role="dialog" aria-modal="true"
     data-dc="<?= (int)$dojiCoins ?>">
    <div class="modal modal-psl">
        <canvas class="modal-dot-canvas" aria-hidden="true"></canvas>
        <div class="modal-content">
            <button class="modal-close" onclick="ProfitSplitModal.close()" aria-label="Close">&times;</button>

            <!-- ─ STEP 1: REQUEST ─ -->
            <div id="pslStep1">
                <div class="pyt-header">
                    <div class="pyt-avail-label">DOJI COINS BALANCE</div>
                    <div class="pyt-avail-val pyt-avail-coins"><?= number_format((int)$dojiCoins) ?><span class="pyt-avail-coins-sym">DC</span></div>
                </div>

                <div class="form-group">
                    <label class="form-label">Select Funded Account</label>
                    <div class="psl-accounts">
                        <?php
                        $pslFunded = array_filter($challenges, fn($c) => $c['status'] === 'funded');
                        if (empty($pslFunded)): ?>
                        <div class="psl-no-accounts">No funded accounts available.</div>
                        <?php else: foreach ($pslFunded as $fa):
                            $faRef   = challengeAcctRef($fa['type'], $fa['account_size'], $userId, $acctIndexMap[(int)$fa['id']] ?? 1);
                            $faSplit = (int)($fa['profit_split'] ?? 80);
                        ?>
                        <button class="psl-acct-btn" type="button"
                                data-id="<?= (int)$fa['id'] ?>"
                                data-ref="<?= htmlspecialchars($faRef) ?>"
                                data-size="<?= htmlspecialchars(formatMoney($fa['account_size'])) ?>"
                                data-split="<?= $faSplit ?>"
                                onclick="ProfitSplitModal.setAccount(this)">
                            <span class="psl-acct-ref"><?= htmlspecialchars($faRef) ?></span>
                            <span class="psl-acct-meta"><?= htmlspecialchars(formatMoney($fa['account_size'])) ?> &nbsp;·&nbsp; CURRENT SPLIT <?= $faSplit ?>%</span>
                        </button>
                        <?php endforeach; endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Upgrade Tier</label>
                    <div class="pyt-method-row" id="pslTierRow">
                        <button class="pyt-method-btn" type="button" data-tier="5" data-cost="500" onclick="ProfitSplitModal.setTier(this)">
                            <span class="pyt-method-name">+5%</span>
                            <span class="pyt-method-sub">500 DC</span>
                        </button>
                        <button class="pyt-method-btn" type="button" data-tier="10" data-cost="1000" onclick="ProfitSplitModal.setTier(this)">
                            <span class="pyt-method-name">+10%</span>
                            <span class="pyt-method-sub">1,000 DC</span>
                        </button>
                    </div>
                    <div class="psl-result-line" id="pslResultLine" style="display:none"></div>
                </div>

                <div class="pyt-disclaimer">
                    <div class="pyt-disclaimer-title">⚠ IMPORTANT NOTICE</div>
                    <p class="pyt-disclaimer-body">The use of Doji Coins for profit split upgrades is definitive and irreversible. Once confirmed, the Doji Coins will be permanently deducted from your balance and the upgrade applied to the selected account. This action cannot be undone or refunded under any circumstances.</p>
                    <label class="pyt-check-lbl">
                        <input type="checkbox" id="pslAck" onchange="ProfitSplitModal.validate()">
                        <span>I acknowledge that the use of Doji Coins is definitive and irreversible.</span>
                    </label>
                </div>

                <div class="form-error" id="pslErr"></div>

                <button class="form-btn" id="pslSubmitBtn" type="button" onclick="ProfitSplitModal.submit()" disabled>
                    CONFIRM UPGRADE →
                </button>
            </div>

            <!-- ─ STEP 2: RECAP ─ -->
            <div id="pslStep2" style="display:none">
                <div class="pyt-success-block">
                    <div class="pyt-success-icon">✓</div>
                    <h2 class="modal-title">Upgrade Applied</h2>
                    <p class="modal-sub">Your profit split upgrade has been confirmed.</p>
                </div>

                <div class="pyt-recap">
                    <div class="pyt-recap-row">
                        <span class="pyt-recap-lbl">ACCOUNT</span>
                        <span class="pyt-recap-val psl-recap-ref" id="pslRecapAcct">—</span>
                    </div>
                    <div class="pyt-recap-row">
                        <span class="pyt-recap-lbl">UPGRADE</span>
                        <span class="pyt-recap-val" id="pslRecapTier">—</span>
                    </div>
                    <div class="pyt-recap-row">
                        <span class="pyt-recap-lbl">NEW PROFIT SPLIT</span>
                        <span class="pyt-recap-val" id="pslRecapSplit" style="color:var(--accent)">—</span>
                    </div>
                    <div class="pyt-recap-row">
                        <span class="pyt-recap-lbl">DC SPENT</span>
                        <span class="pyt-recap-val pyt-status-pending" id="pslRecapCost">—</span>
                    </div>
                    <div class="pyt-recap-row">
                        <span class="pyt-recap-lbl">STATUS</span>
                        <span class="pyt-recap-val pyt-status-pending">PENDING</span>
                    </div>
                    <div class="pyt-recap-row">
                        <span class="pyt-recap-lbl">REFERENCE</span>
                        <span class="pyt-recap-val pyt-recap-ref" id="pslRecapRef">—</span>
                    </div>
                </div>

                <div class="pyt-reminder">
                    The use of Doji Coins is definitive and irreversible. Your profit split upgrade will be active within 24 hours. You will receive a notification once the upgrade is confirmed.
                </div>

                <button class="form-btn" type="button" onclick="ProfitSplitModal.close()">CLOSE</button>
            </div>

        </div><!-- /.modal-content -->
    </div>
</div>

<!-- ══ PAYOUT DETAIL MODAL ══ -->
<div class="modal-overlay" id="payoutDetailModal" role="dialog" aria-modal="true">
    <div class="modal modal-psl">
        <canvas class="modal-dot-canvas" aria-hidden="true"></canvas>
        <div class="modal-content">
            <button class="modal-close" onclick="PayoutDetailModal.close()" aria-label="Close">&times;</button>

            <div class="pyt-header">
                <div class="pyt-avail-label">PAYOUT DETAILS</div>
                <div class="pyd-title">PAYOUT <span id="pydNum">#—</span></div>
            </div>

            <div class="pyt-recap" style="margin-bottom:20px">
                <div class="pyt-recap-row">
                    <span class="pyt-recap-lbl">SOURCE</span>
                    <span class="pyt-recap-val psl-recap-ref" id="pydSource">—</span>
                </div>
                <div class="pyt-recap-row">
                    <span class="pyt-recap-lbl">AMOUNT</span>
                    <span class="pyt-recap-val" id="pydAmount" style="color:var(--accent)">—</span>
                </div>
                <div class="pyt-recap-row">
                    <span class="pyt-recap-lbl">METHOD</span>
                    <span class="pyt-recap-val" id="pydMethod">—</span>
                </div>
                <div class="pyt-recap-row">
                    <span class="pyt-recap-lbl">REQUESTED</span>
                    <span class="pyt-recap-val" id="pydRequested">—</span>
                </div>
                <div class="pyt-recap-row" id="pydProcessedRow" style="display:none">
                    <span class="pyt-recap-lbl">PROCESSED</span>
                    <span class="pyt-recap-val" id="pydProcessed">—</span>
                </div>
            </div>

            <div class="form-label" style="margin-bottom:12px">PROGRESS</div>
            <div class="pyd-vprog" id="pydVprog"></div>

            <div class="disc-cta-row" style="margin-top:20px">
                <button class="form-btn disc-btn-ghost" type="button" onclick="PayoutDetailModal.close()">CLOSE</button>
                <button class="form-btn" id="pydCertBtn" type="button" onclick="PayoutDetailModal.downloadCert()" style="display:none">
                    DOWNLOAD CERTIFICATE
                </button>
            </div>
        </div>
    </div>
</div>

<?php if ($showOnbModal): ?>
<!-- ══ ONBOARDING MODAL ══ -->
<div class="onb-overlay" id="onbOverlay">
    <div class="onb-modal">

        <div class="onb-header">
            <div class="onb-header-left">
                <div class="onb-dot active" id="onbDot0"></div>
                <div class="onb-dot" id="onbDot1"></div>
                <div class="onb-dot" id="onbDot2"></div>
                <div class="onb-dot" id="onbDot3"></div>
                <div class="onb-dot" id="onbDot4"></div>
                <span class="onb-step-label" id="onbStepLabel">ÉTAPE 1 / 5</span>
            </div>
            <button class="onb-skip-btn" onclick="Onboarding.skip()">IGNORER ×</button>
        </div>

        <!-- Étape 1 : Bienvenue -->
        <div class="onb-step-panel" id="onbStep0">
            <div class="onb-body">
                <div class="onb-step-num">01 · BIENVENUE</div>
                <div class="onb-step-title">Bienvenue,<br><?= htmlspecialchars($user['first_name']) ?>&nbsp;!</div>
                <p class="onb-step-desc">Doji Funding est une société de trading propriétaire. Prouve tes compétences, obtiens un compte financé et partage les profits jusqu'à 90&nbsp;%. Ce guide te prépare en 5&nbsp;étapes rapides.</p>
            </div>
        </div>

        <!-- Étape 2 : Profil -->
        <div class="onb-step-panel" id="onbStep1" style="display:none">
            <div class="onb-body">
                <div class="onb-step-num">02 · PROFIL</div>
                <div class="onb-step-title">Complète<br>ton profil</div>
                <p class="onb-step-desc">Renseigne ton adresse, numéro de téléphone et pays. Ces informations sont requises pour activer les paiements et accéder à ton compte financé.</p>
                <a class="onb-action-link" onclick="Onboarding.skip(); Dashboard.switchTab('settings'); Dashboard.showProfileSection('profile')">ALLER AU PROFIL →</a>
            </div>
        </div>

        <!-- Étape 3 : KYC -->
        <div class="onb-step-panel" id="onbStep2" style="display:none">
            <div class="onb-body">
                <div class="onb-step-num">03 · VÉRIFICATION</div>
                <div class="onb-step-title">Vérifie ton<br>identité</div>
                <p class="onb-step-desc">Soumets une pièce d'identité officielle (passeport, carte d'identité). Requis pour être éligible aux payouts et à l'accès compte financé.</p>
                <a class="onb-action-link" onclick="Onboarding.skip(); Dashboard.switchTab('settings'); Dashboard.showProfileSection('verification')">SOUMETTRE KYC →</a>
            </div>
        </div>

        <!-- Étape 4 : Challenge -->
        <div class="onb-step-panel" id="onbStep3" style="display:none">
            <div class="onb-body">
                <div class="onb-step-num">04 · CHALLENGE</div>
                <div class="onb-step-title">Lance ton<br>premier challenge</div>
                <p class="onb-step-desc">Configure ton compte de trading — taille, profil de risque et type. Les prix se mettent à jour en temps réel. Lance ta carrière de trader financé dès aujourd'hui.</p>
                <a class="onb-action-link" onclick="Onboarding.skip(); Dashboard.switchTab('configurator')">OUVRIR LE CONFIGURATEUR →</a>
            </div>
        </div>

        <!-- Étape 5 : Discord -->
        <div class="onb-step-panel" id="onbStep4" style="display:none">
            <div class="onb-body">
                <div class="onb-step-num">05 · COMMUNAUTÉ</div>
                <div class="onb-step-title">Rejoins la<br>communauté</div>
                <p class="onb-step-desc">Support en temps réel, annonces exclusives et communauté de traders ambitieux. Rejoins le Discord officiel Doji Funding et échange avec des milliers de traders.</p>
                <a class="onb-action-link" href="https://discord.gg/kNUqAqCppU" target="_blank" rel="noopener">REJOINDRE DISCORD →</a>
            </div>
        </div>

        <div class="onb-footer">
            <button class="onb-back-btn" id="onbBack" onclick="Onboarding.back()" style="visibility:hidden">← RETOUR</button>
            <button class="onb-next-btn" id="onbNext" onclick="Onboarding.next()">SUIVANT →</button>
        </div>

    </div>
</div>
<script>document.addEventListener('DOMContentLoaded', function(){ Onboarding.init(); });</script>
<?php endif; ?>
