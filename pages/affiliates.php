<?php
/**
 * Doji Funding — Affiliate Program Page
 */
?>

<!-- HERO with Circuit Board Animation -->
<section class="hero" style="min-height:480px;padding:100px 32px 80px">
    <canvas class="hero-globe" id="circuitCanvas"></canvas>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="badge">Partner With Us</div>
        <h1>Affiliate <span class="green">Program</span></h1>
        <p class="subtitle">Earn recurring commissions by referring traders to Doji Funding. No limits, real-time tracking, and fast payouts.</p>
        <div class="cta-row">
            <a style="text-decoration:none" class="btn-primary-lg" href="#apply">Become an Affiliate</a>
        </div>
    </div>
</section>

<section class="section" style="padding-top:48px">
<div class="section-inner" style="max-width:900px;margin:0 auto">

    <div style="height:48px"></div>

    <!-- Stats highlight -->
    <div class="aff-stats">
        <div class="aff-stat">
            <div class="aff-stat-val">15%</div>
            <div class="aff-stat-label">Commission Rate</div>
        </div>
        <div class="aff-stat">
            <div class="aff-stat-val">30 Days</div>
            <div class="aff-stat-label">Cookie Duration</div>
        </div>
        <div class="aff-stat">
            <div class="aff-stat-val">$0</div>
            <div class="aff-stat-label">Joining Fee</div>
        </div>
        <div class="aff-stat">
            <div class="aff-stat-val">Weekly</div>
            <div class="aff-stat-label">Payout Cycle</div>
        </div>
    </div>

    <div style="height:48px"></div>

    <!-- How it works -->
    <h2 style="text-align:center;margin-bottom:32px">How It <span class="green">Works</span></h2>

    <div class="aff-steps">
        <div class="aff-step">
            <div class="aff-step-num">1</div>
            <div class="aff-step-content">
                <h3>Sign Up</h3>
                <p>Create your free affiliate account in minutes. No approval process — start earning immediately after registration.</p>
            </div>
        </div>
        <div class="aff-step">
            <div class="aff-step-num">2</div>
            <div class="aff-step-content">
                <h3>Share Your Link</h3>
                <p>Get your unique referral link and promotional materials. Share on social media, your website, YouTube, Discord — anywhere.</p>
            </div>
        </div>
        <div class="aff-step">
            <div class="aff-step-num">3</div>
            <div class="aff-step-content">
                <h3>Earn Commissions</h3>
                <p>Earn 15% on every challenge purchase made through your link. Track conversions in real-time through your affiliate dashboard.</p>
            </div>
        </div>
        <div class="aff-step">
            <div class="aff-step-num">4</div>
            <div class="aff-step-content">
                <h3>Get Paid</h3>
                <p>Withdraw your earnings weekly via bank transfer, crypto (USDT/USDC), or supported e-wallets. No minimum threshold.</p>
            </div>
        </div>
    </div>

    <div style="height:48px"></div>

    <!-- Commission structure -->
    <h2 style="text-align:center;margin-bottom:32px">Commission <span class="green">Structure</span></h2>

    <div class="aff-tiers">
        <div class="aff-tier">
            <div class="aff-tier-name">Standard</div>
            <div class="aff-tier-rate">15%</div>
            <div class="aff-tier-desc">0 — 50 referrals/month</div>
            <div class="aff-tier-example">Example: $249 challenge → you earn <strong>$37.35</strong></div>
        </div>
        <div class="aff-tier featured">
            <div class="aff-tier-name">Silver</div>
            <div class="aff-tier-rate">20%</div>
            <div class="aff-tier-desc">50 — 150 referrals/month</div>
            <div class="aff-tier-example">Example: $249 challenge → you earn <strong>$49.80</strong></div>
        </div>
        <div class="aff-tier">
            <div class="aff-tier-name">Gold</div>
            <div class="aff-tier-rate">25%</div>
            <div class="aff-tier-desc">150+ referrals/month</div>
            <div class="aff-tier-example">Example: $249 challenge → you earn <strong>$62.25</strong></div>
        </div>
    </div>

    <div style="height:48px"></div>

    <!-- Resources -->
    <h2 style="text-align:center;margin-bottom:32px">Affiliate <span class="green">Resources</span></h2>

    <div class="aff-resources">
        <div class="rule-card">
            <div class="rule-card-title">📦 What You Get</div>
            <div class="rule-list">
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Unique referral link with 30-day cookie tracking</div>
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Real-time dashboard with clicks, conversions, and earnings</div>
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Branded banners, social media assets, and ad copy</div>
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Custom promo codes for your audience</div>
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Dedicated affiliate manager (Silver+ tiers)</div>
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Weekly payouts with no minimum withdrawal</div>
            </div>
        </div>

        <div class="rule-card">
            <div class="rule-card-title"><?= icon('target') ?> Perfect For</div>
            <div class="rule-list">
                <div class="rule-list-item info">📹 Trading content creators (YouTube, TikTok, Instagram)</div>
                <div class="rule-list-item info">📝 Financial bloggers and review sites</div>
                <div class="rule-list-item info"><?= icon('message', 14) ?> Discord & Telegram community owners</div>
                <div class="rule-list-item info">🎓 Trading educators and mentors</div>
                <div class="rule-list-item info"><?= icon('chart', 14) ?> Forex signal providers</div>
                <div class="rule-list-item info">🌐 Website owners in the finance niche</div>
            </div>
        </div>
    </div>

    <div style="height:48px"></div>

    <!-- CTA -->
    <div class="aff-cta">
        <h2>Ready to Start Earning?</h2>
        <p>Join the Doji Funding® affiliate program and earn commissions on every referral. No limits, no caps.</p>
        <div style="display:flex;gap:12px;justify-content:center;margin-top:24px;flex-wrap:wrap">
            <a href="#" style="text-decoration:none" class="btn-primary-lg" onclick="AuthModal.open('signup');return false">Join Affiliate Program</a>
            <a href="mailto:affiliates@dojifunding.com" style="text-decoration:none" class="btn-outline-lg">Contact Affiliate Team</a>
        </div>
    </div>

    <div style="height:32px"></div>
    <p style="text-align:center;color:var(--text3);font-size:12px">
        Commission rates and terms are subject to the <a href="terms.php" class="green">Terms of Service</a>.
        Abuse of the affiliate program (self-referrals, fake traffic, etc.) will result in immediate termination and forfeiture of earnings.
    </p>

</div>
</section>

<?php include 'includes/community.php'; ?>
