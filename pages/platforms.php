<?php
/**
 * Doji Funding — Platforms Page
 * 
 * Trading platforms overview: MetaTrader 5 & cTrader.
 */
?>

<!-- BREADCRUMB -->
<section class="section" style="padding-top:48px">
    <div class="section-inner">
        <h1 class="page-title">
            Trading <span class="green">Platforms</span>
        </h1>
        <p class="page-subtitle">
            Trade on industry-leading platforms with advanced tools, fast execution, and full flexibility.
        </p>

        <div style="height:48px"></div>

        <!-- MT5 -->
        <div class="platform-card">
            <div class="platform-header">
                <div class="platform-icon">
                    <svg viewBox="0 0 40 40" fill="none" width="40" height="40">
                        <rect width="40" height="40" rx="10" fill="rgba(16,185,129,0.08)" stroke="rgba(16,185,129,0.2)" stroke-width="1"/>
                        <text x="20" y="25" font-size="12" fill="#10B981" font-family="Inter, sans-serif" font-weight="700" text-anchor="middle">MT5</text>
                    </svg>
                </div>
                <div>
                    <h2 style="margin:0;font-size:22px">MetaTrader 5</h2>
                    <p class="platform-sub">The world's most popular trading platform</p>
                </div>
            </div>
            <div class="platform-features">
                <div class="platform-feature">
                    <?= icon("check", 14) ?> Advanced charting with 80+ technical indicators
                </div>
                <div class="platform-feature">
                    <?= icon("check", 14) ?> Full Expert Advisor (EA) support
                </div>
                <div class="platform-feature">
                    <?= icon("check", 14) ?> One-click trading & depth of market
                </div>
                <div class="platform-feature">
                    <?= icon("check", 14) ?> Available on Desktop, Web & Mobile (iOS/Android)
                </div>
                <div class="platform-feature">
                    <?= icon("check", 14) ?> Multi-asset: Forex, Indices, Commodities, Crypto
                </div>
                <div class="platform-feature">
                    <?= icon("check", 14) ?> Built-in economic calendar & market news
                </div>
            </div>
            <div class="platform-actions">
                <a style="text-decoration:none" class="btn-primary-lg" href="challenges.php">Start Trading on MT5</a>
            </div>
        </div>

        <div style="height:24px"></div>

        <!-- cTrader -->
        <div class="platform-card">
            <div class="platform-header">
                <div class="platform-icon">
                    <svg viewBox="0 0 40 40" fill="none" width="40" height="40">
                        <rect width="40" height="40" rx="10" fill="rgba(74,158,255,0.08)" stroke="rgba(74,158,255,0.2)" stroke-width="1"/>
                        <text x="20" y="25" font-size="10" fill="#4a9eff" font-family="Inter, sans-serif" font-weight="700" text-anchor="middle">cT</text>
                    </svg>
                </div>
                <div>
                    <h2 style="margin:0;font-size:22px">cTrader</h2>
                    <p class="platform-sub">Modern, fast & intuitive trading</p>
                </div>
            </div>
            <div class="platform-features">
                <div class="platform-feature">
                    <?= icon("check", 14) ?> Level II pricing with full depth of market
                </div>
                <div class="platform-feature">
                    <?= icon("check", 14) ?> cTrader Automate (cBots) for algo trading
                </div>
                <div class="platform-feature">
                    <?= icon("check", 14) ?> Detachable charts & advanced order types
                </div>
                <div class="platform-feature">
                    <?= icon("check", 14) ?> Available on Desktop, Web & Mobile (iOS/Android)
                </div>
                <div class="platform-feature">
                    <?= icon("check", 14) ?> Copy Trading built-in
                </div>
                <div class="platform-feature">
                    <?= icon("check", 14) ?> Clean, modern UI with fast execution
                </div>
            </div>
            <div class="platform-actions">
                <a style="text-decoration:none" class="btn-primary-lg" href="challenges.php">Start Trading on cTrader</a>
            </div>
        </div>

        <div style="height:48px"></div>

        <!-- Comparison -->
        <h2 style="text-align:center;margin-bottom:24px">Platform <span class="green">Comparison</span></h2>
        <div class="platform-compare">
            <table class="compare-table">
                <thead>
                    <tr>
                        <th>Feature</th>
                        <th>MetaTrader 5</th>
                        <th>cTrader</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>Expert Advisors / Bots</td><td class="green"><?= icon("check", 14) ?> EA</td><td class="green"><?= icon("check", 14) ?> cBots</td></tr>
                    <tr><td>Copy Trading</td><td>Via third-party</td><td class="green"><?= icon("check", 14) ?> Built-in</td></tr>
                    <tr><td>Depth of Market</td><td class="green"><?= icon("check", 14) ?></td><td class="green"><?= icon("check", 14) ?> Level II</td></tr>
                    <tr><td>Detachable Charts</td><td>—</td><td class="green"><?= icon("check", 14) ?></td></tr>
                    <tr><td>Mobile App</td><td class="green"><?= icon("check", 14) ?> iOS & Android</td><td class="green"><?= icon("check", 14) ?> iOS & Android</td></tr>
                    <tr><td>Web Platform</td><td class="green"><?= icon("check", 14) ?></td><td class="green"><?= icon("check", 14) ?></td></tr>
                    <tr><td>Technical Indicators</td><td>80+</td><td>70+</td></tr>
                    <tr><td>Community & Marketplace</td><td class="green"><?= icon("check", 14) ?> MQL5</td><td class="green"><?= icon("check", 14) ?> cTrader Open</td></tr>
                </tbody>
            </table>
        </div>

    </div>
</section>

<?php include 'includes/community.php'; ?>
