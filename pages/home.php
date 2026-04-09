<?php
/**
 * Doji Funding — Homepage
 * 
 * Hero, trust bar, how it works, challenge cards, stats, CTA.
 */
?>

<!-- HERO (Interactive Globe) -->
<section class="hero">
    <!-- Particle canvas: dot-grid + rotating sphere (mouse-driven) -->
    <canvas class="hero-shape-canvas" data-shape="sphere" data-cx="0.64" data-scale="1.42" aria-hidden="true"></canvas>
    <!-- Content -->
    <div class="hero-split">
        <div class="hero-left">
            <div class="badge">The World's Most Advanced Prop Firm</div>
            <h1
                data-weight-wave
                data-ww-base-weight="300"
                data-ww-hover-weight="850"
                data-ww-radius="5"
            >
                Trade Your <span class="green text-loop-word" id="heroLoopWord" data-no-ww>Way</span><br><span class="green">Get Funded.</span>
                <span class="seo-tag">H1</span>
            </h1>
            <p class="subtitle">
                Set your profit target, drawdown limits, payout split and more — built entirely around your standards.
                Accounts from $5K to $100K on MetaTrader 5 &amp; cTrader. Up to $500K total capital. Starting at just $39.
                <span class="seo-tag">Above fold keywords</span>
            </p>
            <div class="hero-stats">
                <div class="hero-stat">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    <div><strong data-count="1000" data-suffix="+">0</strong><span>Trading Instruments</span></div>
                </div>
                <div class="hero-stat">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                    <div><strong>Up to 90%</strong><span>Profit Split</span></div>
                </div>
                <div class="hero-stat">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <div><strong>24h Payouts</strong><span>Guaranteed Processing</span></div>
                </div>
                <div class="hero-stat">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <div><strong>No Time Limit</strong><span>Challenge Phase</span></div>
                </div>
            </div>
            <div class="cta-row">
                <a style="text-decoration:none" class="btn-primary-lg" href="challenges.php">Start Your Challenge</a>
                <a style="text-decoration:none;padding:16px 20px" class="btn-outline" href="competitions.php">Join Competitions</a>
                <a style="text-decoration:none;padding:14px 14px;display:inline-flex;align-items:center;justify-content:center" class="btn-outline" href="https://discord.gg/kNUqAqCppU" target="_blank" rel="noopener noreferrer" aria-label="Join our Discord">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M20.317 4.3698a19.7913 19.7913 0 00-4.8851-1.5152.0741.0741 0 00-.0785.0371c-.211.3753-.4447.8648-.6083 1.2495-1.8447-.2762-3.68-.2762-5.4868 0-.1636-.3933-.4058-.8742-.6177-1.2495a.077.077 0 00-.0785-.037 19.7363 19.7363 0 00-4.8852 1.515.0699.0699 0 00-.0321.0277C.5334 9.0458-.319 13.5799.0992 18.0578a.0824.0824 0 00.0312.0561c2.0528 1.5076 4.0413 2.4228 5.9929 3.0294a.0777.0777 0 00.0842-.0276c.4616-.6304.8731-1.2952 1.226-1.9942a.076.076 0 00-.0416-.1057c-.6528-.2476-1.2743-.5495-1.8722-.8923a.077.077 0 01-.0076-.1277c.1258-.0943.2517-.1923.3718-.2914a.0743.0743 0 01.0776-.0105c3.9278 1.7933 8.18 1.7933 12.0614 0a.0739.0739 0 01.0785.0095c.1202.099.246.1981.3728.2924a.077.077 0 01-.0066.1276 12.2986 12.2986 0 01-1.873.8914.0766.0766 0 00-.0407.1067c.3604.698.7719 1.3628 1.225 1.9932a.076.076 0 00.0842.0286c1.961-.6067 3.9495-1.5219 6.0023-3.0294a.077.077 0 00.0313-.0552c.5004-5.177-.8382-9.6739-3.5485-13.6604a.061.061 0 00-.0312-.0286zM8.02 15.3312c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9555-2.4189 2.157-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.9555 2.4189-2.1569 2.4189zm7.9748 0c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9554-2.4189 2.1569-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.946 2.4189-2.1568 2.4189z"/></svg>
                </a>
            </div>
        </div>
        <div class="hero-right"></div>
    </div>
</section>

<div class="section-divider"></div>

<!-- TRUST BAR — Infinite Marquee -->
<section style="background:var(--bg2);border-bottom:1px solid rgba(16,185,129,0.08)">
    <div class="trust-bar-wrap" style="max-width:1200px;margin:0 auto">
        <div class="trust-bar">
            <?php for ($i = 0; $i < 2; $i++): ?>
            <div class="trust-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span><strong>Regulated</strong> Gibraltar Entity</span>
            </div>
            <div class="trust-sep"></div>
            <div class="trust-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span><strong>4,200+</strong> Funded Traders</span>
            </div>
            <div class="trust-sep"></div>
            <div class="trust-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                <span><strong>$12M+</strong> Paid Out</span>
            </div>
            <div class="trust-sep"></div>
            <div class="trust-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span><strong>24h</strong> Guaranteed Payouts</span>
            </div>
            <div class="trust-sep"></div>
            <div class="trust-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <span><strong>24/7</strong> Support</span>
            </div>
            <div class="trust-sep"></div>
            <div class="trust-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                <span><strong>Up to 90%</strong> Profit Split</span>
            </div>
            <div class="trust-sep"></div>
            <div class="trust-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                <span><strong>MT5 & cTrader</strong> Platforms</span>
            </div>
            <div class="trust-sep"></div>
            <div class="trust-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                <span><strong>150+</strong> Countries</span>
            </div>
            <div class="trust-sep"></div>
            <?php endfor; ?>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- KEY HIGHLIGHTS — reference-style layout -->
