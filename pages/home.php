<?php
/**
 * Doji Funding — Homepage
 * 
 * Hero, trust bar, how it works, challenge cards, stats, CTA.
 */
?>

<!-- HERO (Interactive Globe) -->
<section class="hero">
    <!-- 3D Globe Background -->
    <canvas class="hero-globe" id="heroGlobe"></canvas>
    <div class="hero-glow" style="top:-200px;left:50%;transform:translateX(-50%)"></div>
    <!-- Overlay -->
    <div class="hero-overlay"></div>
    <!-- Content -->
    <div class="hero-split">
        <div class="hero-left">
            <div class="badge">100% Customizable Prop Firm</div>
            <h1>
                Trade Your Way.<br><span class="green">Get Funded.</span>
                <span class="seo-tag">H1</span>
            </h1>
            <p class="subtitle">
                Choose your profit target, drawdown limits, and payout split. 
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
                <a style="text-decoration:none" class="btn-outline" href="competitions.php">Join Competitions</a>
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

<!-- KEY HIGHLIGHTS — FundedNext style -->
<section class="section" style="background:var(--bg2);padding-top:80px;padding-bottom:80px">
    <div class="section-inner" style="max-width:1200px;margin:0 auto">
        <div class="kh-layout">
            <div class="kh-left">
                <h2 style="font-size:clamp(28px,3.5vw,40px);line-height:1.2;margin-bottom:28px">Power up your trading success with Doji Funding and unlock your <span class="green">full potential.</span></h2>
                <div class="kh-more-list">
                    <div class="kh-more-item"><?= icon('check', 14) ?> Raw spreads from 0.0 pips</div>
                    <div class="kh-more-item"><?= icon('check', 14) ?> 8 asset classes — 1,000+ instruments</div>
                    <div class="kh-more-item"><?= icon('check', 14) ?> 20 account sizes for every budget</div>
                    <div class="kh-more-item"><?= icon('check', 14) ?> Starting from $39 only</div>
                </div>
            </div>
            <div class="kh-grid">
                <div class="kh-card">
                    <div class="kh-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg></div>
                    <h3>700K+ Configurations</h3>
                    <p>The most advanced configurator in prop trading. Customize every parameter — from profit target to drawdown type. Built for beginners and pros alike.</p>
                </div>
                <div class="kh-card">
                    <div class="kh-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
                    <h3>24h Guaranteed Payouts</h3>
                    <p>Request your payout and receive it within 24 hours — guaranteed. Weekly payout cycles so you never wait long for your rewards.</p>
                </div>
                <div class="kh-card">
                    <div class="kh-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg></div>
                    <h3>News Trading Allowed</h3>
                    <p>Trade high-impact events freely. If there's opportunity in the markets during NFP, FOMC or CPI — you should be able to take it.</p>
                </div>
                <div class="kh-card">
                    <div class="kh-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></div>
                    <h3>Institutional Spreads & Leverage</h3>
                    <p>Spreads from 0.0 pips and leverage up to 1:100. Powered by Tier-1 liquidity providers for professional-grade execution on every trade.</p>
                </div>
                <div class="kh-card">
                    <div class="kh-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg></div>
                    <h3>Challenge Reset</h3>
                    <p>Breached a rule? Reset your account and restart your trading journey. One setback shouldn't end your path — get back on track quickly.</p>
                </div>
                <div class="kh-card">
                    <div class="kh-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg></div>
                    <h3>Monthly Competitions</h3>
                    <p>Compete against other traders in free and paid competitions. Win prizes, earn Doji Coins™, and climb the leaderboards every month.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- HOW IT WORKS — 4 Steps -->
