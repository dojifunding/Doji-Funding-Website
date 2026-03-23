<?php
/**
 * Doji Funding — Scaling Plan Page
 */
?>

<section class="section" style="padding-top:48px">
<div class="section-inner" style="max-width:900px;margin:0 auto">

    <h1 class="page-title">Scaling <span class="green">Plan</span></h1>
    <p class="page-subtitle">Grow your account up to 10× your initial size. Consistent performance unlocks higher capital.</p>

    <div style="height:48px"></div>

    <!-- Visual progression -->
    <div class="scale-progression">
        <div class="scale-step">
            <div class="scale-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg></div>
            <div class="scale-level">Funded</div>
            <div class="scale-size">$50K</div>
            <div class="scale-desc">Pass your challenge</div>
        </div>
        <div class="scale-arrow">→</div>
        <div class="scale-step">
            <div class="scale-icon"><?= icon('trending', 24) ?></div>
            <div class="scale-level">Scale 1</div>
            <div class="scale-size">$100K</div>
            <div class="scale-desc">3 months profitable</div>
        </div>
        <div class="scale-arrow">→</div>
        <div class="scale-step">
            <div class="scale-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round"><path d="M6 12l-3 9 9-3 12-12-6-6-12 12z"/><path d="M15 7l2 2"/><circle cx="17" cy="5" r="1"/></svg></div>
            <div class="scale-level">Scale 2</div>
            <div class="scale-size">$200K</div>
            <div class="scale-desc">6 months profitable</div>
        </div>
        <div class="scale-arrow">→</div>
        <div class="scale-step active-step">
            <div class="scale-icon"><?= icon('diamond', 24) ?></div>
            <div class="scale-level">Maximum</div>
            <div class="scale-size">$500K</div>
            <div class="scale-desc">Elite trader status</div>
        </div>
    </div>

    <div style="height:48px"></div>

    <hr style="border:none;border-top:1px solid var(--border);margin:0 auto 48px;max-width:120px;opacity:0.5">

    <!-- Scaling by evaluation type -->
    <h2 style="text-align:center;margin-bottom:24px">Scaling by <span class="green">Evaluation Type</span></h2>

    <div class="scale-cards">
        <div class="scale-card">
            <div class="scale-card-badge" style="background:rgba(74,158,255,0.1);color:var(--blue);border:1px solid rgba(74,158,255,0.2)">1 STEP</div>
            <div class="scale-card-multiplier">5×</div>
            <div class="scale-card-sub">Maximum Scaling</div>
            <div class="scale-card-details">
                <div class="scale-card-row"><span>Starting Account</span><span>$5K — $200K</span></div>
                <div class="scale-card-row"><span>Max Scaled Size</span><span>$25K — $1M</span></div>
                <div class="scale-card-row"><span>Scale Increment</span><span>+25% per cycle</span></div>
                <div class="scale-card-row"><span>Cycle Length</span><span>3 months</span></div>
                <div class="scale-card-row"><span>Requirement</span><span>Profitable each month</span></div>
            </div>
        </div>

        <div class="scale-card featured">
            <div class="scale-card-badge" style="background:rgba(16,185,129,0.1);color:var(--green);border:1px solid rgba(16,185,129,0.2)">2 STEP</div>
            <div class="scale-card-multiplier">10×</div>
            <div class="scale-card-sub">Maximum Scaling</div>
            <div class="scale-card-details">
                <div class="scale-card-row"><span>Starting Account</span><span>$5K — $200K</span></div>
                <div class="scale-card-row"><span>Max Scaled Size</span><span>$50K — $2M</span></div>
                <div class="scale-card-row"><span>Scale Increment</span><span>+25% per cycle</span></div>
                <div class="scale-card-row"><span>Cycle Length</span><span>3 months</span></div>
                <div class="scale-card-row"><span>Requirement</span><span>Profitable each month</span></div>
            </div>
        </div>

        <div class="scale-card">
            <div class="scale-card-badge" style="background:rgba(255,159,26,0.1);color:var(--orange);border:1px solid rgba(255,159,26,0.2)">INSTANT</div>
            <div class="scale-card-multiplier">2×</div>
            <div class="scale-card-sub">Maximum Scaling</div>
            <div class="scale-card-details">
                <div class="scale-card-row"><span>Starting Account</span><span>$5K — $200K</span></div>
                <div class="scale-card-row"><span>Max Scaled Size</span><span>$10K — $400K</span></div>
                <div class="scale-card-row"><span>Scale Increment</span><span>+100% once</span></div>
                <div class="scale-card-row"><span>Cycle Length</span><span>3 months</span></div>
                <div class="scale-card-row"><span>Requirement</span><span>3 months profitable</span></div>
            </div>
        </div>
    </div>

    <div style="height:48px"></div>

    <hr style="border:none;border-top:1px solid var(--border);margin:0 auto 48px;max-width:120px;opacity:0.5">

    <!-- Eligibility -->
    <h2 style="text-align:center;margin-bottom:24px">Eligibility <span class="green">Requirements</span></h2>

    <div class="rule-card">
        <div class="rule-card-title">How to Qualify for Scaling</div>
        <div class="rule-list">
            <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Be profitable for the required number of consecutive months (minimum 3)</div>
            <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Maintain a minimum monthly return of 2% on your account</div>
            <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> No rule violations or breaches during the scaling period</div>
            <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Comply with the consistency rule at all times</div>
            <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Complete at least 1 successful payout before first scale-up</div>
            <div class="rule-list-item info"><?= icon('chart', 14) ?> Risk score must remain above 0.5 throughout the period</div>
        </div>
    </div>

    <div class="rule-card">
        <div class="rule-card-title">What Changes After Scaling</div>
        <div class="rule-list">
            <div class="rule-list-item info"><?= icon('trending', 14) ?> Account balance increases by 25% (or 100% for Instant once)</div>
            <div class="rule-list-item info"><?= icon('coins', 14) ?> Dollar-value drawdown limits increase proportionally</div>
            <div class="rule-list-item info"><?= icon('chart', 14) ?> Percentage-based rules remain the same</div>
            <div class="rule-list-item info"><?= icon('info', 14) ?> After payout 3, some parameters become locked for stability</div>
            <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Profit split remains unchanged</div>
            <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> All trading privileges are maintained</div>
        </div>
    </div>

    <!-- Trader Levels -->
    <div style="height:48px"></div>

    <hr style="border:none;border-top:1px solid var(--border);margin:0 auto 48px;max-width:120px;opacity:0.5">

    <h2 style="text-align:center;margin-bottom:24px">Trader <span class="green">Levels</span></h2>
    <p style="text-align:center;color:var(--text3);font-size:14px;margin-bottom:32px">As you progress, you unlock better conditions and exclusive benefits.</p>

    <div class="level-timeline">
        <div class="level-item">
            <div class="level-badge"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--orange)" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M8.21 13.89L7 23l5-3 5 3-1.21-9.12"/></svg></div>
            <div class="level-info">
                <div class="level-name">Rookie</div>
                <div class="level-duration">0 — 3 months</div>
                <div class="level-desc">Standard restrictions apply. Focus on consistency and risk management. Build your track record.</div>
            </div>
        </div>
        <div class="level-item">
            <div class="level-badge"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--text3)" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M8.21 13.89L7 23l5-3 5 3-1.21-9.12"/></svg></div>
            <div class="level-info">
                <div class="level-name">Trader</div>
                <div class="level-duration">3 — 6 months</div>
                <div class="level-desc">Reduced restrictions. Access to additional configuration options and priority support.</div>
            </div>
        </div>
        <div class="level-item">
            <div class="level-badge"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#FFD700" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M8.21 13.89L7 23l5-3 5 3-1.21-9.12"/></svg></div>
            <div class="level-info">
                <div class="level-name">Pro</div>
                <div class="level-duration">6 — 12 months</div>
                <div class="level-desc">Premium configurations unlocked. Reduced fees on new challenges. Faster payout processing.</div>
            </div>
        </div>
        <div class="level-item featured">
            <div class="level-badge"><?= icon('diamond', 20) ?></div>
            <div class="level-info">
                <div class="level-name">Elite</div>
                <div class="level-duration">12+ months</div>
                <div class="level-desc">VIP conditions. Minimal restrictions. Priority support. Exclusive scaling opportunities. Highest profit splits.</div>
            </div>
        </div>
    </div>

    <div style="height:48px"></div>

    <div style="text-align:center">
        <a href="challenges.php" style="text-decoration:none" class="btn-primary-lg">Start Your Challenge</a>
    </div>

</div>
</section>

<?php include 'includes/community.php'; ?>