<section class="feat-section">
    <div class="feat-wrap">

        <!-- Header Row -->
        <div class="feat-header">
            <div class="feat-header-left">
                <div class="feat-label">
                    <span class="feat-num">01</span>
                    <span class="feat-pipe"></span>
                    <span class="feat-cat">Trading Infrastructure</span>
                </div>
                <h2 class="feat-h2">Power up your <span class="green">trading success.</span></h2>
                <div class="kh-more-list" style="margin-top:32px">
                    <div class="kh-more-item"><?= icon('check', 14) ?> Raw spreads from 0.0 pips</div>
                    <div class="kh-more-item"><?= icon('check', 14) ?> 8 asset classes — 1,000+ instruments</div>
                    <div class="kh-more-item"><?= icon('check', 14) ?> 20 account sizes for every budget</div>
                    <div class="kh-more-item"><?= icon('check', 14) ?> Starting from $39 only</div>
                </div>
            </div>
            <div class="feat-header-right">
                <p class="feat-desc">Doji Funding® is built on institutional infrastructure — designed around your standards, your comfort, and your trajectory. The most advanced prop firm ever built, with over 700,000 possible configurations and payouts that arrive in 24 hours. Set your own dimensions.</p>
                <div class="feat-cta-row">
                    <a href="/challenges.php" class="feat-cta-primary">Start Your Challenge →</a>
                </div>
            </div>
        </div>

        <!-- Cards Row -->
        <div class="feat-cards">

            <!-- Card 1: Configurator -->
            <div class="feat-card">
                <h3>700K+ Configurations</h3>
                <p>The most advanced configurator in prop trading. Customize every parameter — from profit target to drawdown type. Built for beginners and pros alike.</p>
                <div class="feat-visual">
                    <div class="fv-slider-row"><span>Profit Target</span><div class="fv-bar"><div class="fv-fill" style="width:62%"></div></div><span class="fv-val">8%</span></div>
                    <div class="fv-slider-row"><span>Max Drawdown</span><div class="fv-bar"><div class="fv-fill" style="width:40%"></div></div><span class="fv-val">10%</span></div>
                    <div class="fv-slider-row"><span>Profit Split</span><div class="fv-bar"><div class="fv-fill" style="width:90%"></div></div><span class="fv-val">90%</span></div>
                    <div class="fv-price-row"><span>Total Price</span><span class="fv-price">$249</span></div>
                </div>
            </div>

            <!-- Card 2: Payouts -->
            <div class="feat-card">
                <h3>24h Guaranteed Payouts</h3>
                <p>Request your payout and receive it within 24 hours — guaranteed. Weekly payout cycles so you never wait long for your rewards.</p>
                <div class="feat-visual">
                    <div class="fv-pay-row">
                        <span class="fv-pay-dot"></span>
                        <div><div class="fv-pay-label">Payout Requested</div><div class="fv-pay-time">Today, 09:14 UTC</div></div>
                        <span class="fv-pay-amt">$1,850</span>
                    </div>
                    <div class="fv-pay-line"></div>
                    <div class="fv-pay-row">
                        <span class="fv-pay-dot"></span>
                        <div><div class="fv-pay-label">Processing</div><div class="fv-pay-time">Today, 09:16 UTC</div></div>
                        <span class="fv-pay-check">✓</span>
                    </div>
                    <div class="fv-pay-line"></div>
                    <div class="fv-pay-row">
                        <span class="fv-pay-dot fv-pay-dot-active"></span>
                        <div><div class="fv-pay-label">Funds Sent</div><div class="fv-pay-time">Today, 14:22 UTC</div></div>
                        <span class="fv-pay-badge">24h ✓</span>
                    </div>
                </div>
            </div>

            <!-- Card 3: News Trading -->
            <div class="feat-card">
                <h3>News Trading Allowed</h3>
                <p>Trade high-impact events freely — NFP, FOMC, CPI. If there's opportunity in the markets, you should be able to take it. No restrictions. Ever.</p>
                <div class="feat-visual">
                    <div class="fv-news-row">
                        <span class="fv-news-impact fv-news-high">HIGH</span>
                        <span class="fv-news-name">Non-Farm Payroll</span>
                        <span class="fv-news-ok">✓ Allowed</span>
                    </div>
                    <div class="fv-news-row">
                        <span class="fv-news-impact fv-news-high">HIGH</span>
                        <span class="fv-news-name">FOMC Rate Decision</span>
                        <span class="fv-news-ok">✓ Allowed</span>
                    </div>
                    <div class="fv-news-row">
                        <span class="fv-news-impact fv-news-med">MED</span>
                        <span class="fv-news-name">CPI Data Release</span>
                        <span class="fv-news-ok">✓ Allowed</span>
                    </div>
                    <div class="fv-news-tag">No restrictions. No exceptions.</div>
                </div>
            </div>

        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- HOW IT WORKS — reference-style layout -->