<section class="section" style="background:var(--bg)">
    <div class="section-inner" style="max-width:1200px;margin:0 auto;text-align:center">
        <h2>How It Works</h2>
        <p class="section-sub" style="text-align:center;color:var(--text2);font-size:16px;margin-bottom:48px">Your path to becoming a funded trader</p>
        <div class="hiw-grid">
            <div class="hiw-card" style="--step-opacity:0.3; --border-top:#022c22; --border-bottom:#064e3b">
                <div class="hiw-num">01</div>
                <h3>Choose Plan</h3>
                <p>Select an account size that matches your risk profile and trading style.</p>
                <a href="/challenges.php" class="hiw-cta">READY TO START →</a>
            </div>
            <div class="hiw-card" style="--step-opacity:0.5; --border-top:#064e3b; --border-bottom:#047857">
                <div class="hiw-num">02</div>
                <h3>Pass Challenge</h3>
                <p>Demonstrate your edge by hitting the profit target while managing risk.</p>
            </div>
            <div class="hiw-card" style="--step-opacity:0.75; --border-top:#047857; --border-bottom:#10B981">
                <div class="hiw-num">03</div>
                <h3>Verify Identity</h3>
                <p>Fast-track KYC process to ensure institutional compliance and security.</p>
            </div>
            <div class="hiw-card" style="--step-opacity:1; --border-top:#10B981; --border-bottom:#34d399">
                <div class="hiw-num">04</div>
                <h3>Get Funded</h3>
                <p>Trade live capital and keep up to 90% of your generated profits.</p>
            </div>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- CONFIGURATOR (Home) -->
