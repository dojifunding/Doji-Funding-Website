<?php
/**
 * Doji Funding — Challenges Page
 * 
 * Challenge type selector and full pricing configurator.
 * The configurator logic is in assets/js/configurator.js
 * Pricing data comes from config/pricing.php via window.DOJI_CONFIG.
 */
?>

<!-- BREADCRUMB -->
<!-- HERO with Liquid Wave -->
<section class="hero" style="min-height:420px;padding:90px 32px 60px">
    <canvas class="hero-globe" id="waveCanvas"></canvas>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="badge">Configure & Trade</div>
        <h1>Choose Your <span class="green">Challenge</span></h1>
        <p class="subtitle">Select your evaluation type, customize every parameter, and see your price in real time.</p>
    </div>
</section>

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

                <!-- Preset Picker (Challenges page only) -->
                <div class="preset-picker">
                    <div class="preset-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        Load a Preset
                    </div>
                    <select class="preset-select" id="presetSelect" onchange="this.classList.toggle('has-value',!!this.value);if(this.value){Configurator.loadPreset(this.value)}">
                        <option value="">Compare with other prop firms...</option>
                        <?php if (function_exists('getPresetsJson')): ?>
                        <?php foreach ($challengePresets as $group): ?>
                        <?php if (!empty($group['presets'])): ?>
                        <optgroup label="<?= htmlspecialchars($group['group']) ?>">
                            <?php foreach ($group['presets'] as $p): ?>
                            <option value="<?= $p['id'] ?>" data-note="<?= htmlspecialchars($p['note']) ?>"><?= htmlspecialchars($p['name']) ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                        <?php endif; ?>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <div class="preset-hint">Load a competitor's config to compare pricing instantly</div>
                </div>
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
                    🔗 Share Configuration
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

        <!-- ═══ TRADING OBJECTIVES — FundingPips-style Cards ═══ -->
        <div style="height:64px"></div>
        <div id="objectivesSection">

            <h2 style="text-align:center;margin-bottom:8px">Trading <span class="green">Objectives</span></h2>
            <p class="section-sub" style="margin-bottom:36px">Your evaluation path to a Doji Funded account — all values update dynamically.</p>

            <!-- ── EVALUATION STAGE ── -->
            <div class="obj-card" id="objEvalCard">
                <div class="obj-card-header">
                    <div class="obj-card-num" id="objEvalNum">1</div>
                    <div>
                        <div class="obj-card-title" id="objEvalTitle">The Evaluation Stage</div>
                        <div class="obj-card-sub">Complete the evaluation to get your Doji Funded account.</div>
                    </div>
                </div>

                <div class="obj-two-col">
                    <!-- Left: Targets -->
                    <div>
                        <div class="obj-section-label green">TARGETS — HOW TO PASS</div>
                        <div class="obj-target-box" id="objTargetBox"></div>
                    </div>
                    <!-- Right: Limits -->
                    <div>
                        <div class="obj-section-label" style="color:var(--red)">LIMITS — HARD BREACHES</div>
                        <p style="font-size:13px;color:var(--text2);margin-bottom:12px">These limits apply to the evaluation.</p>
                        <div class="obj-limit-card" id="objLimitDaily" onclick="toggleObjChart('daily')">
                            <div class="obj-limit-left">
                                <svg viewBox="0 0 20 20" fill="none" width="18" height="18"><path d="M3 14l3-4 3 2 4-5 4 3" stroke="var(--text3)" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <div>
                                    <div class="obj-limit-name">Max Daily Loss</div>
                                    <div class="obj-limit-desc">The amount you are allowed to lose every day.</div>
                                </div>
                            </div>
                            <div class="obj-limit-right">
                                <svg class="obj-chart-icon" viewBox="0 0 20 20" fill="none" width="18" height="18"><rect x="2" y="2" width="16" height="16" rx="2" stroke="var(--text3)" stroke-width="1"/><path d="M5 14l3-4 3 2 4-5" stroke="var(--green)" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
                                <span class="obj-limit-val" id="objDailyVal">5%</span>
                            </div>
                        </div>
                        <!-- Daily chart popover -->
                        <div class="obj-chart-popover" id="objChartDaily">
                            <div class="obj-chart-title">Max Daily Loss</div>
                            <svg class="obj-chart-svg" viewBox="0 0 280 120" id="objChartDailySvg"></svg>
                            <div class="obj-chart-desc">The most you can lose in a single trading day.</div>
                        </div>

                        <div class="obj-limit-card" id="objLimitMax" onclick="toggleObjChart('max')">
                            <div class="obj-limit-left">
                                <svg viewBox="0 0 20 20" fill="none" width="18" height="18"><circle cx="10" cy="10" r="7" stroke="var(--text3)" stroke-width="1.5" fill="none"/><path d="M10 6v4l3 2" stroke="var(--text3)" stroke-width="1.5" stroke-linecap="round"/></svg>
                                <div>
                                    <div class="obj-limit-name">Max Overall Loss</div>
                                    <div class="obj-limit-desc">The amount you are allowed to lose overall.</div>
                                </div>
                            </div>
                            <div class="obj-limit-right">
                                <svg class="obj-chart-icon" viewBox="0 0 20 20" fill="none" width="18" height="18"><rect x="2" y="2" width="16" height="16" rx="2" stroke="var(--text3)" stroke-width="1"/><path d="M5 14l3-4 3 2 4-5" stroke="var(--green)" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
                                <span class="obj-limit-val" style="color:var(--red)" id="objMaxVal">8%</span>
                            </div>
                        </div>
                        <!-- Max chart popover -->
                        <div class="obj-chart-popover" id="objChartMax">
                            <div class="obj-chart-title">Max Overall Loss</div>
                            <svg class="obj-chart-svg" viewBox="0 0 280 120" id="objChartMaxSvg"></svg>
                            <div class="obj-chart-desc">A fixed floor your account balance can never breach.</div>
                        </div>
                    </div>
                </div>

                <!-- Guidelines -->
                <div class="obj-section-label" style="margin-top:28px">GUIDELINES</div>
                <div class="obj-guidelines" id="objGuidelines"></div>

                <!-- Tags -->
                <div class="obj-tags" id="objTags"></div>

                <!-- Progress -->
                <div class="obj-flow" id="objFlow"></div>
            </div>

            <!-- ── DOJI FUNDED ACCOUNT ── -->
            <div style="height:24px"></div>
            <div class="obj-card" id="objFundedCard">
                <div class="obj-card-header">
                    <div class="obj-card-num funded" id="objFundedNum">2</div>
                    <div>
                        <div class="obj-card-title">Doji Funded Account</div>
                        <div class="obj-card-sub">Follow these rules to get rewarded on your Doji Funded account.</div>
                    </div>
                </div>

                <div class="obj-section-label" style="color:var(--red)">RULES & LIMITS</div>
                <div class="obj-limit-card" onclick="toggleObjChart('fdaily')">
                    <div class="obj-limit-left">
                        <svg viewBox="0 0 20 20" fill="none" width="18" height="18"><path d="M3 14l3-4 3 2 4-5 4 3" stroke="var(--text3)" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <div>
                            <div class="obj-limit-name">Max Daily Loss</div>
                            <div class="obj-limit-desc">The amount you are allowed to lose every day.</div>
                        </div>
                    </div>
                    <div class="obj-limit-right">
                        <svg class="obj-chart-icon" viewBox="0 0 20 20" fill="none" width="18" height="18"><rect x="2" y="2" width="16" height="16" rx="2" stroke="var(--text3)" stroke-width="1"/><path d="M5 14l3-4 3 2 4-5" stroke="var(--green)" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
                        <span class="obj-limit-val" id="objFDailyVal">5%</span>
                    </div>
                </div>
                <div class="obj-chart-popover" id="objChartFdaily">
                    <div class="obj-chart-title">Max Daily Loss — Funded</div>
                    <svg class="obj-chart-svg" viewBox="0 0 280 120" id="objChartFdailySvg"></svg>
                    <div class="obj-chart-desc">The most you can lose in a single trading day on your funded account.</div>
                </div>

                <div class="obj-limit-card" onclick="toggleObjChart('fmax')">
                    <div class="obj-limit-left">
                        <svg viewBox="0 0 20 20" fill="none" width="18" height="18"><circle cx="10" cy="10" r="7" stroke="var(--text3)" stroke-width="1.5" fill="none"/><path d="M10 6v4l3 2" stroke="var(--text3)" stroke-width="1.5" stroke-linecap="round"/></svg>
                        <div>
                            <div class="obj-limit-name">Max Overall Loss</div>
                            <div class="obj-limit-desc">The amount you are allowed to lose overall.</div>
                        </div>
                    </div>
                    <div class="obj-limit-right">
                        <svg class="obj-chart-icon" viewBox="0 0 20 20" fill="none" width="18" height="18"><rect x="2" y="2" width="16" height="16" rx="2" stroke="var(--text3)" stroke-width="1"/><path d="M5 14l3-4 3 2 4-5" stroke="var(--green)" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
                        <span class="obj-limit-val" style="color:var(--red)" id="objFMaxVal">8%</span>
                    </div>
                </div>
                <div class="obj-chart-popover" id="objChartFmax">
                    <div class="obj-chart-title">Max Overall Loss — Funded</div>
                    <svg class="obj-chart-svg" viewBox="0 0 280 120" id="objChartFmaxSvg"></svg>
                    <div class="obj-chart-desc">A fixed floor your funded account balance can never breach.</div>
                </div>

                <div class="obj-limit-card">
                    <div class="obj-limit-left">
                        <svg viewBox="0 0 20 20" fill="none" width="18" height="18"><circle cx="10" cy="10" r="6" stroke="var(--text3)" stroke-width="1.2" fill="none"/><circle cx="10" cy="10" r="2.5" fill="var(--text3)"/></svg>
                        <div>
                            <div class="obj-limit-name">Consistency Rule</div>
                            <div class="obj-limit-desc">No single day's profit can exceed this % of total profit.</div>
                        </div>
                    </div>
                    <div class="obj-limit-right">
                        <span class="obj-limit-val" style="color:var(--orange)" id="objFConsVal">30%</span>
                    </div>
                </div>

                <!-- Funded Guidelines -->
                <div class="obj-section-label" style="margin-top:28px">GUIDELINES</div>
                <div class="obj-guidelines" id="objFGuidelines"></div>

                <!-- Rewards -->
                <div class="obj-section-label green" style="margin-top:28px">REWARDS</div>
                <div class="obj-rewards" id="objRewards"></div>
            </div>

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

<?php include 'includes/community.php'; ?>
