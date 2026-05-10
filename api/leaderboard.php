<?php
/**
 * Doji Funding — Global Leaderboard API
 * GET /api/leaderboard.php
 * Returns top 50 public traders ranked by profit % on their best funded account.
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isLoggedIn()) jsonResponse(['error' => 'Unauthorized'], 403);

$db = getDB();
if (!$db) jsonResponse(['error' => 'Service unavailable'], 500);

$currentUserId = (int)$_SESSION['user_id'];

try {
    /*
     * For each public user, pick their single best funded challenge
     * (highest profit %) then join payout aggregates and dominant asset.
     */
    $stmt = $db->prepare('
        SELECT
            u.id                                             AS uid,
            CONCAT(u.first_name, " ", UPPER(LEFT(u.last_name, 1)), ".") AS name,
            COALESCE(NULLIF(u.country, ""), "—")             AS country,
            c.account_size                                   AS size,
            ROUND(c.total_profit, 2)                         AS profit,
            ROUND(c.total_profit / c.account_size * 100, 2)  AS profit_pct,
            CASE WHEN c.total_trades > 0
                THEN ROUND(c.winning_trades / c.total_trades * 100)
                ELSE 0 END                                   AS win_rate,
            c.total_trades                                   AS trades,
            GREATEST(0, DATEDIFF(NOW(), c.started_at))       AS funded_days,
            COALESCE(p.total_payout,   0)                    AS total_payout,
            COALESCE(p.highest_payout, 0)                    AS highest_payout,
            COALESCE(p.payout_count,   0)                    AS payout_count,
            COALESCE(ca.symbol, "—")                         AS pair
        FROM users u
        INNER JOIN challenges c ON c.id = (
            SELECT id FROM challenges
            WHERE user_id = u.id
              AND status = "funded"
              AND account_size > 0
              AND total_profit > 0
            ORDER BY (total_profit / account_size) DESC
            LIMIT 1
        )
        LEFT JOIN (
            SELECT user_id,
                   SUM(amount)   AS total_payout,
                   MAX(amount)   AS highest_payout,
                   COUNT(*)      AS payout_count
            FROM payouts
            WHERE status = "completed"
            GROUP BY user_id
        ) p ON p.user_id = u.id
        LEFT JOIN challenge_assets ca ON ca.id = (
            SELECT id FROM challenge_assets
            WHERE challenge_id = c.id
            ORDER BY ABS(pnl) DESC
            LIMIT 1
        )
        WHERE u.is_public = 1
        ORDER BY (c.total_profit / c.account_size) DESC
        LIMIT 50
    ');
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $rank    = 1;
    $entries = [];
    foreach ($rows as $row) {
        $entry = [
            'rank'          => $rank++,
            'uid'           => (int)$row['uid'],
            'name'          => $row['name'],
            'country'       => $row['country'],
            'size'          => (int)$row['size'],
            'profit'        => (float)$row['profit'],
            'profitPct'     => (float)$row['profit_pct'],
            'winRate'       => (int)$row['win_rate'],
            'pair'          => $row['pair'],
            'avgWin'        => 0,
            'avgLoss'       => 0,
            'avgHold'       => 0,
            'avgRR'         => 0,
            'trades'        => (int)$row['trades'],
            'fundedDays'    => (int)$row['funded_days'],
            'totalPayout'   => (float)$row['total_payout'],
            'highestPayout' => (float)$row['highest_payout'],
            'payoutCount'   => (int)$row['payout_count'],
        ];
        if ((int)$row['uid'] === $currentUserId) {
            $entry['me'] = true;
        }
        $entries[] = $entry;
    }

    jsonResponse(['success' => true, 'data' => $entries]);
} catch (PDOException $e) {
    error_log('Leaderboard error: ' . $e->getMessage());
    jsonResponse(['error' => 'Unable to load leaderboard'], 500);
}
