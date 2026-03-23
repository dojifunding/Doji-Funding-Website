<?php
/**
 * Doji Funding — Trading Rules Page
 */
?>

<section class="section" style="padding-top:48px">
<div class="section-inner" style="max-width:860px;margin:0 auto">

    <h1 class="page-title">Trading <span class="green">Rules</span></h1>
    <p class="page-subtitle">Clear, transparent rules for every evaluation type. No hidden conditions.</p>

    <div style="height:40px"></div>

    <!-- Rule nav tabs -->
    <div class="rule-tabs">
        <button class="rule-tab active" onclick="showRuleTab('general',this)">General Rules</button>
        <button class="rule-tab" onclick="showRuleTab('onestep',this)">1 Step Challenge</button>
        <button class="rule-tab" onclick="showRuleTab('twostep',this)">2 Step Challenge</button>
        <button class="rule-tab" onclick="showRuleTab('instant',this)">Instant Funding</button>
    </div>

    <!-- GENERAL RULES -->
    <div class="rule-section active" id="rule-general">

        <div class="rule-card">
            <div class="rule-card-title"><?= icon('chart') ?> Drawdown Calculation</div>
            <p>All drawdown calculations are based on your <strong>initial account balance</strong> unless otherwise specified in your configuration.</p>
            <div class="rule-detail">
                <span class="rule-dt">Static Drawdown</span>
                <span class="rule-dd">Calculated from initial balance. Does not change as your equity grows. If your starting balance is $50,000 with 10% max drawdown, your absolute floor is $45,000 regardless of profits made.</span>
            </div>
            <div class="rule-detail">
                <span class="rule-dt">Trailing Drawdown</span>
                <span class="rule-dd">Follows your highest equity reached. If your account peaks at $55,000 with 10% trailing drawdown, your new floor becomes $49,500. The trail locks once it reaches your initial balance + profit target.</span>
            </div>
            <div class="rule-detail">
                <span class="rule-dt">End of Day Drawdown</span>
                <span class="rule-dd">Daily drawdown is calculated based on your balance at the start of each trading day (00:00 UTC). Intraday equity dips below the threshold will trigger a breach even if balance recovers by end of day.</span>
            </div>
        </div>

        <div class="rule-card">
            <div class="rule-card-title"><?= icon('calendar') ?> Trading Days</div>
            <p>A trading day is defined as any day where <strong>at least one trade is opened and closed</strong>. Pending orders that are not triggered do not count as a trading day.</p>
            <div class="rule-highlight">
                Minimum trading days must be completed before you can pass the challenge or request a payout. These days do not need to be consecutive.
            </div>
        </div>

        <div class="rule-card">
            <div class="rule-card-title"><?= icon('trending') ?> Consistency Rule</div>
            <p>No single trading day can account for more than a set percentage of your total profits. This ensures consistent performance rather than reliance on a single lucky trade.</p>
            <div class="rule-grid">
                <div class="rule-mini">
                    <span class="rule-mini-label">1 Step</span>
                    <span class="rule-mini-val">40%</span>
                    <span class="rule-mini-desc">default</span>
                </div>
                <div class="rule-mini">
                    <span class="rule-mini-label">2 Step</span>
                    <span class="rule-mini-val">45%</span>
                    <span class="rule-mini-desc">default</span>
                </div>
                <div class="rule-mini">
                    <span class="rule-mini-label">Instant</span>
                    <span class="rule-mini-val">50%</span>
                    <span class="rule-mini-desc">Best Day Rule</span>
                </div>
            </div>
        </div>

        <div class="rule-card">
            <div class="rule-card-title"><?= icon('bot') ?> Expert Advisors & Automation</div>
            <div class="rule-list">
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Standard Expert Advisors (EA) are allowed</div>
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Copy trading from your own accounts is allowed</div>
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Trade management tools (trailing SL, partial close) are allowed</div>
                <div class="rule-list-item forbidden"><?= icon('x-circle', 14) ?> High Frequency Trading (HFT) bots are prohibited</div>
                <div class="rule-list-item forbidden"><?= icon('x-circle', 14) ?> Latency arbitrage or exploit-based EAs are prohibited</div>
                <div class="rule-list-item forbidden"><?= icon('x-circle', 14) ?> Third-party copy trading services are prohibited</div>
            </div>
        </div>

        <div class="rule-card">
            <div class="rule-card-title">🚫 Prohibited Strategies</div>
            <div class="rule-list">
                <div class="rule-list-item forbidden"><?= icon('x-circle', 14) ?> Martingale / Grid Trading</div>
                <div class="rule-list-item forbidden"><?= icon('x-circle', 14) ?> Hedging between multiple accounts</div>
                <div class="rule-list-item forbidden"><?= icon('x-circle', 14) ?> Gap trading exploitation</div>
                <div class="rule-list-item forbidden"><?= icon('x-circle', 14) ?> Tick scalping (trades under 30 seconds)</div>
                <div class="rule-list-item forbidden"><?= icon('x-circle', 14) ?> Account management by third parties</div>
            </div>
        </div>

        <div class="rule-card">
            <div class="rule-card-title">📰 News Trading</div>
            <p>News trading eligibility depends on your evaluation type and configuration:</p>
            <div class="rule-grid">
                <div class="rule-mini">
                    <span class="rule-mini-label">1 Step</span>
                    <span class="rule-mini-val green">Allowed*</span>
                    <span class="rule-mini-desc">If target > 8%</span>
                </div>
                <div class="rule-mini">
                    <span class="rule-mini-label">2 Step</span>
                    <span class="rule-mini-val green">Allowed</span>
                    <span class="rule-mini-desc">5min buffer if risky</span>
                </div>
                <div class="rule-mini">
                    <span class="rule-mini-label">Instant</span>
                    <span class="rule-mini-val" style="color:var(--red)">Forbidden</span>
                    <span class="rule-mini-desc">All news events</span>
                </div>
            </div>
            <p style="margin-top:12px;font-size:12px;color:var(--text3)">Major news events include: NFP, FOMC, CPI, ECB/BOE/BOJ rate decisions, GDP releases. A 5-minute buffer means no new positions may be opened 5 minutes before or after the event.</p>
        </div>

        <div class="rule-card">
            <div class="rule-card-title"><?= icon('coins') ?> Payout Rules</div>
            <div class="rule-detail">
                <span class="rule-dt">Minimum Profit</span>
                <span class="rule-dd">You must reach the minimum profit threshold before requesting a payout: 3% (Instant), 2% (1 Step), 1% (2 Step).</span>
            </div>
            <div class="rule-detail">
                <span class="rule-dt">KYC Verification</span>
                <span class="rule-dd">Identity verification (passport/ID + proof of address) is required before your first payout. Processing takes 24-48 hours.</span>
            </div>
            <div class="rule-detail">
                <span class="rule-dt">Payout Processing</span>
                <span class="rule-dd">Payouts are processed within 1-3 business days via bank transfer, crypto (USDT/USDC), or supported e-wallets.</span>
            </div>
            <div class="rule-detail">
                <span class="rule-dt">Account Limit</span>
                <span class="rule-dd">Maximum 1 funded account per person. Multiple evaluation accounts are allowed during the challenge phase.</span>
            </div>
        </div>

        <div class="rule-card">
            <div class="rule-card-title"><?= icon('calendar') ?> Weekend & Overnight Holding</div>
            <div class="rule-detail">
                <span class="rule-dt">1 Step Challenge</span>
                <span class="rule-dd">Weekend holding allowed if minimum trading days ≥ 5. Overnight holding always allowed.</span>
            </div>
            <div class="rule-detail">
                <span class="rule-dt">2 Step Challenge</span>
                <span class="rule-dd">Both weekend and overnight holding are allowed on all configurations.</span>
            </div>
            <div class="rule-detail">
                <span class="rule-dt">Instant Funding</span>
                <span class="rule-dd">Weekend holding is prohibited (except crypto pairs). All positions must be closed before market close on Friday.</span>
            </div>
        </div>

    </div>

    <!-- 1 STEP RULES -->
    <div class="rule-section" id="rule-onestep">
        <div class="rule-card highlight-green">
            <div class="rule-card-title"><?= icon('target') ?> 1 Step Challenge — Overview</div>
            <p>A single-phase evaluation designed for traders who want to get funded fast. Prove your skills once and receive your funded account.</p>
            <div class="rule-grid cols-4">
                <div class="rule-mini"><span class="rule-mini-label">Profit Target</span><span class="rule-mini-val">5-15%</span><span class="rule-mini-desc">default 8%</span></div>
                <div class="rule-mini"><span class="rule-mini-label">Daily Loss</span><span class="rule-mini-val">2-8%</span><span class="rule-mini-desc">default 4%</span></div>
                <div class="rule-mini"><span class="rule-mini-label">Max Loss</span><span class="rule-mini-val">4-12%</span><span class="rule-mini-desc">default 8%</span></div>
                <div class="rule-mini"><span class="rule-mini-label">Profit Split</span><span class="rule-mini-val">60-90%</span><span class="rule-mini-desc">default 75%</span></div>
            </div>
        </div>
        <div class="rule-card">
            <div class="rule-card-title">Specific Rules</div>
            <div class="rule-list">
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Min trading days: 3-10 days (configurable)</div>
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Consistency rule: 30-50% (default 40%)</div>
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> EA/Bots allowed (except HFT)</div>
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Copy trading with verification</div>
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> News trading if profit target > 8%</div>
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Weekend holding if min days ≥ 5</div>
                <div class="rule-list-item info"><?= icon('chart', 14) ?> Maximum scaling: 5× after validation</div>
                <div class="rule-list-item info"><?= icon('coins', 14) ?> Minimum payout: 2% profit</div>
            </div>
        </div>
    </div>

    <!-- 2 STEP RULES -->
    <div class="rule-section" id="rule-twostep">
        <div class="rule-card highlight-green">
            <div class="rule-card-title"><?= icon('chart') ?> 2 Step Challenge — Overview</div>
            <p>The classic two-phase evaluation with maximum flexibility. Complete both phases to unlock your funded account with the best conditions.</p>
            <div class="rule-grid cols-4">
                <div class="rule-mini"><span class="rule-mini-label">Phase 1 Target</span><span class="rule-mini-val">5-12%</span><span class="rule-mini-desc">default 8%</span></div>
                <div class="rule-mini"><span class="rule-mini-label">Phase 2 Target</span><span class="rule-mini-val">3-8%</span><span class="rule-mini-desc">default 5%</span></div>
                <div class="rule-mini"><span class="rule-mini-label">Daily Loss</span><span class="rule-mini-val">3-8%</span><span class="rule-mini-desc">default 5%</span></div>
                <div class="rule-mini"><span class="rule-mini-label">Profit Split</span><span class="rule-mini-val">70-90%</span><span class="rule-mini-desc">default 80%</span></div>
            </div>
        </div>
        <div class="rule-card">
            <div class="rule-card-title">Maximum Freedom</div>
            <div class="rule-list">
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> News trading allowed (5min buffer if risky config)</div>
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Weekend holding allowed</div>
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> EA/Copy trading allowed</div>
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> All instruments available</div>
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Pre-optimized config templates available</div>
                <div class="rule-list-item info"><?= icon('chart', 14) ?> Maximum scaling: 10× after validation</div>
                <div class="rule-list-item info"><?= icon('coins', 14) ?> Minimum payout: 1% profit</div>
                <div class="rule-list-item info"><?= icon('calendar', 14) ?> Min trading days: 5-20 total (configurable)</div>
            </div>
        </div>
    </div>

    <!-- INSTANT FUNDING RULES -->
    <div class="rule-section" id="rule-instant">
        <div class="rule-card" style="border-color:rgba(255,159,26,0.3);background:rgba(255,159,26,0.03)">
            <div class="rule-card-title"><?= icon('zap') ?> Instant Funding — Overview</div>
            <p style="color:var(--orange);font-weight:600;font-size:13px">Coming Soon — This evaluation type is currently in development.</p>
            <p>Instant Funding provides immediate access to a funded account without any evaluation phase. Designed for experienced, conservative traders.</p>
            <div class="rule-grid cols-4">
                <div class="rule-mini"><span class="rule-mini-label">Daily Loss</span><span class="rule-mini-val">2-6%</span><span class="rule-mini-desc">default 3%</span></div>
                <div class="rule-mini"><span class="rule-mini-label">Max Loss</span><span class="rule-mini-val">4-10%</span><span class="rule-mini-desc">default 6%</span></div>
                <div class="rule-mini"><span class="rule-mini-label">Profit Split</span><span class="rule-mini-val">50-80%</span><span class="rule-mini-desc">default 50%</span></div>
                <div class="rule-mini"><span class="rule-mini-label">Scaling</span><span class="rule-mini-val">2×</span><span class="rule-mini-desc">after 3 months</span></div>
            </div>
        </div>
        <div class="rule-card">
            <div class="rule-card-title">Strict Protection Rules</div>
            <div class="rule-list">
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> 50% Best Day Rule enforced</div>
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Stop Loss mandatory on every position</div>
                <div class="rule-list-item forbidden"><?= icon('x-circle', 14) ?> News trading prohibited</div>
                <div class="rule-list-item forbidden"><?= icon('x-circle', 14) ?> Weekend holding prohibited (except crypto)</div>
                <div class="rule-list-item forbidden"><?= icon('x-circle', 14) ?> Martingale / Grid trading prohibited</div>
                <div class="rule-list-item forbidden"><?= icon('x-circle', 14) ?> HFT Expert Advisors prohibited</div>
                <div class="rule-list-item info"><?= icon('info', 14) ?> IP Lock for 30 days after activation</div>
                <div class="rule-list-item info"><?= icon('coins', 14) ?> Minimum 3% profit for first payout</div>
            </div>
        </div>
    </div>

    <div style="height:32px"></div>
    <p style="text-align:center;color:var(--text3);font-size:12px">
        Rules are subject to change. Always refer to your account dashboard for the specific rules applied to your configuration.<br>
        Last updated: <?= date('F Y') ?>. Operated by Volatys Dynamics LTD.
    </p>

</div>
</section>

<script>
function showRuleTab(id, btn) {
    document.querySelectorAll('.rule-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.rule-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('rule-' + id).classList.add('active');
    btn.classList.add('active');
}
</script>

<?php include 'includes/community.php'; ?>
