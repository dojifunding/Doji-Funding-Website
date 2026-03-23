<?php
/**
 * Doji Funding — About Page
 *
 * Company story, mission, values, and corporate information.
 */
?>

<!-- HERO HEADER -->
<section class="section" style="padding-top:48px">
<div class="section-inner" style="max-width:900px;margin:0 auto">

    <h1 class="page-title">About <span class="green">Doji Funding<sup class="tm">®</sup></span></h1>
    <p class="page-subtitle">
        The first fully customizable prop trading firm. Built by traders, for traders.
    </p>

    <div style="height:48px"></div>

    <!-- OUR MISSION -->
    <h2 style="text-align:center;margin-bottom:12px">Our <span class="green">Mission</span></h2>
    <p style="text-align:center;color:var(--text2);font-size:15px;max-width:700px;margin:0 auto 32px;line-height:1.8">
        We believe every skilled trader deserves access to capital — regardless of background, location, or account size.
        Traditional prop firms offer rigid, one-size-fits-all programs that ignore how diverse trading styles really are.
        Doji Funding was created to change that.
    </p>
    <p style="text-align:center;color:var(--text2);font-size:15px;max-width:700px;margin:0 auto 32px;line-height:1.8">
        Our platform puts <strong style="color:var(--text)">you</strong> in control. Configure your own profit targets, drawdown limits,
        leverage, payout splits, and trading rules — then prove your edge on your terms. With over
        <strong style="color:var(--green)">700,000+ possible configurations</strong>, no two challenges have to look alike.
    </p>
    <p style="text-align:center;color:var(--text2);font-size:15px;max-width:700px;margin:0 auto;line-height:1.8">
        We are democratizing proprietary trading — making funded accounts accessible, transparent, and fair for traders worldwide.
    </p>

    <div style="height:64px"></div>

    <!-- WHY DOJI FUNDING -->
    <h2 style="text-align:center;margin-bottom:32px">Why <span class="green">Doji Funding</span></h2>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px">

        <div class="rule-card">
            <div class="rule-card-title"><?= icon('sliders') ?> Full Customization</div>
            <p style="color:var(--text2);font-size:14px;line-height:1.7">
                Over 700K+ unique configurations. Set your own profit target, drawdown type, leverage,
                trading days, and payout split. Your challenge, your rules.
            </p>
        </div>

        <div class="rule-card">
            <div class="rule-card-title"><?= icon('chart') ?> 1,000+ Instruments</div>
            <p style="color:var(--text2);font-size:14px;line-height:1.7">
                Trade across 8 asset classes — Forex, Indices, Commodities, Crypto, Stocks, ETFs, Bonds, and Metals
                on MetaTrader 5 and cTrader.
            </p>
        </div>

        <div class="rule-card">
            <div class="rule-card-title"><?= icon('coins') ?> Up to 90% Profit Split</div>
            <p style="color:var(--text2);font-size:14px;line-height:1.7">
                Keep the lion's share of your profits. Choose your split from 60% to 90% and get paid
                within 24 hours of requesting a payout.
            </p>
        </div>

        <div class="rule-card">
            <div class="rule-card-title"><?= icon('zap') ?> 24-Hour Payouts</div>
            <p style="color:var(--text2);font-size:14px;line-height:1.7">
                No waiting weeks for your money. Once you request a payout, we process it within 24 hours
                via bank transfer, crypto, or e-wallet.
            </p>
        </div>

        <div class="rule-card">
            <div class="rule-card-title"><?= icon('diamond') ?> Doji Coins™</div>
            <p style="color:var(--text2);font-size:14px;line-height:1.7">
                Earn Doji Coins™ with every action — purchasing challenges, achieving milestones, and staying active.
                Redeem them for discounts, upgrades, and exclusive perks.
            </p>
        </div>

        <div class="rule-card">
            <div class="rule-card-title"><?= icon('trophy') ?> Monthly Competitions</div>
            <p style="color:var(--text2);font-size:14px;line-height:1.7">
                Compete against other traders for cash prizes and recognition. Monthly leaderboard competitions
                keep the community sharp and motivated.
            </p>
        </div>

    </div>

    <div style="height:64px"></div>

    <!-- OUR VALUES -->
    <h2 style="text-align:center;margin-bottom:32px">Our <span class="green">Values</span></h2>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px">

        <div class="rule-card">
            <div class="rule-card-title"><?= icon('eye') ?> Transparency</div>
            <div class="rule-list">
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> All rules published upfront — no hidden conditions</div>
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Real-time dashboard with full trade analytics</div>
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Clear pricing with no surprise fees</div>
            </div>
        </div>

        <div class="rule-card">
            <div class="rule-card-title"><?= icon('trending') ?> Innovation</div>
            <div class="rule-list">
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> First-of-its-kind challenge configurator</div>
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Proprietary risk scoring engine</div>
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Continuous platform improvements</div>
            </div>
        </div>

        <div class="rule-card">
            <div class="rule-card-title"><?= icon('shield') ?> Fairness</div>
            <div class="rule-list">
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> No traps designed to make traders fail</div>
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Balanced rules that protect both sides</div>
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Equal opportunity regardless of account size</div>
            </div>
        </div>

        <div class="rule-card">
            <div class="rule-card-title"><?= icon('message') ?> Community</div>
            <div class="rule-list">
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Active Discord with thousands of traders</div>
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> 24/7 multilingual customer support</div>
                <div class="rule-list-item allowed"><?= icon('check-circle', 14) ?> Trader feedback shapes our roadmap</div>
            </div>
        </div>

    </div>

    <div style="height:64px"></div>

    <!-- COMPANY INFORMATION -->
    <h2 style="text-align:center;margin-bottom:12px">The <span class="green">Company</span></h2>
    <p style="text-align:center;color:var(--text2);font-size:15px;max-width:700px;margin:0 auto 32px;line-height:1.8">
        Doji Funding® is a registered trademark operated by <strong style="color:var(--text)">Volatys Dynamics LTD</strong>,
        a company incorporated and registered in Gibraltar.
    </p>

    <div class="rule-card">
        <div class="rule-card-title"><?= icon('info') ?> Corporate Details</div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:24px;margin-top:8px">
            <div>
                <div style="font-size:12px;color:var(--text3);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px">Legal Entity</div>
                <div style="font-size:14px;color:var(--text)">Volatys Dynamics LTD</div>
            </div>
            <div>
                <div style="font-size:12px;color:var(--text3);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px">Registered Address</div>
                <div style="font-size:14px;color:var(--text)">Suite 4.3.02, Block 4, Eurotowers<br>Gibraltar GX11 1AA</div>
            </div>
            <div>
                <div style="font-size:12px;color:var(--text3);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px">Incorporation No.</div>
                <div style="font-size:14px;color:var(--text)">125095</div>
            </div>
            <div>
                <div style="font-size:12px;color:var(--text3);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px">REID Number</div>
                <div style="font-size:14px;color:var(--text)">GICO.125095-94</div>
            </div>
        </div>
    </div>

    <div style="height:64px"></div>

    <!-- STATS BAR -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;text-align:center;padding:40px 24px;background:var(--bg3);border:1px solid var(--border);border-radius:16px">
        <div>
            <div style="font-size:32px;font-weight:800;color:var(--green)">700K+</div>
            <div style="font-size:13px;color:var(--text3);margin-top:4px">Possible Configurations</div>
        </div>
        <div>
            <div style="font-size:32px;font-weight:800;color:var(--green)">1,000+</div>
            <div style="font-size:13px;color:var(--text3);margin-top:4px">Trading Instruments</div>
        </div>
        <div>
            <div style="font-size:32px;font-weight:800;color:var(--green)">24h</div>
            <div style="font-size:13px;color:var(--text3);margin-top:4px">Payout Processing</div>
        </div>
        <div>
            <div style="font-size:32px;font-weight:800;color:var(--green)">90%</div>
            <div style="font-size:13px;color:var(--text3);margin-top:4px">Max Profit Split</div>
        </div>
    </div>

    <div style="height:64px"></div>

    <!-- CTA -->
    <div style="text-align:center;padding:48px 24px;background:var(--bg3);border:1px solid var(--border);border-radius:16px">
        <h2 style="font-size:24px;margin-bottom:12px">Ready to Trade <span class="green">Your Way</span>?</h2>
        <p style="color:var(--text3);font-size:15px;max-width:480px;margin:0 auto 24px;line-height:1.7">
            Configure your challenge in minutes. Choose your parameters, prove your skills, and get funded — all on your terms.
        </p>
        <a href="challenges.php" style="text-decoration:none" class="btn-primary-lg">Start Your Challenge</a>
    </div>

    <div style="height:32px"></div>
    <p style="text-align:center;color:var(--text3);font-size:12px">
        Doji Funding® is a registered trademark of Volatys Dynamics LTD. All rights reserved.<br>
        Doji Coins™ is a trademark of Volatys Dynamics LTD.
    </p>

</div>
</section>

<?php include 'includes/community.php'; ?>