<section class="section" style="background:var(--bg2);padding-top:80px;padding-bottom:80px">
    <div class="section-inner">
        <h2 style="text-align:center;margin-bottom:10px">Configure Your <span class="green">Challenge</span></h2>
        <p class="section-sub" style="text-align:center;color:var(--text2);font-size:16px;margin-bottom:44px">Customize every parameter. See your price in real time.</p>

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
<section class="section" style="background:var(--bg);padding-top:100px;padding-bottom:100px">
    <div class="section-inner" style="max-width:1200px;margin:0 auto">
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
<section class="section" style="background:var(--bg2);overflow:hidden">
    <div class="section-inner" style="max-width:1100px;margin:0 auto">

        <!-- Header -->
        <div class="dc-header">
            <div class="dc-header-left">
                <div class="dc-badge">LOYALTY PROGRAM</div>
                <h2 class="dc-title">Earn <span class="green">Doji Coins</span>™ Every Time You Trade</h2>
                <p class="dc-subtitle">Every trade you take — win or lose — earns you Doji Coins™. Accumulate rewards and spend them on free challenges, boosted splits, and exclusive perks. Your dedication is always rewarded.</p>
                <div class="dc-cta-row">
                    <a href="challenges.php" style="text-decoration:none" class="btn-primary-lg">Get Funded →</a>
                    <a href="faq.php#doji-coins" class="dc-learn-more">Learn more</a>
                </div>
            </div>
            <div class="dc-header-visual">
                <!-- Animated Doji Coin visual -->
                <div class="dc-coin-wrap">
                    <div class="dc-coin">
                        <div class="dc-coin-ring"></div>
                        <div class="dc-coin-ring dc-coin-ring-2"></div>
                        <img src="<?= DOJI_COIN_FILE ?>" alt="Doji Coin" class="dc-coin-logo">
                    </div>
                    <div class="dc-coin-badge">
                        <img src="<?= DOJI_COIN_FILE ?>" alt="" class="dc-coin-badge-icon">
                        <span class="dc-coin-badge-val">5,300</span>
                    </div>
                </div>
            </div>
        </div>

        <div style="height:48px"></div>

        <!-- 3 Steps: Trade → Earn → Spend -->
        <div class="dc-steps">

            <!-- STEP 1: Trade -->
            <div class="dc-step-card">
                <div class="dc-step-visual">
                    <div class="dc-step-mockup">
                        <!-- Mini chart mockup -->
                        <div class="dc-mockup-header">
                            <span class="dc-mockup-pair">EUR/USD</span>
                            <span class="dc-mockup-price">1.0847</span>
                        </div>
                        <svg viewBox="0 0 200 80" class="dc-mockup-chart">
                            <path d="M0 60 L20 52 L40 55 L60 40 L80 35 L100 42 L120 28 L140 22 L160 30 L180 15 L200 10" stroke="#10B981" stroke-width="2" fill="none" stroke-linecap="round"/>
                            <path d="M0 60 L20 52 L40 55 L60 40 L80 35 L100 42 L120 28 L140 22 L160 30 L180 15 L200 10 L200 80 L0 80 Z" fill="url(#dcChartFill)" opacity="0.3"/>
                            <defs><linearGradient id="dcChartFill" x1="0" y1="0" x2="0" y2="80" gradientUnits="userSpaceOnUse"><stop stop-color="#10B981"/><stop offset="1" stop-color="#10B981" stop-opacity="0"/></linearGradient></defs>
                        </svg>
                        <!-- Floating second pair -->
                        <div class="dc-mockup-float">
                            <span class="dc-mockup-float-pair">BTC/USD</span>
                            <span class="dc-mockup-float-price">$65,000</span>
                            <span class="dc-mockup-float-pct green">+0.25%</span>
                        </div>
                    </div>
                </div>
                <div class="dc-step-arrow">»</div>
                <div class="dc-step-content">
                    <h3>Trade</h3>
                    <p>Every trade you execute on your evaluation or funded account earns Doji Coins™. Both phases of the challenge participate in the rewards program.</p>
                </div>
            </div>

            <!-- STEP 2: Earn -->
            <div class="dc-step-card">
                <div class="dc-step-visual">
                    <div class="dc-earn-visual">
                        <div class="dc-earn-coin dc-earn-coin-1">
                            <img src="<?= DOJI_COIN_FILE ?>" alt="" class="dc-earn-coin-img">
                        </div>
                        <div class="dc-earn-coin dc-earn-coin-2">
                            <img src="<?= DOJI_COIN_FILE ?>" alt="" class="dc-earn-coin-img">
                        </div>
                        <div class="dc-earn-coin dc-earn-coin-3">
                            <img src="<?= DOJI_COIN_FILE ?>" alt="" class="dc-earn-coin-img">
                        </div>
                        <div class="dc-earn-counter">
                            <img src="<?= DOJI_COIN_FILE ?>" alt="" style="width:16px;height:16px;border-radius:4px">
                            <span>5,300</span>
                        </div>
                    </div>
                </div>
                <div class="dc-step-arrow">»</div>
                <div class="dc-step-content">
                    <h3>Earn</h3>
                    <p>As you navigate your trading journey, Doji Coins™ accumulate based on the volume (lots) you trade. Track your balance in real-time on your dashboard.</p>
                </div>
            </div>

            <!-- STEP 3: Spend -->
            <div class="dc-step-card">
                <div class="dc-step-visual">
                    <div class="dc-spend-visual">
                        <div class="dc-spend-card-main">
                            <div class="dc-spend-check"><?= icon("check", 14) ?></div>
                            <div class="dc-spend-text">Free Challenge</div>
                            <div class="dc-spend-sub">Redeem with Doji Coins™</div>
                        </div>
                        <div class="dc-spend-card-float">
                            <span class="dc-spend-tag">Perk</span>
                            <span class="dc-spend-float-text">+5% Split Boost</span>
                        </div>
                        <div class="dc-spend-cost">
                            <span>3,200</span>
                            <img src="<?= DOJI_COIN_FILE ?>" alt="" style="width:14px;height:14px;border-radius:3px">
                        </div>
                    </div>
                </div>
                <div class="dc-step-content">
                    <h3>Spend</h3>
                    <p>Use your Doji Coins™ on trading perks: free challenges, boosted profit splits, reduced fees, or exclusive merchandise. Your loyalty pays off.</p>
                </div>
            </div>

        </div>

    </div>
</section>

<div class="section-divider"></div>

