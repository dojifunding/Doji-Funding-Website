<?php
/**
 * Doji Funding — Challenges Page
 * 
 * Challenge type selector and full pricing configurator.
 * The configurator logic is in assets/js/configurator.js
 * Pricing data comes from config/pricing.php via window.DOJI_CONFIG.
 */
?>

<!-- HERO -->
<section class="hero" style="min-height:420px;padding:90px 32px 60px">
    <canvas class="hero-globe" id="heroSquare" aria-hidden="true"></canvas>
    <div class="hero-content">
        <div class="badge">Configure & Trade</div>
        <h1>Choose Your <span class="green">Challenge</span></h1>
        <p class="subtitle">Select your evaluation type, customize every parameter, and see your price in real time.</p>
    </div>
</section>

<div class="section-divider"></div>

<!-- TRUST BAR -->
<section style="background:var(--bg2);border-bottom:1px solid rgba(16,185,129,0.08)">
    <div class="trust-bar-wrap" style="max-width:1200px;margin:0 auto">
        <div class="trust-bar">
            <?php for ($i = 0; $i < 2; $i++): ?>
            <div class="trust-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                <span><strong>700K+</strong> Configurations</span>
            </div>
            <div class="trust-sep"></div>
            <div class="trust-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                <span><strong>Up to 90%</strong> Profit Split</span>
            </div>
            <div class="trust-sep"></div>
            <div class="trust-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span><strong>24h</strong> Payouts</span>
            </div>
            <div class="trust-sep"></div>
            <div class="trust-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span><strong>No</strong> Time Limit</span>
            </div>
            <div class="trust-sep"></div>
            <div class="trust-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span><strong>Regulated</strong> Gibraltar Entity</span>
            </div>
            <div class="trust-sep"></div>
            <div class="trust-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                <span><strong>4,200+</strong> Funded Traders</span>
            </div>
            <div class="trust-sep"></div>
            <?php endfor; ?>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<section style="padding:48px 16px 0;background:var(--bg)">
    <div class="section-inner">

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
                        <select class="preset-select" id="presetSelect" onchange="this.classList.toggle('has-value',!!this.value);if(this.value){Configurator.loadPreset(this.value)}">
                            <option value="">Compare with other firms...</option>
                        </select>
                    </div>
                </div>

            </div>
        </div>

        <!-- ═══ CONFIGURATOR LAYOUT ═══ -->
        <div class="cfg-layout">

            <!-- LEFT PANEL: Parameters -->
            <div class="cfg-panel">
                <div class="cfg-header">
                    <h2 class="cfg-title">
                        Challenge Configurator
                        <span class="seo-tag">H2</span>
                    </h2>
                    <span class="cfg-badge" id="cfgBadge">FAST TRACK</span>
                </div>

                <!-- Sliders injected by configurator.js -->
                <div id="slidersContainer"></div>

                <!-- Reset Button -->
                <button class="reset-btn" onclick="Configurator.reset()">
                    ↺ Reset to Defaults
                </button>

            </div>

            <!-- RIGHT PANEL: Summary + Price -->
            <div class="cfg-panel">
                <div class="cfg-header">
                    <h2 class="cfg-title">Your Configuration</h2>
                </div>

                <!-- Configuration Summary -->
                <div class="summary-box">
                    <div class="summary-title">Configuration Summary</div>
                    <div class="summary-grid" id="summaryGrid"></div>
                </div>

                <!-- Total Price -->
                <div class="price-box">
                    <div class="price-label">Total Price</div>
                    <div id="priceDisplay">
                        <div class="price-val" id="priceVal">$249</div>
                    </div>
                </div>

                <!-- Promo Code -->
                <div class="promo-row">
                    <input class="promo-input" id="promoInput" 
                           placeholder="Enter promo code" 
                           onkeydown="if(event.key==='Enter')Configurator.applyPromo()">
                    <button class="promo-btn" id="promoBtn" 
                            onclick="Configurator.applyPromo()">Apply</button>
                </div>
                <div id="promoMsg"></div>

                <!-- Share -->
                <button class="share-btn" onclick="Configurator.share()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg> Share Configuration
                </button>
                <div id="shareMsg"></div>

                <!-- Purchase -->
                <button class="purchase-btn" id="purchaseBtn" onclick="Configurator.purchase()">
                    Purchase Challenge — $249
                </button>

                <!-- Note -->
                <div class="note-box" id="noteBox">
                    <strong class="green">Note:</strong> 
                    Consistency Rule and Payout Frequency apply to Funded accounts only.
                </div>

                <!-- Simulation Disclaimer -->
                <div class="note-box note-disclaimer">
                    Doji Funding® is a simulated trading platform designed for performance evaluation and skill development. No real capital is deployed or at risk. Program fees provide access to our simulation and assessment tools. Performance-based payouts are discretionary and subject to compliance with all program rules and Doji Funding®'s <a href="terms.php" class="disclaimer-link">Terms&nbsp;of&nbsp;Service</a>.
                </div>
            </div>
        </div>

        <!-- Trading Objectives — removed, future section TBD -->
        <!-- Hidden stubs keep configurator.js references valid -->
        <div id="objectivesSection" style="display:none">
            <div id="objEvalCard"><div id="objEvalNum"></div><div id="objEvalTitle"></div><div id="objTargetBox"></div><div id="objDailyVal"></div><div id="objMaxVal"></div><div id="objChartDaily"><svg id="objChartDailySvg"></svg></div><div id="objChartMax"><svg id="objChartMaxSvg"></svg></div><div id="objGuidelines"></div><div id="objTags"></div><div id="objFlow"></div><div id="objLimitDaily"></div><div id="objLimitMax"></div></div>
            <div id="objFundedCard"><div id="objFundedNum"></div><div id="objFDailyVal"></div><div id="objFMaxVal"></div><div id="objFConsVal"></div><div id="objChartFdaily"><svg id="objChartFdailySvg"></svg></div><div id="objChartFmax"><svg id="objChartFmaxSvg"></svg></div><div id="objFGuidelines"></div><div id="objRewards"></div></div>
        </div>

        <!-- SEO Strategy Block -->
        <div class="seo-only seo-strategy">
            <div class="seo-strategy-title">SEO CONTENT STRATEGY — CHALLENGES PAGE</div>
            <div class="seo-strategy-grid">
                <div>
                    <div class="seo-strategy-label">Target Keywords</div>
                    prop firm challenge, funded trading account, 1 step challenge, 
                    2 step challenge, trading evaluation, customizable prop firm, best prop firm 2026
                </div>
                <div>
                    <div class="seo-strategy-label">Internal Links From This Page</div>
                    → FAQ (rules details), Blog posts (strategy guides), 
                    About (trust building), Individual challenge detail pages (future)
                </div>
            </div>
        </div>

    </div>
</section>

<div class="section-divider"></div>

<?php include 'includes/community.php'; ?>