<section class="s-dot" style="background:var(--bg);padding:0">
    <div class="feat-wrap">

        <!-- Header Row -->
        <div class="feat-header">
            <div class="feat-header-left">
                <div class="feat-label">
                    <span class="feat-num">02</span>
                    <span class="feat-pipe"></span>
                    <span class="feat-cat">Process</span>
                </div>
                <h2 class="feat-h2">Your path to <span class="green">funded trading.</span></h2>
            </div>
            <div class="feat-header-right">
                <p class="feat-desc">Four simple steps separate you from trading live institutional capital. No hidden rules, no unnecessary complexity — just a clear path designed around your success.</p>
                <div class="feat-cta-row">
                    <a href="/challenges.php" class="feat-cta-primary">Configure your Challenge →</a>
                </div>
            </div>
        </div>

        <!-- Steps Grid -->
        <div class="hiw-grid hiw-grid-padded">
            <div class="hiw-card hiw-card-centered" style="--step-opacity:0.3; --border-top:#022c22; --border-bottom:#064e3b">
                <div class="hiw-num">01</div>
                <h3>Choose Plan</h3>
                <p>Select an account size that matches your risk profile and trading style.</p>
                <a href="/challenges.php" class="hiw-cta">READY TO START →</a>
            </div>
            <div class="hiw-card hiw-card-centered" style="--step-opacity:0.5; --border-top:#064e3b; --border-bottom:#047857">
                <div class="hiw-num">02</div>
                <h3>Pass Challenge</h3>
                <p>Demonstrate your edge by hitting the profit target while managing risk.</p>
            </div>
            <div class="hiw-card hiw-card-centered" style="--step-opacity:0.75; --border-top:#047857; --border-bottom:#10B981">
                <div class="hiw-num">03</div>
                <h3>Verify Identity</h3>
                <p>Fast-track KYC process to ensure institutional compliance and security.</p>
            </div>
            <div class="hiw-card hiw-card-centered" style="--step-opacity:1; --border-top:#10B981; --border-bottom:#34d399">
                <div class="hiw-num">04</div>
                <h3>Get Funded</h3>
                <p>Trade live capital and keep up to 90% of your generated profits.</p>
            </div>
        </div>

    </div>
</section>

<div class="section-divider"></div>

<!-- CONFIGURATOR (Home) -->
<section class="section" style="background:var(--bg2);padding-top:72px;padding-bottom:80px">
    <div class="section-inner">
        <div class="feat-label" style="margin-bottom:32px;padding-left:24px">
            <span class="feat-num">03</span>
            <span class="feat-pipe"></span>
            <span class="feat-cat">Pricing &amp; Plans</span>
        </div>
        <h2 style="text-align:center;margin-bottom:10px">Configure Your <span class="green">Challenge</span></h2>
        <p class="section-sub" style="text-align:center;color:var(--text2);font-size:16px;margin-bottom:44px">Select your evaluation type, customize every parameter and configure your account in the dimensions of your choice.</p>

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

        <!-- ═══ MODE PICKER (pre-configurator) ═══ -->
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

                <button class="mode-card mode-affiliate" data-mode="affiliate" id="modeAffiliate" onclick="Configurator.applyMode('affiliate')">
                    <span class="mode-tag">AFFILIATE</span>
                    <span class="mode-name">Affiliate</span>
                    <span class="mode-desc mode-desc-locked">Unlocks with sales</span>
                    <span class="mode-lock">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
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

            <!-- LEFT: Parameters -->
            <div class="cfg-panel">
                <div class="cfg-header">
                    <h2 class="cfg-title">Challenge Configurator</h2>
                    <span class="cfg-badge" id="cfgBadge">FAST TRACK</span>
                </div>
                <div id="slidersContainer"></div>
                <button class="reset-btn" onclick="Configurator.reset()">↺ Reset to Defaults</button>
            </div>

            <!-- RIGHT: Summary + Price -->
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
                    <input class="promo-input" id="promoInput" placeholder="Enter promo code" 
                           onkeydown="if(event.key==='Enter')Configurator.applyPromo()">
                    <button class="promo-btn" id="promoBtn" onclick="Configurator.applyPromo()">Apply</button>
                </div>
                <div id="promoMsg"></div>

                <button class="share-btn" onclick="Configurator.share()">🔗 Share Configuration</button>
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
    </div>
</section>

<div class="section-divider"></div>

