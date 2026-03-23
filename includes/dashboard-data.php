<?php
/**
 * Doji Funding — Dashboard Data Layer
 * 
 * Queries for dashboard sections: overview, challenges, payouts, settings.
 * All functions require a valid user_id.
 */

/**
 * Get dashboard overview stats
 */
function getDashboardOverview($userId) {
    $db = getDB();
    if (!$db) return null;

    $out = [
        'total_challenges' => 0,
        'active_challenges' => 0,
        'funded_accounts'  => 0,
        'total_payouts'    => 0,
        'total_payout_amount' => 0,
        'total_spent'      => 0,
        'win_rate'         => 0,
        'avg_profit'       => 0,
        'doji_coins'       => 0,
        'trader_level'     => 'rookie',
        'active_list'      => [],
    ];

    try {
        // Challenge counts
        $stmt = $db->prepare('SELECT 
            COUNT(*) AS total,
            SUM(status = "active") AS active,
            SUM(status = "funded") AS funded,
            SUM(final_price) AS spent,
            AVG(CASE WHEN total_trades > 0 THEN (winning_trades / total_trades) * 100 ELSE 0 END) AS win_rate,
            AVG(CASE WHEN status IN ("passed","funded") THEN total_profit ELSE NULL END) AS avg_profit
            FROM challenges WHERE user_id = ?');
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        if ($row) {
            $out['total_challenges'] = (int)$row['total'];
            $out['active_challenges'] = (int)$row['active'];
            $out['funded_accounts']  = (int)$row['funded'];
            $out['total_spent']      = (float)$row['spent'];
            $out['win_rate']         = round((float)$row['win_rate'], 1);
            $out['avg_profit']       = round((float)$row['avg_profit'], 2);
        }

        // Payout totals
        $stmt = $db->prepare('SELECT COUNT(*) AS cnt, COALESCE(SUM(amount),0) AS total 
            FROM payouts WHERE user_id = ? AND status = "completed"');
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        if ($row) {
            $out['total_payouts']        = (int)$row['cnt'];
            $out['total_payout_amount']  = (float)$row['total'];
        }

        // User info
        $stmt = $db->prepare('SELECT doji_coins, trader_level FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        if ($row) {
            $out['doji_coins']   = (int)$row['doji_coins'];
            $out['trader_level'] = $row['trader_level'];
        }

        // Active challenges (for overview cards)
        $stmt = $db->prepare('SELECT id, type, account_size, status, phase,
            profit_target_1, profit_target_2, daily_loss, max_loss, profit_split,
            current_balance, peak_balance, total_profit, total_trades,
            winning_trades, trading_days, min_trading_days, consistency_rule,
            started_at, platform
            FROM challenges WHERE user_id = ? AND status = "active" 
            ORDER BY created_at DESC LIMIT 5');
        $stmt->execute([$userId]);
        $out['active_list'] = $stmt->fetchAll();

    } catch (PDOException $e) {
        error_log('Dashboard overview error: ' . $e->getMessage());
    }

    return $out;
}

/**
 * Get all challenges for user
 */
function getUserChallenges($userId, $filter = 'all') {
    $db = getDB();
    if (!$db) return [];

    try {
        $where = 'user_id = ?';
        $params = [$userId];

        if ($filter === 'active') {
            $where .= ' AND status = "active"';
        } elseif ($filter === 'funded') {
            $where .= ' AND status = "funded"';
        } elseif ($filter === 'completed') {
            $where .= ' AND status IN ("passed","failed","expired")';
        }

        $stmt = $db->prepare("SELECT * FROM challenges WHERE $where ORDER BY created_at DESC");
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Challenges query error: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get payout history for user
 */
function getUserPayouts($userId) {
    $db = getDB();
    if (!$db) return [];

    try {
        $stmt = $db->prepare('SELECT p.*, c.account_size, c.type AS challenge_type
            FROM payouts p
            JOIN challenges c ON c.id = p.challenge_id
            WHERE p.user_id = ?
            ORDER BY p.requested_at DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Payouts query error: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get user profile for settings
 */
function getUserProfile($userId) {
    $db = getDB();
    if (!$db) return null;

    try {
        $stmt = $db->prepare('SELECT id, email, first_name, last_name, phone, address, city, zipcode, country, region,
            avatar_url, kyc_status, kyc_submitted_at, kyc_reviewed_at,
            trader_level, doji_coins, referral_code, email_verified, created_at
            FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log('Profile query error: ' . $e->getMessage());
        return null;
    }
}

/**
 * Get user notifications
 */
function getUserNotifications($userId, $limit = 10) {
    $db = getDB();
    if (!$db) return [];

    try {
        $stmt = $db->prepare('SELECT * FROM notifications 
            WHERE user_id = ? ORDER BY created_at DESC LIMIT ?');
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Notifications query error: ' . $e->getMessage());
        return [];
    }
}

/**
 * Format helpers
 */
function formatMoney($amount, $currency = 'USD') {
    return '$' . number_format((float)$amount, 2);
}

function formatMoneyShort($amount) {
    if ($amount >= 1000000) return '$' . number_format($amount / 1000000, 1) . 'M';
    if ($amount >= 1000) return '$' . number_format($amount / 1000, 0) . 'K';
    return '$' . number_format($amount, 0);
}

function challengeStatusBadge($status) {
    $map = [
        'active'   => ['Active', 'badge-active'],
        'passed'   => ['Passed', 'badge-passed'],
        'failed'   => ['Failed', 'badge-failed'],
        'funded'   => ['Funded', 'badge-funded'],
        'expired'  => ['Expired', 'badge-expired'],
        'refunded' => ['Refunded', 'badge-refunded'],
    ];
    $s = $map[$status] ?? ['Unknown', 'badge-default'];
    return '<span class="dash-badge ' . $s[1] . '">' . $s[0] . '</span>';
}

function payoutStatusBadge($status) {
    $map = [
        'pending'    => ['Pending', 'badge-pending'],
        'processing' => ['Processing', 'badge-active'],
        'completed'  => ['Completed', 'badge-funded'],
        'rejected'   => ['Rejected', 'badge-failed'],
        'cancelled'  => ['Cancelled', 'badge-expired'],
    ];
    $s = $map[$status] ?? ['Unknown', 'badge-default'];
    return '<span class="dash-badge ' . $s[1] . '">' . $s[0] . '</span>';
}

function timeAgo($datetime) {
    if (!$datetime) return 'N/A';
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    if ($diff->y > 0) return $diff->y . 'y ago';
    if ($diff->m > 0) return $diff->m . 'mo ago';
    if ($diff->d > 0) return $diff->d . 'd ago';
    if ($diff->h > 0) return $diff->h . 'h ago';
    if ($diff->i > 0) return $diff->i . 'min ago';
    return 'just now';
}