<!-- BUILT FOR TRADERS — Pill Bar -->
<section class="section" style="background:var(--bg)">
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
<section class="section" style="background:var(--bg2)">
    <div class="section-inner">
        <h2>Trusted by <span class="green">Thousands</span> of Traders</h2>
        <p class="section-sub">Real feedback from funded traders around the world.</p>
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-text">"The configurator is a game-changer. I set my own drawdown limits and profit target — no other prop firm gives you this level of control. Got funded in 12 days."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">M</div>
                    <div>
                        <div class="testimonial-name">Marcus T.</div>
                        <div class="testimonial-role">Forex Trader · $50K Funded</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-text">"Payout came in under 18 hours. I've been with 3 other firms and none of them process this fast. The 90% split on a $100K account is unbeatable."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">S</div>
                    <div>
                        <div class="testimonial-name">Sarah K.</div>
                        <div class="testimonial-role">Indices Trader · $100K Funded</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-text">"Started with a $5K challenge for $39. The Doji Coins reward system keeps me motivated — I've already redeemed a free challenge. Support team is responsive 24/7."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">A</div>
                    <div>
                        <div class="testimonial-name">Ahmed R.</div>
                        <div class="testimonial-role">Crypto Trader · $25K Funded</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- SOCIAL PROOF METRICS -->
<section class="section" style="background:var(--bg);border-top:1px solid rgba(16,185,129,0.08);border-bottom:1px solid rgba(16,185,129,0.08)">
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
<section class="section" style="background:var(--bg2)">
    <div class="section-inner">
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
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="8 12 11 15 16 9"/>
                    <path d="M12 2v4"/>
                    <path d="M12 18v4"/>
                    <path d="M4.93 4.93l2.83 2.83"/>
                    <path d="M16.24 16.24l2.83 2.83"/>
                </svg>
                <div class="platform-name">cTrader</div>
                <div class="platform-desc">Modern interface with Level II pricing, advanced cBots and copy trading built-in</div>
            </div>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- COMPARISON TABLE — Doji vs Competitors -->
<section class="section" style="background:var(--bg)">
    <div class="section-inner">
        <h2>How Doji Funding <span class="green">Compares</span></h2>
        <p class="section-sub">See why traders choose Doji over other prop firms.</p>
        <div class="compare-table-wrap">
            <table class="compare-table">
                <thead>
                    <tr>
                        <th>Feature</th>
                        <th class="compare-highlight">Doji Funding</th>
                        <th>FTMO</th>
                        <th>MyFundedFX</th>
                        <th>FundedNext</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Starting Price</td>
                        <td class="compare-highlight"><span class="compare-val-green">From $39</span></td>
                        <td><span class="compare-val">$155</span></td>
                        <td><span class="compare-val">$49</span></td>
                        <td><span class="compare-val">$32</span></td>
                    </tr>
                    <tr>
                        <td>Profit Split</td>
                        <td class="compare-highlight"><span class="compare-val-green">Up to 90%</span></td>
                        <td><span class="compare-val">80%</span></td>
                        <td><span class="compare-val">80%</span></td>
                        <td><span class="compare-val">Up to 90%</span></td>
                    </tr>
                    <tr>
                        <td>Custom Parameters</td>
                        <td class="compare-highlight"><span class="compare-check">✓</span> 700K+ combos</td>
                        <td><span class="compare-cross">✕</span> Fixed</td>
                        <td><span class="compare-cross">✕</span> Fixed</td>
                        <td><span class="compare-cross">✕</span> Limited</td>
                    </tr>
                    <tr>
                        <td>Payout Speed</td>
                        <td class="compare-highlight"><span class="compare-val-green">24h Guaranteed</span></td>
                        <td><span class="compare-val">1–3 days</span></td>
                        <td><span class="compare-val">1–2 days</span></td>
                        <td><span class="compare-val">1–2 days</span></td>
                    </tr>
                    <tr>
                        <td>News Trading</td>
                        <td class="compare-highlight"><span class="compare-check">✓</span></td>
                        <td><span class="compare-cross">✕</span></td>
                        <td><span class="compare-check">✓</span></td>
                        <td><span class="compare-check">✓</span></td>
                    </tr>
                    <tr>
                        <td>Time Limit</td>
                        <td class="compare-highlight"><span class="compare-val-green">None</span></td>
                        <td><span class="compare-val">30 days</span></td>
                        <td><span class="compare-val">None</span></td>
                        <td><span class="compare-val">None</span></td>
                    </tr>
                    <tr>
                        <td>Rewards Program</td>
                        <td class="compare-highlight"><span class="compare-check">✓</span> Doji Coins™</td>
                        <td><span class="compare-cross">✕</span></td>
                        <td><span class="compare-cross">✕</span></td>
                        <td><span class="compare-cross">✕</span></td>
                    </tr>
                    <tr>
                        <td>Platforms</td>
                        <td class="compare-highlight"><span class="compare-val-green">MT5 + cTrader</span></td>
                        <td><span class="compare-val">MT4/MT5</span></td>
                        <td><span class="compare-val">MT4/MT5</span></td>
                        <td><span class="compare-val">MT4/MT5</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- QUICK FAQ -->