<!-- ASSET CLASSES BANNER -->
<section class="section s-dot" style="background:var(--bg);padding-top:72px;padding-bottom:100px">
    <div class="section-inner" style="max-width:1200px;margin:0 auto">
        <div class="feat-label" style="margin-bottom:32px;padding-left:24px">
            <span class="feat-num">04</span>
            <span class="feat-pipe"></span>
            <span class="feat-cat">Markets</span>
        </div>
        <h2 style="text-align:center;margin-bottom:8px">Trade 1,000+ Instruments Across <span class="green">8 Asset Classes</span></h2>
        <p class="section-sub" style="text-align:center;color:var(--text2);font-size:15px;margin-bottom:48px">From forex to crypto, indices to futures — everything you need under one roof with institutional-grade conditions.</p>

        <!-- Top 2 large cards -->
        <div class="ac-top">
            <div class="ac-card-lg">
                <div class="ac-card-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="1.8"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div>
                <h3>Indices & Shares</h3>
                <p>DAX, Dow Jones, NASDAQ, S&P 500, Apple, Microsoft, Adidas, SAP and 800+ more. Trade CFDs on stocks and global indices under top conditions with raw spreads.</p>
                <div class="ac-tags"><span>DE40</span><span>USTEC</span><span>US500</span><span>DJ30</span><span>AAPL</span><span>MSFT</span><span>SAPG</span><span>800+</span></div>
            </div>
            <div class="ac-card-lg">
                <div class="ac-card-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></div>
                <h3>Forex</h3>
                <p>Access 60+ currency pairs including majors, minors and exotics. Trade EUR/USD, GBP/JPY, USD/TRY and more with spreads starting from 0.0 pips.</p>
                <div class="ac-tags"><span>EURUSD</span><span>GBPUSD</span><span>USDJPY</span><span>AUDUSD</span><span>USDCHF</span><span>60+</span></div>
            </div>
        </div>

        <!-- Bottom 4 cards -->
        <div class="ac-bottom">
            <div class="ac-card-sm">
                <div class="ac-card-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="1.8"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5" opacity="0.5"/><path d="M2 12l10 5 10-5" opacity="0.7"/></svg></div>
                <h3>Metals</h3>
                <p>Diversify with precious metals CFDs — Gold, Silver, Platinum and Palladium at competitive spreads.</p>
                <div class="ac-tags"><span>XAUUSD</span><span>XAGUSD</span><span>XPTUSD</span></div>
            </div>
            <div class="ac-card-sm">
                <div class="ac-card-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                <h3>Commodities & Energies</h3>
                <p>Trade CFDs on Crude Oil (WTI & Brent) and other essential energy markets.</p>
                <div class="ac-tags"><span>USOIL</span><span>UKOIL</span></div>
            </div>
            <div class="ac-card-sm">
                <div class="ac-card-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                <h3>Futures</h3>
                <p>CFDs on futures — Germany 40, Crude Oil, Brent, US 10-Year Notes, Gold Futures and more.</p>
                <div class="ac-tags"><span>DE40.Exp</span><span>USOIL.Exp</span><span>XAUUSD.Exp</span></div>
            </div>
            <div class="ac-card-sm">
                <div class="ac-card-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="1.8"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
                <h3>Crypto</h3>
                <p>Trade Bitcoin, Ethereum, Solana, Cardano, Dogecoin, Chainlink, BNB and more on CFDs.</p>
                <div class="ac-tags"><span>BTCUSD</span><span>ETHUSD</span><span>SOLUSD</span><span>15+</span></div>
            </div>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- DOJI COINS LOYALTY PROGRAM -->
<section class="dc2-section" style="background:var(--bg2);overflow:hidden">
    <div class="dc2-layout">

        <!-- LEFT: label + headline + terminal visual -->
        <div class="dc2-left">
            <div class="feat-label" style="margin-bottom:32px">
                <span class="feat-num">05</span>
                <span class="feat-pipe"></span>
                <span class="feat-cat">Rewards</span>
            </div>
            <h2 class="feat-h2">Trade more. <span class="green">Earn more.</span></h2>

            <!-- Terminal visual -->
            <div class="dc2-terminal">
                <div class="dc2-terminal-bar">
                    <span class="dc2-terminal-dot dc2-dot-green"></span>
                    <span class="dc2-terminal-title">DOJI COINS™</span>
                    <span class="dc2-terminal-status">ACTIVE</span>
                </div>
                <div class="dc2-terminal-balance">
                    <img src="<?= DOJI_COIN_FILE ?>" loading="lazy" alt="Doji Coin" class="dc2-coin-icon">
                    <span class="dc2-balance-num">5,300</span>
                    <span class="dc2-balance-unit">DC</span>
                </div>
                <div class="dc2-bar-wrap">
                    <div class="dc2-bar-labels">
                        <span>Next: Free Challenge</span>
                        <span style="color:#10B981">3,200 / 5,000 DC</span>
                    </div>
                    <div class="dc2-bar"><div class="dc2-bar-fill" style="width:64%"></div></div>
                </div>
                <div class="dc2-rate-rows">
                    <div class="dc2-rate-row"><span>1 lot traded</span><span>+10 DC</span></div>
                    <div class="dc2-rate-row"><span>Challenge eval</span><span>Earns coins</span></div>
                    <div class="dc2-rate-row"><span>Funded account</span><span>Earns coins</span></div>
                    <div class="dc2-rate-row"><span>Expiry</span><span style="color:#10B981">Never</span></div>
                </div>
                <a href="challenges.php" style="text-decoration:none" class="btn-primary-lg dc2-cta">Start Earning <span style="font-family:'Doto',monospace;font-weight:400;letter-spacing:0">→</span></a>
            </div>
        </div>

        <!-- RIGHT: monospace desc + 2x2 reward cards -->
        <div class="dc2-right">
            <p class="dc2-desc">A LOYALTY PROGRAM BUILT FOR TRADERS — EVERY LOT YOU TRADE BUILDS YOUR DOJI COINS™ BALANCE. NO EXPIRY. NO CONDITIONS. JUST REWARDS THAT REFLECT HOW HARD YOU WORK.</p>

            <div class="dc2-cards">
                <div class="dc2-card">
                    <div class="dc2-card-top">
                        <span class="dc2-card-num">01/</span>
                        <span class="dc2-card-dots">• • •</span>
                    </div>
                    <h3 class="dc2-card-title">Free Challenge</h3>
                    <p class="dc2-card-desc">Skip the entry fee entirely. Redeem coins for a full evaluation — same rules, zero cost.</p>
                    <span class="dc2-card-cost"><img src="<?= DOJI_COIN_FILE ?>" loading="lazy" alt="" class="dc-reward-icon"> 5,000 DC</span>
                </div>
                <div class="dc2-card">
                    <div class="dc2-card-top">
                        <span class="dc2-card-num">02/</span>
                        <span class="dc2-card-dots">• • •</span>
                    </div>
                    <h3 class="dc2-card-title">+5% Split Boost</h3>
                    <p class="dc2-card-desc">Raise your profit share on the next funded cycle. Stack multiple boosts for compounding gains.</p>
                    <span class="dc2-card-cost"><img src="<?= DOJI_COIN_FILE ?>" loading="lazy" alt="" class="dc-reward-icon"> 2,500 DC</span>
                </div>
                <div class="dc2-card">
                    <div class="dc2-card-top">
                        <span class="dc2-card-num">03/</span>
                        <span class="dc2-card-dots">• • •</span>
                    </div>
                    <h3 class="dc2-card-title">Fee Reduction</h3>
                    <p class="dc2-card-desc">Trim your challenge fee by up to 30%. The more volume you trade, the cheaper your next challenge.</p>
                    <span class="dc2-card-cost"><img src="<?= DOJI_COIN_FILE ?>" loading="lazy" alt="" class="dc-reward-icon"> 1,500 DC</span>
                </div>
                <div class="dc2-card">
                    <div class="dc2-card-top">
                        <span class="dc2-card-num">04/</span>
                        <span class="dc2-card-dots">• • •</span>
                    </div>
                    <h3 class="dc2-card-title">Exclusive Merch</h3>
                    <p class="dc2-card-desc">Limited Doji Funding drops — gear made for traders, refreshed every quarter. Coins only.</p>
                    <span class="dc2-card-cost"><img src="<?= DOJI_COIN_FILE ?>" loading="lazy" alt="" class="dc-reward-icon"> 3,000 DC</span>
                </div>
            </div>
        </div>

    </div>
