<?php
/**
 * Doji Funding — Dashboard Page
 * 
 * Sections: Overview | My Challenges | Payouts | Settings
 * Data loaded from dashboard.php entry point.
 */

$user = getCurrentUser();
$kycLabels = ['none' => 'Not Submitted', 'pending' => 'Under Review', 'approved' => 'Verified', 'rejected' => 'Rejected'];
$levelLabels = ['rookie' => 'Rookie', 'trader' => 'Trader', 'pro' => 'Pro', 'elite' => 'Elite'];
$levelIcons = ['rookie' => icon('circle-green', 16), 'trader' => icon('zap', 16), 'pro' => icon('diamond', 16), 'elite' => icon('crown', 16)];
?>

<section class="dash">
    <div class="dash-container">

        <!-- ═══ SIDEBAR ═══ -->
        <aside class="dash-sidebar">
            <div class="dash-profile">
                <div class="dash-avatar">
                    <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                </div>
                <div class="dash-profile-info">
                    <div class="dash-profile-name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
                    <div class="dash-profile-level"><?= $levelIcons[$overview['trader_level'] ?? 'rookie'] ?> <?= $levelLabels[$overview['trader_level'] ?? 'rookie'] ?></div>
                </div>
            </div>

            <nav class="dash-nav">
                <button class="dash-nav-item active" data-tab="overview">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                    Overview
                </button>
                <button class="dash-nav-item" data-tab="challenges">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                    My Challenges
                    <?php if ($overview['active_challenges'] > 0): ?>
                    <span class="dash-nav-badge"><?= $overview['active_challenges'] ?></span>
                    <?php endif; ?>
                </button>
                <button class="dash-nav-item" data-tab="payouts">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 100 7h5a3.5 3.5 0 110 7H6"/></svg>
                    Payouts
                </button>
                <button class="dash-nav-item" data-tab="settings">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><circle cx="12" cy="12" r="3"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                    Settings
                </button>
            </nav>

            <div class="dash-sidebar-footer">
                <div class="dash-coins">
                    <span class="dash-coins-icon">🪙</span>
                    <span class="dash-coins-val"><?= number_format($overview['doji_coins'] ?? 0) ?></span>
                    <span class="dash-coins-label">Doji Coins</span>
                </div>
            </div>
        </aside>

        <!-- ═══ MAIN CONTENT ═══ -->
        <main class="dash-main">

            <!-- ─── TAB: OVERVIEW ─── -->
            <div class="dash-tab active" id="tab-overview">
                <div class="dash-header">
                    <div>
                        <h1 class="dash-title">Welcome back, <span class="green"><?= htmlspecialchars($user['first_name']) ?></span></h1>
                        <p class="dash-sub">Here's your trading overview</p>
                    </div>
                    <a href="challenges.php" class="dash-action-btn">+ New Challenge</a>
                </div>

                <!-- KPI Cards -->
                <div class="dash-kpis">
                    <div class="dash-kpi">
                        <div class="dash-kpi-label">Active Challenges</div>
                        <div class="dash-kpi-val"><?= $overview['active_challenges'] ?></div>
                        <div class="dash-kpi-sub">of <?= $overview['total_challenges'] ?> total</div>
                    </div>
                    <div class="dash-kpi">
                        <div class="dash-kpi-label">Funded Accounts</div>
                        <div class="dash-kpi-val green"><?= $overview['funded_accounts'] ?></div>
                        <div class="dash-kpi-sub"><?= $overview['funded_accounts'] > 0 ? 'Congratulations!' : 'Complete a challenge' ?></div>
                    </div>
                    <div class="dash-kpi">
                        <div class="dash-kpi-label">Total Payouts</div>
                        <div class="dash-kpi-val"><?= formatMoney($overview['total_payout_amount']) ?></div>
                        <div class="dash-kpi-sub"><?= $overview['total_payouts'] ?> payout<?= $overview['total_payouts'] !== 1 ? 's' : '' ?></div>
                    </div>
                    <div class="dash-kpi">
                        <div class="dash-kpi-label">Win Rate</div>
                        <div class="dash-kpi-val"><?= $overview['win_rate'] ?>%</div>
                        <div class="dash-kpi-sub">across all challenges</div>
                    </div>
                </div>

                <!-- Active challenges preview -->
                <?php if (!empty($overview['active_list'])): ?>
                <div class="dash-section">
                    <h2 class="dash-section-title">Active Challenges</h2>
                    <div class="dash-active-grid">
                        <?php foreach ($overview['active_list'] as $ch): 
                            $target = $ch['type'] === 'one_step' ? $ch['profit_target_1'] : $ch['profit_target_1'];
                            $pnlPct = $ch['account_size'] > 0 ? ($ch['total_profit'] / $ch['account_size']) * 100 : 0;
                            $progressPct = $target > 0 ? min(100, max(0, ($pnlPct / $target) * 100)) : 0;
                            $ddUsed = $ch['account_size'] > 0 && $ch['peak_balance'] > 0 
                                ? max(0, (($ch['peak_balance'] - $ch['current_balance']) / $ch['account_size']) * 100) : 0;
                        ?>
                        <div class="dash-challenge-card">
                            <div class="dash-cc-header">
                                <div class="dash-cc-type"><?= $ch['type'] === 'one_step' ? '1-STEP' : '2-STEP' ?> <?= $ch['phase'] > 1 ? '· P' . $ch['phase'] : '' ?></div>
                                <?= challengeStatusBadge($ch['status']) ?>
                            </div>
                            <div class="dash-cc-size"><?= formatMoneyShort($ch['account_size']) ?></div>
                            <div class="dash-cc-platform"><?= strtoupper($ch['platform']) ?></div>

                            <div class="dash-cc-progress">
                                <div class="dash-cc-progress-header">
                                    <span>Profit Target</span>
                                    <span class="<?= $pnlPct >= 0 ? 'green' : 'red' ?>"><?= number_format($pnlPct, 2) ?>% / <?= number_format($target, 0) ?>%</span>
                                </div>
                                <div class="dash-cc-bar">
                                    <div class="dash-cc-bar-fill" style="width:<?= $progressPct ?>%"></div>
                                </div>
                            </div>

                            <div class="dash-cc-metrics">
                                <div class="dash-cc-metric">
                                    <span class="dash-cc-metric-label">Balance</span>
                                    <span class="dash-cc-metric-val"><?= formatMoney($ch['current_balance']) ?></span>
                                </div>
                                <div class="dash-cc-metric">
                                    <span class="dash-cc-metric-label">DD Used</span>
                                    <span class="dash-cc-metric-val <?= $ddUsed > ($ch['max_loss'] * 0.7) ? 'red' : '' ?>"><?= number_format($ddUsed, 2) ?>%</span>
                                </div>
                                <div class="dash-cc-metric">
                                    <span class="dash-cc-metric-label">Days</span>
                                    <span class="dash-cc-metric-val"><?= $ch['trading_days'] ?>/<?= $ch['min_trading_days'] ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="dash-empty">
                    <div class="dash-empty-icon"><?= icon('target', 32) ?></div>
                    <h3>No Active Challenges</h3>
                    <p>Start your first challenge and begin your trading journey</p>
                    <a href="challenges.php" class="dash-action-btn">Browse Challenges</a>
                </div>
                <?php endif; ?>
            </div>

            <!-- ─── TAB: MY CHALLENGES ─── -->
            <div class="dash-tab" id="tab-challenges">
                <div class="dash-header">
                    <div>
                        <h1 class="dash-title">My Challenges</h1>
                        <p class="dash-sub"><?= count($challenges) ?> challenge<?= count($challenges) !== 1 ? 's' : '' ?> total</p>
                    </div>
                    <a href="challenges.php" class="dash-action-btn">+ New Challenge</a>
                </div>

                <!-- Filters -->
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
                        <thead>
                            <tr>
                                <th>Challenge</th>
                                <th>Account</th>
                                <th>Progress</th>
                                <th>Balance</th>
                                <th>P&L</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($challenges as $ch):
                                $pnlPct = $ch['account_size'] > 0 ? ($ch['total_profit'] / $ch['account_size']) * 100 : 0;
                                $target = $ch['type'] === 'one_step' ? $ch['profit_target_1'] : $ch['profit_target_1'];
                            ?>
                            <tr class="dash-row" data-status="<?= $ch['status'] ?>">
                                <td>
                                    <div class="dash-cell-type"><?= $ch['type'] === 'one_step' ? '1-Step' : '2-Step' ?></div>
                                    <div class="dash-cell-sub"><?= strtoupper($ch['platform']) ?> <?= $ch['phase'] > 1 ? '· Phase ' . $ch['phase'] : '' ?></div>
                                </td>
                                <td class="mono"><?= formatMoneyShort($ch['account_size']) ?></td>
                                <td>
                                    <div class="dash-mini-bar">
                                        <div class="dash-mini-bar-fill" style="width:<?= min(100, max(0, ($pnlPct / $target) * 100)) ?>%"></div>
                                    </div>
                                    <span class="dash-cell-sub"><?= number_format($pnlPct, 1) ?>% / <?= $target ?>%</span>
                                </td>
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
                    <div class="dash-empty-icon"><?= icon('chart', 32) ?></div>
                    <h3>No Challenges Yet</h3>
                    <p>Purchase your first challenge to start trading</p>
                    <a href="challenges.php" class="dash-action-btn">Browse Challenges</a>
                </div>
                <?php endif; ?>
            </div>

            <!-- ─── TAB: PAYOUTS ─── -->
            <div class="dash-tab" id="tab-payouts">
                <div class="dash-header">
                    <div>
                        <h1 class="dash-title">Payouts</h1>
                        <p class="dash-sub">Total earned: <strong class="green"><?= formatMoney($overview['total_payout_amount']) ?></strong></p>
                    </div>
                </div>

                <?php if (!empty($payouts)): ?>
                <div class="dash-table-wrap">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Payout</th>
                                <th>Challenge</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Requested</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payouts as $po): 
                                $methodLabels = [
                                    'crypto_btc' => 'BTC', 'crypto_eth' => 'ETH', 'crypto_usdt' => 'USDT',
                                    'bank_transfer' => 'Bank', 'wise' => 'Wise', 'paypal' => 'PayPal'
                                ];
                            ?>
                            <tr class="dash-row">
                                <td class="mono">#<?= $po['id'] ?></td>
                                <td>
                                    <span class="dash-cell-type"><?= $po['challenge_type'] === 'one_step' ? '1-Step' : '2-Step' ?></span>
                                    <span class="dash-cell-sub"><?= formatMoneyShort($po['account_size']) ?></span>
                                </td>
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
                    <div class="dash-empty-icon"><?= icon('coins', 32) ?></div>
                    <h3>No Payouts Yet</h3>
                    <p>Complete a funded challenge to request your first payout</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- ─── TAB: SETTINGS ─── -->
            <div class="dash-tab" id="tab-settings">
                <div class="dash-header">
                    <h1 class="dash-title">Settings</h1>
                </div>

                <div class="dash-settings-grid">
                    <!-- Profile Section -->
                    <div class="dash-card">
                        <h3 class="dash-card-title">Profile Information</h3>
                        <form id="profileForm" class="dash-form">
                            <input type="hidden" name="csrf" value="<?= generateCsrf() ?>">
                            <div class="dash-form-row">
                                <div class="dash-form-group">
                                    <label>First Name</label>
                                    <input type="text" name="first_name" value="<?= htmlspecialchars($profile['first_name'] ?? '') ?>" class="dash-input" required>
                                </div>
                                <div class="dash-form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="last_name" value="<?= htmlspecialchars($profile['last_name'] ?? '') ?>" class="dash-input" required>
                                </div>
                            </div>
                            <div class="dash-form-group">
                                <label>Email</label>
                                <input type="email" value="<?= htmlspecialchars($profile['email'] ?? '') ?>" class="dash-input" disabled>
                                <span class="dash-form-hint">Contact support to change email</span>
                            </div>
                            <div class="dash-form-group">
                                <label>Address</label>
                                <input type="text" name="address" value="<?= htmlspecialchars($profile['address'] ?? '') ?>" class="dash-input" placeholder="Street address">
                            </div>
                            <div class="dash-form-row">
                                <div class="dash-form-group">
                                    <label>City</label>
                                    <input type="text" name="city" value="<?= htmlspecialchars($profile['city'] ?? '') ?>" class="dash-input">
                                </div>
                                <div class="dash-form-group">
                                    <label>Zipcode</label>
                                    <input type="text" name="zipcode" value="<?= htmlspecialchars($profile['zipcode'] ?? '') ?>" class="dash-input">
                                </div>
                            </div>
                            <div class="dash-form-row">
                                <div class="dash-form-group">
                                    <label>Country</label>
                                    <input type="text" name="country" value="<?= htmlspecialchars($profile['country'] ?? '') ?>" class="dash-input">
                                </div>
                                <div class="dash-form-group">
                                    <label>Region/State</label>
                                    <input type="text" name="region" value="<?= htmlspecialchars($profile['region'] ?? '') ?>" class="dash-input">
                                </div>
                            </div>
                            <div class="dash-form-group">
                                <label>Phone</label>
                                <input type="tel" name="phone" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>" class="dash-input" placeholder="+1 234 567 890">
                            </div>
                            <div id="profileMsg" class="dash-form-msg"></div>
                            <button type="submit" class="dash-btn">Save Changes</button>
                        </form>
                    </div>

                    <!-- Password Section -->
                    <div class="dash-card">
                        <h3 class="dash-card-title">Change Password</h3>
                        <form id="passwordForm" class="dash-form">
                            <input type="hidden" name="csrf" value="<?= generateCsrf() ?>">
                            <div class="dash-form-group">
                                <label>Current Password</label>
                                <input type="password" name="current_password" class="dash-input" required>
                            </div>
                            <div class="dash-form-group">
                                <label>New Password</label>
                                <input type="password" name="new_password" class="dash-input" required minlength="8" placeholder="Min. 8 characters">
                            </div>
                            <div class="dash-form-group">
                                <label>Confirm New Password</label>
                                <input type="password" name="confirm_password" class="dash-input" required minlength="8">
                            </div>
                            <div id="passwordMsg" class="dash-form-msg"></div>
                            <button type="submit" class="dash-btn">Update Password</button>
                        </form>
                    </div>

                    <!-- KYC Section -->
                    <div class="dash-card">
                        <h3 class="dash-card-title">Identity Verification (KYC)</h3>
                        <div class="dash-kyc-status">
                            <?php
                            $kycClass = ['none' => 'kyc-none', 'pending' => 'kyc-pending', 'approved' => 'kyc-approved', 'rejected' => 'kyc-rejected'];
                            $kycStatus = $profile['kyc_status'] ?? 'none';
                            ?>
                            <div class="dash-kyc-badge <?= $kycClass[$kycStatus] ?>">
                                <?= $kycLabels[$kycStatus] ?>
                            </div>
                            <?php if ($kycStatus === 'approved'): ?>
                                <p class="dash-kyc-info">Your identity has been verified. You're eligible for payouts.</p>
                            <?php elseif ($kycStatus === 'pending'): ?>
                                <p class="dash-kyc-info">Your documents are being reviewed. This usually takes 24-48 hours.</p>
                            <?php elseif ($kycStatus === 'rejected'): ?>
                                <p class="dash-kyc-info red">Your verification was rejected. Please resubmit with a clearer document.</p>
                            <?php else: ?>
                                <p class="dash-kyc-info">KYC verification is required before your first payout. Please upload a government-issued ID.</p>
                            <?php endif; ?>
                        </div>
                        <?php if ($kycStatus !== 'approved' && $kycStatus !== 'pending'): ?>
                        <form id="kycForm" class="dash-form" enctype="multipart/form-data">
                            <input type="hidden" name="csrf" value="<?= generateCsrf() ?>">
                            <div class="dash-form-group">
                                <label>Upload ID Document</label>
                                <div class="dash-upload">
                                    <input type="file" name="kyc_document" id="kycFile" accept=".jpg,.jpeg,.png,.pdf" required>
                                    <div class="dash-upload-label" id="kycUploadLabel">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                        <span>Click to upload or drag & drop</span>
                                        <span class="dash-upload-sub">JPG, PNG, PDF — Max 5MB</span>
                                    </div>
                                </div>
                            </div>
                            <div id="kycMsg" class="dash-form-msg"></div>
                            <button type="submit" class="dash-btn">Submit for Verification</button>
                        </form>
                        <?php endif; ?>
                    </div>

                    <!-- Referral Section -->
                    <div class="dash-card">
                        <h3 class="dash-card-title">Referral Program</h3>
                        <p class="dash-card-desc">Share your referral code and earn Doji Coins for every friend who purchases a challenge.</p>
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
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><circle cx="12" cy="12" r="3"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
            <span>Settings</span>
        </button>
    </div>
</section>