<section class="section" style="background:var(--bg2)">
    <div class="section-inner">
        <h2>Frequently Asked <span class="green">Questions</span></h2>
        <p class="section-sub">Everything you need to know before starting your challenge.</p>
        <div class="home-faq">
            <div class="home-faq-item">
                <button class="home-faq-q" onclick="this.parentElement.classList.toggle('open')">
                    What is Doji Funding?
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </button>
                <div class="home-faq-a">
                    <p>Doji Funding is a proprietary trading firm that provides funded accounts to talented traders. Pass our evaluation challenge and trade with our capital — keeping up to 90% of the profits you generate. We support MetaTrader 5 and cTrader with 1,000+ instruments across 8 asset classes.</p>
                </div>
            </div>
            <div class="home-faq-item">
                <button class="home-faq-q" onclick="this.parentElement.classList.toggle('open')">
                    How much does it cost to start?
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </button>
                <div class="home-faq-a">
                    <p>Challenge accounts start from just $39 for a $5K account. We offer 20 different account sizes up to $100K, with the ability to scale to $500K total capital. Use our configurator to customize your exact parameters and see the price in real time.</p>
                </div>
            </div>
            <div class="home-faq-item">
                <button class="home-faq-q" onclick="this.parentElement.classList.toggle('open')">
                    How fast are payouts processed?
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </button>
                <div class="home-faq-a">
                    <p>All payouts are processed within 24 hours — guaranteed. We offer weekly payout cycles so you never wait long for your rewards. Payments are sent via bank transfer, crypto, or your preferred method.</p>
                </div>
            </div>
            <div class="home-faq-item">
                <button class="home-faq-q" onclick="this.parentElement.classList.toggle('open')">
                    Is there a time limit to pass the challenge?
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </button>
                <div class="home-faq-a">
                    <p>No. Doji Funding has no time limit on the challenge phase. Take as long as you need to reach your profit target while respecting the drawdown rules. Trade at your own pace with zero pressure.</p>
                </div>
            </div>
            <div class="home-faq-item">
                <button class="home-faq-q" onclick="this.parentElement.classList.toggle('open')">
                    What are Doji Coins™?
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </button>
                <div class="home-faq-a">
                    <p>Doji Coins™ are our loyalty rewards. Every trade you execute earns coins based on volume. Redeem them for free challenges, boosted profit splits, reduced fees, and exclusive perks. Your trading activity is always rewarded — win or lose.</p>
                </div>
            </div>
            <div class="home-faq-item">
                <button class="home-faq-q" onclick="this.parentElement.classList.toggle('open')">
                    Can I trade during news events?
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </button>
                <div class="home-faq-a">
                    <p>Yes. News trading is fully allowed on Doji Funding. Trade NFP, FOMC, CPI, and any other high-impact events freely. We believe if there's opportunity in the markets, you should be able to take it.</p>
                </div>
            </div>
        </div>
        <div style="text-align:center;margin-top:32px">
            <a href="faq.php" style="color:var(--green);font-size:14px;font-weight:600;text-decoration:none">View all FAQs →</a>
        </div>
    </div>
</section>

<!-- ENHANCED CTA -->
<section class="cta-enhanced">
    <div class="cta-enhanced-inner">
        <h2>Start Trading <span class="green">Your Way</span></h2>
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

<?php include 'includes/community.php'; ?>