</section>

<div class="section-divider"></div>

<!-- BUILT FOR TRADERS — Pill Bar -->
<section class="section s-dot" style="background:var(--bg)">
    <div style="max-width:1200px;margin:0 auto">
        <div class="pill-bar">
            <div class="pill-bar-title">Built for traders,<br>down to the details</div>
            <div class="pill-bar-items">
                <div class="pill-bar-item"><?= icon('check-circle', 16) ?> No Time Limits</div>
                <div class="pill-bar-item"><?= icon('check-circle', 16) ?> 24/7 Support</div>
                <div class="pill-bar-item"><?= icon('check-circle', 16) ?> Up to 90% Profit Split</div>
                <div class="pill-bar-item"><?= icon('check-circle', 16) ?> Max Loss Up to 12%</div>
                <div class="pill-bar-item"><?= icon('check-circle', 16) ?> Profit Target from 5%</div>
                <div class="pill-bar-item"><?= icon('check-circle', 16) ?> 10 Accounts Max</div>
                <div class="pill-bar-item"><?= icon('check-circle', 16) ?> Doji Coins™ Rewards</div>
                <div class="pill-bar-item"><?= icon('check-circle', 16) ?> $500K Max Capital</div>
            </div>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- TESTIMONIALS -->
<section class="section" style="background:var(--bg2);padding-top:0;padding-bottom:72px">
    <div class="section-inner">
        <!-- Header -->
        <div class="feat-header" style="border-bottom:1px solid rgba(255,255,255,0.05)">
            <div class="feat-header-left">
                <div class="feat-label" style="margin-bottom:32px;padding-left:0">
                    <span class="feat-num">06</span>
                    <span class="feat-pipe"></span>
                    <span class="feat-cat">User Feedback</span>
                </div>
                <h2 class="feat-h2" data-stacking-words>Voices of <span class="green">Doji traders.</span></h2>
            </div>
            <div class="feat-header-right">
                <p class="feat-desc">Hear from the traders who have built their career with Doji Funding — real accounts, real payouts, real results from funded traders around the world.</p>
            </div>
        </div>

        <!-- Featured quote — cornered frame -->
        <div class="testi-frame" id="testiStage">
            <div class="testi-stars" id="testiStars">★★★★★</div>
            <div class="testi-quote" id="testiQuote"></div>
            <div class="testi-author" id="testiAuthor">
                <div class="testi-avatar" id="testiAvatar">M</div>
                <div>
                    <div class="testi-name" id="testiName">Marcus T.</div>
                    <div class="testi-role" id="testiRole">Forex Trader · $50K Funded</div>
                </div>
            </div>
            <div class="testi-dots">
                <button class="testi-dot testi-dot-active" onclick="testiGo(0)" aria-label="Testimonial 1"></button>
                <button class="testi-dot" onclick="testiGo(1)" aria-label="Testimonial 2"></button>
                <button class="testi-dot" onclick="testiGo(2)" aria-label="Testimonial 3"></button>
            </div>
        </div>

        <script>
        var _testiData = [
            { q: '"The configurator is a game-changer. I set my own drawdown limits and profit target — no other prop firm gives you this level of control. Got funded in 12 days."', av: 'M', name: 'Marcus T.', role: 'Forex Trader · $50K Funded' },
            { q: '"Payout came in under 18 hours. I\'ve been with 3 other firms and none of them process this fast. The 90% split on a $100K account is unbeatable."', av: 'S', name: 'Sarah K.', role: 'Indices Trader · $100K Funded' },
            { q: '"Started with a $5K challenge for $39. The Doji Coins reward system keeps me motivated — I\'ve already redeemed a free challenge. Support team is responsive 24/7."', av: 'A', name: 'Ahmed R.', role: 'Crypto Trader · $25K Funded' }
        ];
        var _testiCurrent = 0;
        var _testiTimer;

        /* ── Split reveal lines ─────────────────────────────────────
           1. Set plain text in the quote element
           2. Wrap each word in a span to detect Y positions
           3. Group words by line (same offsetTop)
           4. Wrap each line in overflow:hidden mask + inner span
           5. Animate lines in from translateY(100%) with stagger
        ──────────────────────────────────────────────────────────── */
        function testiRevealLines(el, text) {
            el.innerHTML = '';

            // Step 1 — word spans
            var words = text.split(/(\s+)/);
            var wordEls = [];
            words.forEach(function(tok) {
                if (/^\s+$/.test(tok)) {
                    el.appendChild(document.createTextNode(tok));
                } else if (tok) {
                    var s = document.createElement('span');
                    s.style.display = 'inline-block';
                    s.textContent = tok;
                    el.appendChild(s);
                    wordEls.push(s);
                }
            });

            // Step 2 — group by Y in rAF (after layout)
            requestAnimationFrame(function() {
                var lines = [];
                var lastY = null;
                wordEls.forEach(function(w) {
                    var y = w.getBoundingClientRect().top;
                    if (lastY === null || Math.abs(y - lastY) > 4) {
                        lines.push([]);
                        lastY = y;
                    }
                    lines[lines.length - 1].push(w);
                });

                // Step 3 — wrap each line in mask + inner
                lines.forEach(function(lineWords, li) {
                    var mask = document.createElement('span');
                    mask.style.cssText = 'display:block;overflow:hidden;';

                    var inner = document.createElement('span');
                    inner.style.cssText = 'display:block;transform:translateY(110%);transition:transform 0.65s cubic-bezier(0.16,1,0.3,1) ' + (li * 0.10) + 's;';

                    // Re-insert words into inner
                    var first = lineWords[0];
                    var parent = first.parentNode;
                    parent.insertBefore(mask, first);
                    mask.appendChild(inner);
                    lineWords.forEach(function(w, wi) {
                        if (wi > 0) inner.appendChild(document.createTextNode(' '));
                        inner.appendChild(w);
                    });
                });

                // Step 4 — trigger animation
                requestAnimationFrame(function() {
                    el.querySelectorAll('span > span').forEach(function(inner) {
                        inner.style.transform = 'translateY(0)';
                    });
                });
            });
        }

        function testiUpdateDots(i) {
            document.querySelectorAll('.testi-dot').forEach(function(dot, idx) {
                dot.classList.toggle('testi-dot-active', idx === i);
            });
        }

        function testiGo(i) {
            _testiCurrent = i;
            var d = _testiData[i];

            // Fade out author + stars
            var author = document.getElementById('testiAuthor');
            var stars  = document.getElementById('testiStars');
            author.style.opacity = '0';
            author.style.transform = 'translateY(6px)';
            stars.style.opacity = '0';

            // Reveal new quote with line-split effect
            testiRevealLines(document.getElementById('testiQuote'), d.q);

            // Fade in author + stars after lines start animating
            setTimeout(function() {
                document.getElementById('testiAvatar').textContent = d.av;
                document.getElementById('testiName').textContent   = d.name;
                document.getElementById('testiRole').textContent   = d.role;
                author.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                author.style.opacity    = '1';
                author.style.transform  = 'translateY(0)';
                stars.style.transition  = 'opacity 0.4s ease';
                stars.style.opacity     = '1';
            }, 200);

            testiUpdateDots(i);
            clearInterval(_testiTimer);
            _testiTimer = setInterval(function() { testiGo((_testiCurrent + 1) % _testiData.length); }, 6000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initial reveal
            testiGo(0);
            _testiTimer = setInterval(function() { testiGo((_testiCurrent + 1) % _testiData.length); }, 6000);
        });
        </script>
    </div>
