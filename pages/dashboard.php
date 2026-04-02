<?php
/**
 * Doji Funding — Dashboard Page
 * Layout: fixed sidebar + fixed topbar + scrollable content (Phidias-style)
 */
?>
<!-- Nothing OS — Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Doto:wght@100..900&family=Space+Grotesk:wght@300;400;500;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
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
?>

<div class="dash">

    <!-- ═══════════════ SIDEBAR ═══════════════ -->
    <aside class="dash-sidebar">

        <!-- Logo -->
        <div class="dash-sidebar-logo">
            <a href="index.php" class="dash-logo-link">
                <img src="<?= LOGO_FILE ?>" alt="Doji" class="dash-logo-img" onerror="this.style.display='none'">
                <span class="dash-logo-brand">DOJI <span class="green">FUNDING</span></span>
            </a>
        </div>

        <!-- Nav -->
        <nav class="dash-nav">
            <button class="dash-nav-item active" data-tab="overview">
                <svg class="dash-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                <span>Dashboard</span>
            </button>
            <button class="dash-nav-item" data-tab="challenges">
                <svg class="dash-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                <span>My Challenges</span>
                <?php if (($overview['active_challenges'] ?? 0) > 0): ?>
                <span class="dash-nav-badge"><?= $overview['active_challenges'] ?></span>
                <?php endif; ?>
            </button>
            <button class="dash-nav-item" data-tab="payouts">
                <svg class="dash-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 100 7h5a3.5 3.5 0 110 7H6"/></svg>
                <span>Payouts</span>
            </button>
            <div class="dash-nav-group" id="navGroupProfile">
                <button class="dash-nav-item" data-tab="settings" id="navProfile">
                    <svg class="dash-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Profile</span>
                    <svg class="dash-nav-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="13" height="13"><polyline points="6 9 12 15 18 9"/></svg>
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
            <button class="dash-theme-switch" id="dashThemeSwitch" onclick="Dashboard.toggleTheme()" title="Toggle theme">
                <svg id="dashThemeIcon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"></svg>
            </button>
            <a href="index.php" class="dash-back-link">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
                dojifunding.com
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            </a>
        </div>

    </aside>

    <!-- ═══════════════ MAIN WRAP ═══════════════ -->
    <div class="dash-main-wrap">

        <!-- ─── TOPBAR ─── -->
        <header class="dash-topbar">
            <div class="dash-topbar-left">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".5"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <span class="dash-topbar-username"><?= htmlspecialchars(strtolower($user['first_name'] . $user['last_name'])) ?></span>
                <button class="dash-topbar-logout" onclick="AuthModal.logout()">Log out</button>
            </div>
            <div class="dash-topbar-right"></div>
        </header>


        <main class="dash-main">

            <div class="dash-page-head">
                <h1 class="dash-page-title" id="dashPageTitle">DASHBOARD</h1>
            </div>

            <!-- ══ TAB: OVERVIEW ══ -->
            <div class="dash-tab active" id="tab-overview">

                <div class="dash-kpis">
                    <div class="dash-kpi">
                        <div class="dash-kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="20" height="20"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg></div>
                        <div class="dash-kpi-label">Active Challenges</div>
                        <div class="dash-kpi-val"><?= $overview['active_challenges'] ?></div>
                        <div class="dash-kpi-sub">of <?= $overview['total_challenges'] ?> total</div>
                    </div>
                    <div class="dash-kpi">
                        <div class="dash-kpi-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="20" height="20"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
                        <div class="dash-kpi-label">Funded Accounts</div>
                        <div class="dash-kpi-val green"><?= $overview['funded_accounts'] ?></div>
                        <div class="dash-kpi-sub"><?= $overview['funded_accounts'] > 0 ? 'Congratulations!' : 'Complete a challenge' ?></div>
                    </div>
                    <div class="dash-kpi">
                        <div class="dash-kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="20" height="20"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 100 7h5a3.5 3.5 0 110 7H6"/></svg></div>
                        <div class="dash-kpi-label">Total Payouts</div>
                        <div class="dash-kpi-val"><?= formatMoney($overview['total_payout_amount']) ?></div>
                        <div class="dash-kpi-sub"><?= $overview['total_payouts'] ?> payout<?= $overview['total_payouts'] !== 1 ? 's' : '' ?></div>
                    </div>
                    <div class="dash-kpi">
                        <div class="dash-kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="20" height="20"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div>
                        <div class="dash-kpi-label">Win Rate</div>
                        <div class="dash-kpi-val"><?= $overview['win_rate'] ?>%</div>
                        <div class="dash-kpi-sub">across all challenges</div>
                    </div>
                </div>

                <?php if (!empty($overview['active_list'])): ?>
                <div class="dash-section">
                    <h2 class="dash-section-title">Active Challenges</h2>
                    <div class="dash-active-grid">
                        <?php foreach ($overview['active_list'] as $ch):
                            $target      = $ch['profit_target_1'];
                            $pnlPct      = $ch['account_size'] > 0 ? ($ch['total_profit'] / $ch['account_size']) * 100 : 0;
                            $progressPct = $target > 0 ? min(100, max(0, ($pnlPct / $target) * 100)) : 0;
                            $ddUsed      = $ch['account_size'] > 0 && $ch['peak_balance'] > 0
                                ? max(0, (($ch['peak_balance'] - $ch['current_balance']) / $ch['account_size']) * 100) : 0;
                        ?>
                        <div class="dash-challenge-card">
                            <div class="dash-cc-header">
                                <div class="dash-cc-type"><?= $ch['type'] === 'one_step' ? '1-STEP' : '2-STEP' ?><?= $ch['phase'] > 1 ? ' · P' . $ch['phase'] : '' ?></div>
                                <?= challengeStatusBadge($ch['status']) ?>
                            </div>
                            <div class="dash-cc-size"><?= formatMoneyShort($ch['account_size']) ?></div>
                            <div class="dash-cc-platform"><?= strtoupper($ch['platform']) ?></div>
                            <div class="dash-cc-progress">
                                <div class="dash-cc-progress-header">
                                    <span>Profit Target</span>
                                    <span class="<?= $pnlPct >= 0 ? 'green' : 'red' ?>"><?= number_format($pnlPct, 2) ?>% / <?= number_format($target, 0) ?>%</span>
                                </div>
                                <div class="dash-cc-bar"><div class="dash-cc-bar-fill" style="width:<?= $progressPct ?>%"></div></div>
                            </div>
                            <div class="dash-cc-metrics">
                                <div class="dash-cc-metric"><span class="dash-cc-metric-label">Balance</span><span class="dash-cc-metric-val"><?= formatMoney($ch['current_balance']) ?></span></div>
                                <div class="dash-cc-metric"><span class="dash-cc-metric-label">DD Used</span><span class="dash-cc-metric-val <?= $ddUsed > ($ch['max_loss'] * 0.7) ? 'red' : '' ?>"><?= number_format($ddUsed, 2) ?>%</span></div>
                                <div class="dash-cc-metric"><span class="dash-cc-metric-label">Days</span><span class="dash-cc-metric-val"><?= $ch['trading_days'] ?>/<?= $ch['min_trading_days'] ?></span></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="dash-empty">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" opacity="0.3"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <h3>No Active Challenges</h3>
                    <p>Start your first challenge and begin your trading journey</p>
                    <a href="challenges.php" class="dash-action-btn">Browse Challenges</a>
                </div>
                <?php endif; ?>
            </div>

            <!-- ══ TAB: MY CHALLENGES ══ -->
            <div class="dash-tab" id="tab-challenges">
                <div class="dash-tab-actions">
                    <p class="dash-tab-count"><?= count($challenges) ?> challenge<?= count($challenges) !== 1 ? 's' : '' ?></p>
                    <a href="challenges.php" class="dash-action-btn">+ New Challenge</a>
                </div>
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
                        <thead><tr><th>Challenge</th><th>Account</th><th>Progress</th><th>Balance</th><th>P&amp;L</th><th>Status</th><th>Date</th></tr></thead>
                        <tbody>
                            <?php foreach ($challenges as $ch):
                                $pnlPct = $ch['account_size'] > 0 ? ($ch['total_profit'] / $ch['account_size']) * 100 : 0;
                                $target = $ch['profit_target_1'];
                            ?>
                            <tr class="dash-row" data-status="<?= $ch['status'] ?>">
                                <td><div class="dash-cell-type"><?= $ch['type'] === 'one_step' ? '1-Step' : '2-Step' ?></div><div class="dash-cell-sub"><?= strtoupper($ch['platform']) ?><?= $ch['phase'] > 1 ? ' · Phase ' . $ch['phase'] : '' ?></div></td>
                                <td class="mono"><?= formatMoneyShort($ch['account_size']) ?></td>
                                <td><div class="dash-mini-bar"><div class="dash-mini-bar-fill" style="width:<?= min(100, max(0, ($pnlPct / $target) * 100)) ?>%"></div></div><span class="dash-cell-sub"><?= number_format($pnlPct, 1) ?>% / <?= $target ?>%</span></td>
                                <td class="mono"><?= formatMoney($ch['current_balance']) ?></td>
                                <td class="mono <?= $ch['total_profit'] >= 0 ? 'green' : 'red' ?>"><?= $ch['total_profit'] >= 0 ? '+' : '' ?><?= formatMoney($ch['total_profit']) ?></td>
                                <td><?= challengeStatusBadge($ch['status']) ?></td>
                                <td class="dash-cell-sub"><?= timeAgo($ch['purchased_at']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="dash-empty">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" opacity="0.3"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                    <h3>No Challenges Yet</h3>
                    <p>Purchase your first challenge to start trading</p>
                    <a href="challenges.php" class="dash-action-btn">Browse Challenges</a>
                </div>
                <?php endif; ?>
            </div>

            <!-- ══ TAB: PAYOUTS ══ -->
            <div class="dash-tab" id="tab-payouts">
                <div class="dash-tab-actions">
                    <p class="dash-tab-count">Total earned: <strong class="green"><?= formatMoney($overview['total_payout_amount']) ?></strong></p>
                </div>
                <?php if (!empty($payouts)): ?>
                <div class="dash-table-wrap">
                    <table class="dash-table">
                        <thead><tr><th>Payout</th><th>Challenge</th><th>Amount</th><th>Method</th><th>Status</th><th>Requested</th></tr></thead>
                        <tbody>
                            <?php foreach ($payouts as $po):
                                $methodLabels = ['crypto_btc'=>'BTC','crypto_eth'=>'ETH','crypto_usdt'=>'USDT','bank_transfer'=>'Bank','wise'=>'Wise','paypal'=>'PayPal'];
                            ?>
                            <tr>
                                <td class="mono">#<?= $po['id'] ?></td>
                                <td><span class="dash-cell-type"><?= $po['challenge_type'] === 'one_step' ? '1-Step' : '2-Step' ?></span> <span class="dash-cell-sub"><?= formatMoneyShort($po['account_size']) ?></span></td>
                                <td class="mono green">+<?= formatMoney($po['amount']) ?></td>
                                <td><?= $methodLabels[$po['method']] ?? '—' ?></td>
                                <td><?= payoutStatusBadge($po['status']) ?></td>
                                <td class="dash-cell-sub"><?= timeAgo($po['requested_at']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="dash-empty">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" opacity="0.3"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 100 7h5a3.5 3.5 0 110 7H6"/></svg>
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
                        <div class="dash-user-card">
                            <div class="dash-user-card-av"><?= $initials ?></div>
                            <div class="dash-user-card-name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
                            <div class="dash-user-card-email"><?= htmlspecialchars($profile['email'] ?? '') ?></div>
                            <div class="dash-user-card-badge <?= $kycClass[$kycStatus] ?>">
                                <?php if ($kycStatus === 'approved'): ?>
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Verified Trader
                                <?php elseif ($kycStatus === 'pending'): ?>
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Under Review
                                <?php else: ?>
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> Not Verified
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($user['created_at'])): ?>
                            <div class="dash-user-card-since">Member since <?= date('M Y', strtotime($user['created_at'])) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Coins widget -->
                        <div class="dash-coins-card">
                            <div class="dash-coins-card-icon">🪙</div>
                            <div class="dash-coins-card-val"><?= number_format($overview['doji_coins'] ?? 0) ?></div>
                            <div class="dash-coins-card-label">Doji Coins</div>
                        </div>
                    </div>

                    <!-- ── Right: Sections ── -->
                    <div class="dash-profile-right">

                        <!-- Section: Personal Information -->
                        <div class="dash-psection" id="psec-profile">
                            <div class="dash-psection-head">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
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
                                            <label>Username</label>
                                            <input type="text" name="username" value="<?= htmlspecialchars($profile['username'] ?? '') ?>" class="dash-input" placeholder="your_pseudo" pattern="[a-zA-Z0-9_]{3,30}">
                                            <span class="dash-form-hint">Letters, numbers, underscores — min 3 characters</span>
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
                                    <div id="profileMsg" class="dash-form-msg"></div>
                                    <button type="submit" class="dash-btn">Save Changes</button>
                                </form>
                            </div>
                        </div>

                        <!-- Section: Security -->
                        <div class="dash-psection" id="psec-security">
                            <div class="dash-psection-head">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                Security
                            </div>
                            <div class="dash-psection-body">

                                <!-- 2FA Banner (emphasized) -->
                                <div class="dash-2fa-banner">
                                    <div class="dash-2fa-left">
                                        <div class="dash-2fa-icon">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/><circle cx="12" cy="16" r="1" fill="currentColor"/></svg>
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

                        <!-- Section: KYC Documents -->
                        <div class="dash-psection" id="psec-verification">
                            <div class="dash-psection-head">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
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
                                            <span class="dash-doc-verified"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Verified</span>
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
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
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
                                            <span class="dash-doc-verified"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Verified</span>
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
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
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
                                            <span class="dash-doc-verified"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Verified</span>
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
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
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
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
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
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
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
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="1"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/><line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/></svg>
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
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
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
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.5 9.5c0-1.1.9-2 2-2h1a2 2 0 010 4h-3a2 2 0 010 4h1a2 2 0 002-2M12 7v10"/></svg>
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
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 100 7h5a3.5 3.5 0 110 7H6"/></svg>
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
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                                Discord
                                <span class="dash-psection-badge" style="background:rgba(255,255,255,0.06);color:var(--dash-text3);margin-left:auto">Coming Soon</span>
                            </div>
                            <div class="dash-psection-body">
                                <p class="dash-psection-desc">Connect your Discord account to access the Doji Funding community server and receive role-based access. This feature will be available shortly.</p>
                            </div>
                        </div>

                        <!-- Section: Feature Suggestions -->
                        <div class="dash-psection" id="psec-suggestions">
                            <div class="dash-psection-head">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
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
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 010 14.14M4.93 4.93a10 10 0 000 14.14"/></svg>
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

        </main>
    </div>

    <!-- Mobile tab bar -->
    <div class="dash-mobile-tabs">
        <button class="dash-mobile-tab active" data-tab="overview">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            <span>Overview</span>
        </button>
        <button class="dash-mobile-tab" data-tab="challenges">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
            <span>Challenges</span>
        </button>
        <button class="dash-mobile-tab" data-tab="payouts">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 100 7h5a3.5 3.5 0 110 7H6"/></svg>
            <span>Payouts</span>
        </button>
        <button class="dash-mobile-tab" data-tab="settings">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span>Profile</span>
        </button>
    </div>

</div>