</section>

<div class="section-divider"></div>

<!-- SOCIAL PROOF METRICS -->
<section class="section s-dot" style="background:var(--bg);border-top:1px solid rgba(16,185,129,0.08);border-bottom:1px solid rgba(16,185,129,0.08)">
    <div class="section-inner">
        <div class="proof-grid">
            <div class="proof-item">
                <div class="proof-val" data-count="4200" data-suffix="+">0</div>
                <div class="proof-label">Funded Traders</div>
            </div>
            <div class="proof-item">
                <div class="proof-val" data-count="12" data-prefix="$" data-suffix="M+">0</div>
                <div class="proof-label">Total Payouts</div>
            </div>
            <div class="proof-item">
                <div class="proof-val" data-count="150" data-suffix="+">0</div>
                <div class="proof-label">Countries Served</div>
            </div>
            <div class="proof-item">
                <div class="proof-val" data-count="24" data-suffix="h">0</div>
                <div class="proof-label">Payout Processing</div>
            </div>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- TRADING PLATFORMS -->
<section class="section" style="background:var(--bg2);padding-top:72px">
    <div class="section-inner">
        <div class="feat-label" style="margin-bottom:32px;padding-left:24px">
            <span class="feat-num">07</span>
            <span class="feat-pipe"></span>
            <span class="feat-cat">Platforms</span>
        </div>
        <h2>Trade on <span class="green">World-Class</span> Platforms</h2>
        <p class="section-sub">Professional-grade execution with the platforms you already know and trust.</p>
        <div class="platforms-row">
            <div class="platform-card">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="3" width="20" height="14" rx="2"/>
                    <line x1="8" y1="21" x2="16" y2="21"/>
                    <line x1="12" y1="17" x2="12" y2="21"/>
                    <polyline points="6 10 10 7 14 12 18 8"/>
                </svg>
                <div class="platform-name">MetaTrader 5</div>
                <div class="platform-desc">The industry standard for forex & CFD trading with advanced charting and EAs</div>
            </div>
            <div class="platform-card">
                <img src="assets/img/ctrader-logo.svg" alt="cTrader" class="platform-logo-img" width="120" height="43" loading="lazy">
                <div class="platform-name">cTrader</div>
                <div class="platform-desc">Modern interface with Level II pricing, advanced cBots and copy trading built-in</div>
            </div>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- DASHBOARD SHOWCASE -->
<section class="section s-dot" style="background:var(--bg);padding-top:72px">
    <div class="section-inner" style="max-width:1200px;margin:0 auto">

        <div class="feat-label" style="margin-bottom:32px;padding-left:24px">
            <span class="feat-num">08</span>
            <span class="feat-pipe"></span>
            <span class="feat-cat">Dashboard</span>
        </div>

        <!-- Header split -->
        <div class="dsh-header">
            <h2 class="feat-h2">The world's most<br><span class="green">advanced dashboard.</span></h2>
            <p class="feat-desc" style="padding-top:8px">Every metric that matters. Every tool you need. Built from the ground up for serious traders — real-time P&L, payout requests, challenge progress and Doji Coins™ all in one place.</p>
        </div>

        <!-- Dashboard mockup -->
        <div class="dsh-mockup">

            <!-- Top bar -->
            <div class="dsh-topbar">
                <div class="dsh-topbar-left">
                    <span class="dsh-logo-dot"></span>
                    <span class="dsh-topbar-label">DOJI FUNDING®</span>
                    <span class="dsh-topbar-sep">|</span>
                    <span class="dsh-topbar-label" style="color:#10B981">DASHBOARD</span>
                </div>
                <div class="dsh-topbar-right">
                    <span class="dsh-badge-live"><span class="dsh-live-dot"></span>LIVE</span>
                    <span class="dsh-topbar-user">trader_alex</span>
                </div>
            </div>

            <!-- Main grid -->
            <div class="dsh-grid">

                <!-- Stat cards row -->
                <div class="dsh-stats-row">
                    <div class="dsh-stat-card">
                        <div class="dsh-stat-label">Account Balance</div>
                        <div class="dsh-stat-val" style="color:#fff">$52,840</div>
                        <div class="dsh-stat-sub" style="color:#10B981">▲ +$1,240 today</div>
                    </div>
                    <div class="dsh-stat-card">
                        <div class="dsh-stat-label">Profit Target</div>
                        <div class="dsh-stat-val" style="color:#10B981">8.4% / 10%</div>
                        <div class="dsh-mini-bar"><div class="dsh-mini-fill" style="width:84%;background:#10B981"></div></div>
                    </div>
                    <div class="dsh-stat-card">
                        <div class="dsh-stat-label">Daily Drawdown</div>
                        <div class="dsh-stat-val" style="color:#f59e0b">1.2% / 5%</div>
                        <div class="dsh-mini-bar"><div class="dsh-mini-fill" style="width:24%;background:#f59e0b"></div></div>
                    </div>
                    <div class="dsh-stat-card">
                        <div class="dsh-stat-label">Doji Coins™</div>
                        <div class="dsh-stat-val" style="color:#10B981;font-family:'Doto',monospace">3,240 DC</div>
                        <div class="dsh-stat-sub">Next reward: 1,760 DC</div>
                    </div>
                </div>

                <!-- Chart + Activity -->
                <div class="dsh-main-row">

                    <!-- Equity chart -->
                    <div class="dsh-chart-panel">
                        <div class="dsh-panel-header">
                            <span class="dsh-panel-title">Equity Curve</span>
                            <span class="dsh-panel-badge">MT5 · $50K</span>
                        </div>
                        <div class="dsh-chart">
                            <svg viewBox="0 0 400 100" preserveAspectRatio="none" style="width:100%;height:100%">
                                <defs>
                                    <linearGradient id="eqGrad" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#10B981" stop-opacity="0.25"/>
                                        <stop offset="100%" stop-color="#10B981" stop-opacity="0"/>
                                    </linearGradient>
                                </defs>
                                <path d="M0,75 L30,70 L60,65 L90,68 L120,58 L150,52 L180,55 L210,45 L240,40 L270,35 L300,30 L330,25 L360,20 L400,15 L400,100 L0,100 Z" fill="url(#eqGrad)"/>
                                <path d="M0,75 L30,70 L60,65 L90,68 L120,58 L150,52 L180,55 L210,45 L240,40 L270,35 L300,30 L330,25 L360,20 L400,15" fill="none" stroke="#10B981" stroke-width="1.5"/>
                                <circle cx="400" cy="15" r="3" fill="#10B981"/>
                            </svg>
                        </div>
                        <div class="dsh-chart-labels">
                            <span>Day 1</span><span>Day 7</span><span>Day 14</span><span>Day 21</span><span>Today</span>
                        </div>
                    </div>

                    <!-- Activity feed -->
                    <div class="dsh-activity-panel">
                        <div class="dsh-panel-header">
                            <span class="dsh-panel-title">Recent Activity</span>
                            <span class="dsh-activity-dot"></span>
                        </div>
                        <div class="dsh-activity-list">
                            <div class="dsh-activity-row">
                                <span class="dsh-act-badge dsh-act-win">WIN</span>
                                <span class="dsh-act-name">EURUSD</span>
                                <span class="dsh-act-val" style="color:#10B981">+$420</span>
                            </div>
                            <div class="dsh-activity-row">
                                <span class="dsh-act-badge dsh-act-win">WIN</span>
                                <span class="dsh-act-name">XAUUSD</span>
                                <span class="dsh-act-val" style="color:#10B981">+$820</span>
                            </div>
                            <div class="dsh-activity-row">
                                <span class="dsh-act-badge dsh-act-loss">LOSS</span>
                                <span class="dsh-act-name">GBPUSD</span>
                                <span class="dsh-act-val" style="color:#ef4444">–$180</span>
                            </div>
                            <div class="dsh-activity-row">
                                <span class="dsh-act-badge dsh-act-win">WIN</span>
                                <span class="dsh-act-name">BTCUSD</span>
                                <span class="dsh-act-val" style="color:#10B981">+$640</span>
                            </div>
                            <div class="dsh-activity-row dsh-activity-row-payout">
                                <span class="dsh-act-badge dsh-act-pay">PAYOUT</span>
                                <span class="dsh-act-name">Requested</span>
                                <span class="dsh-act-val" style="color:#10B981">$1,850 ✓</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom glow line -->
            <div class="dsh-bottom-glow"></div>
        </div>

    </div>
</section>

<div class="section-divider"></div>

<!-- QUICK FAQ -->
<section style="background:var(--bg);padding:0">
    <div class="hfaq-layout">

        <!-- Left -->
        <div class="hfaq-left">
            <div class="feat-label" style="margin-bottom:32px">
                <span class="feat-num">09</span>
                <span class="feat-pipe"></span>
                <span class="feat-cat">FAQs</span>
            </div>
            <h2 class="hfaq-title" data-stacking-words>Frequently Asked <span class="green">Questions</span></h2>
            <p class="hfaq-desc">Still unsure? We have answers. Learn everything you need to know about challenges, payouts, rules and rewards before you start.</p>
            <div class="hfaq-cta-block">
                <p class="hfaq-cta-text">Want specific guidance? Contact us now</p>
                <a href="contact.php" class="btn-outline">Contact Us</a>
            </div>
        </div>

        <!-- Right: accordion -->
        <div class="hfaq-right">
            <div class="home-faq" style="max-width:none;margin:0">
                <div class="home-faq-item">
                    <button class="home-faq-q" onclick="this.parentElement.classList.toggle('open')">
                        What is Doji Funding?
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </button>
                    <div class="home-faq-a">
                        <p>Doji Funding is a proprietary trading firm that provides funded accounts to talented traders. Pass our evaluation challenge and trade with our capital — keeping up to 90% of the profits you generate. We support MetaTrader 5 and cTrader with 1,000+ instruments across 8 asset classes.</p>
                    </div>
                </div>
                <div class="home-faq-item">
                    <button class="home-faq-q" onclick="this.parentElement.classList.toggle('open')">
                        How much does it cost to start?
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </button>
                    <div class="home-faq-a">
                        <p>Challenge accounts start from just $39 for a $5K account. We offer 20 different account sizes up to $100K, with the ability to scale to $500K total capital. Use our configurator to customize your exact parameters and see the price in real time.</p>
                    </div>
                </div>
                <div class="home-faq-item">
                    <button class="home-faq-q" onclick="this.parentElement.classList.toggle('open')">
                        How fast are payouts processed?
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </button>
                    <div class="home-faq-a">
                        <p>All payouts are processed within 24 hours — guaranteed. We offer weekly payout cycles so you never wait long for your rewards. Payments are sent via bank transfer, crypto, or your preferred method.</p>
                    </div>
                </div>
                <div class="home-faq-item">
                    <button class="home-faq-q" onclick="this.parentElement.classList.toggle('open')">
                        Is there a time limit to pass the challenge?
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </button>
                    <div class="home-faq-a">
                        <p>No. Doji Funding has no time limit on the challenge phase. Take as long as you need to reach your profit target while respecting the drawdown rules. Trade at your own pace with zero pressure.</p>
                    </div>
                </div>
                <div class="home-faq-item">
                    <button class="home-faq-q" onclick="this.parentElement.classList.toggle('open')">
                        What are Doji Coins™?
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </button>
                    <div class="home-faq-a">
                        <p>Doji Coins™ are our loyalty rewards. Every trade you execute earns coins based on volume. Redeem them for free challenges, boosted profit splits, reduced fees, and exclusive perks. Your trading activity is always rewarded — win or lose.</p>
                    </div>
                </div>
                <div class="home-faq-item">
                    <button class="home-faq-q" onclick="this.parentElement.classList.toggle('open')">
                        Can I trade during news events?
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </button>
                    <div class="home-faq-a">
                        <p>Yes. News trading is fully allowed on Doji Funding. Trade NFP, FOMC, CPI, and any other high-impact events freely. We believe if there's opportunity in the markets, you should be able to take it.</p>
                    </div>
                </div>
                <div style="padding-top:24px;border-top:1px solid rgba(255,255,255,0.06)">
                    <a href="faq.php" class="feat-cta-primary">View all FAQs →</a>
                </div>
            </div>
        </div>

    </div>
</section>

<div class="section-divider"></div>

<!-- ENHANCED CTA -->
<section class="cta-enhanced s-dot">
    <div class="cta-enhanced-inner">
        <h2 data-stacking-words>Start Trading <span class="green">Your Way</span></h2>
        <p class="section-sub" style="margin:0 auto 40px">
            Join 4,200+ funded traders worldwide. Configure your challenge in under 2 minutes and start your journey to consistent profitability.
        </p>
        <div class="cta-btn-row">
            <a style="text-decoration:none" class="btn-primary-lg" href="challenges.php">Get Started — From $39</a>
            <a style="text-decoration:none" class="btn-outline" href="challenges.php#configurator">Configure Your Challenge</a>
        </div>
        <div class="cta-price-anchor">
            <span>Accounts from <strong>$39</strong></span>
            <span>·</span>
            <span>Up to <strong>90% profit split</strong></span>
            <span>·</span>
            <span><strong>No time limit</strong></span>
        </div>
        <div class="cta-guarantee">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Regulated entity · 24h guaranteed payouts · 24/7 support
        </div>
    </div>
</section>

<div class="section-divider"></div>

<?php include 'includes/community.php'; ?>
