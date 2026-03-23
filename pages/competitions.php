<?php
/**
 * Doji Funding — Competitions Page
 * Two competition types: Free & Paid with full rules display.
 * Visual effects matching homepage.
 */
?>

<!-- BREADCRUMB -->
<!-- HERO with Liquid Wave -->
<section class="hero" style="min-height:420px;padding:90px 32px 60px">
    <canvas class="hero-globe" id="waveCanvas"></canvas>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="badge">Compete & Win</div>
        <h1>Trading <span class="green">Competitions</span></h1>
        <p class="subtitle">Compete against other traders. Prove your skills. Win real prizes and funded accounts.</p>
    </div>
</section>

<!-- COMPETITION CARDS + RULES -->
<section class="section" style="background:var(--bg)">
    <div class="section-inner" style="max-width:1100px">

        <div class="comp-grid">

            <!-- ═══ FREE COMPETITION ═══ -->
            <div class="comp-column">
                <div class="comp-card scroll-reveal">
                    <div class="comp-badge comp-badge-green">FREE ENTRY</div>
                    <div class="comp-icon-wrap">
                        <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="comp-icon-svg">
                            <defs>
                                <filter id="freeGlow"><feGaussianBlur stdDeviation="3" result="g"/><feMerge><feMergeNode in="g"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
                                <linearGradient id="freeGrad" x1="20" y1="20" x2="60" y2="60" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#10B981" stop-opacity="0.25"/><stop offset="1" stop-color="#10B981" stop-opacity="0.05"/>
                                </linearGradient>
                            </defs>
                            <!-- Outer ring -->
                            <circle cx="40" cy="40" r="37" stroke="rgba(16,185,129,0.08)" stroke-width="0.5"/>
                            <circle cx="40" cy="40" r="33" stroke="rgba(16,185,129,0.12)" stroke-width="0.5" stroke-dasharray="4 3"/>
                            <!-- Podium -->
                            <rect x="18" y="42" width="14" height="20" rx="2" fill="url(#freeGrad)" stroke="rgba(16,185,129,0.25)" stroke-width="1"/>
                            <rect x="33" y="32" width="14" height="30" rx="2" fill="url(#freeGrad)" stroke="#10B981" stroke-width="1.2"/>
                            <rect x="48" y="48" width="14" height="14" rx="2" fill="url(#freeGrad)" stroke="rgba(16,185,129,0.25)" stroke-width="1"/>
                            <!-- Numbers on podium -->
                            <text x="25" y="55" text-anchor="middle" fill="rgba(16,185,129,0.5)" font-size="8" font-weight="700" font-family="Inter">2</text>
                            <text x="40" y="48" text-anchor="middle" fill="#10B981" font-size="10" font-weight="800" font-family="Inter">1</text>
                            <text x="55" y="58" text-anchor="middle" fill="rgba(16,185,129,0.5)" font-size="8" font-weight="700" font-family="Inter">3</text>
                            <!-- Star above 1st -->
                            <path d="M40 18 L42 24 L48 24 L43 28 L45 34 L40 30 L35 34 L37 28 L32 24 L38 24Z" fill="#10B981" filter="url(#freeGlow)" opacity="0.8"/>
                            <!-- Sparkles -->
                            <circle cx="22" cy="30" r="1" fill="#10B981" opacity="0.4"/>
                            <circle cx="58" cy="36" r="1.2" fill="#10B981" opacity="0.3"/>
                            <circle cx="15" cy="50" r="0.8" fill="#10B981" opacity="0.25"/>
                        </svg>
                    </div>
                    <h2 class="comp-title">Free Competition</h2>
                    <p class="comp-desc">No entry fee required. Trade on a simulated account and climb the leaderboard. Perfect for sharpening your skills risk-free and earning Doji Coins<span class="tm">™</span>.</p>
                    <div class="comp-prize-row">
                        <div class="comp-prize">
                            <span class="comp-prize-label">Entry</span>
                            <span class="comp-prize-val green">FREE</span>
                        </div>
                        <div class="comp-prize">
                            <span class="comp-prize-label">Prize Pool</span>
                            <span class="comp-prize-val">$500+</span>
                        </div>
                        <div class="comp-prize">
                            <span class="comp-prize-label">Duration</span>
                            <span class="comp-prize-val">2 Weeks</span>
                        </div>
                    </div>
                    <button class="comp-btn comp-btn-green" disabled>Coming Soon</button>
                </div>

                <!-- FREE RULES -->
                <div class="comp-rules scroll-reveal">
                    <h3 class="comp-rules-title">Free Competition Rules</h3>
                    <div class="comp-rule">
                        <span class="comp-rule-icon-svg"><svg viewBox="0 0 20 20" fill="none"><rect x="2" y="6" width="16" height="11" rx="2" stroke="#10B981" stroke-width="1.2"/><path d="M2 9h16" stroke="#10B981" stroke-width="1.2"/><rect x="4" y="12" width="5" height="2" rx="0.5" fill="rgba(16,185,129,0.4)"/></svg></span>
                        <div>
                            <div class="comp-rule-name">Starting Balance</div>
                            <div class="comp-rule-val">$10,000</div>
                        </div>
                    </div>
                    <div class="comp-rule">
                        <span class="comp-rule-icon-svg"><svg viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="7.5" stroke="#10B981" stroke-width="1.2"/><path d="M10 5v5l3.5 2" stroke="#10B981" stroke-width="1.2" stroke-linecap="round"/></svg></span>
                        <div>
                            <div class="comp-rule-name">Daily Loss Limit</div>
                            <div class="comp-rule-val">No Daily Loss</div>
                        </div>
                    </div>
                    <div class="comp-rule">
                        <span class="comp-rule-icon-svg"><svg viewBox="0 0 20 20" fill="none"><path d="M3 4v12h14" stroke="#10B981" stroke-width="1.2" stroke-linecap="round"/><polyline points="6,13 9,9 12,11 16,5" stroke="#10B981" stroke-width="1.2" fill="none" stroke-linecap="round" stroke-linejoin="round"/><circle cx="16" cy="5" r="1.5" fill="#10B981"/></svg></span>
                        <div>
                            <div class="comp-rule-name">Max Loss</div>
                            <div class="comp-rule-val">10% overall drawdown (Static)</div>
                        </div>
                    </div>
                    <div class="comp-rule">
                        <span class="comp-rule-icon-svg"><svg viewBox="0 0 20 20" fill="none"><rect x="3" y="3" width="14" height="14" rx="2" stroke="#10B981" stroke-width="1.2"/><path d="M3 7h14" stroke="#10B981" stroke-width="1.2"/><circle cx="7" cy="11" r="1" fill="#10B981"/><circle cx="10" cy="11" r="1" fill="#10B981"/><circle cx="13" cy="11" r="1" fill="#10B981"/><circle cx="7" cy="14" r="1" fill="#10B981" opacity="0.4"/><circle cx="10" cy="14" r="1" fill="#10B981" opacity="0.4"/></svg></span>
                        <div>
                            <div class="comp-rule-name">Min Trading Days</div>
                            <div class="comp-rule-val">5 days minimum</div>
                        </div>
                    </div>
                    <div class="comp-rule">
                        <span class="comp-rule-icon-svg"><svg viewBox="0 0 20 20" fill="none"><rect x="3" y="4" width="14" height="12" rx="1.5" stroke="#10B981" stroke-width="1.2"/><path d="M6 4V2M14 4V2" stroke="#10B981" stroke-width="1.2" stroke-linecap="round"/><path d="M7 9h6M7 12h3" stroke="#10B981" stroke-width="1" stroke-linecap="round" opacity="0.6"/></svg></span>
                        <div>
                            <div class="comp-rule-name">News Trading</div>
                            <div class="comp-rule-val green">Allowed</div>
                        </div>
                    </div>
                    <div class="comp-rule">
                        <span class="comp-rule-icon-svg"><svg viewBox="0 0 20 20" fill="none"><circle cx="10" cy="8" r="3" stroke="#10B981" stroke-width="1.2"/><path d="M10 11v2M7 15h6" stroke="#10B981" stroke-width="1.2" stroke-linecap="round"/><path d="M5 5l-2-2M15 5l2-2" stroke="#10B981" stroke-width="1" stroke-linecap="round" opacity="0.5"/></svg></span>
                        <div>
                            <div class="comp-rule-name">Overnight / Overweek</div>
                            <div class="comp-rule-val green">Allowed</div>
                        </div>
                    </div>
                    <div class="comp-rule">
                        <span class="comp-rule-icon-svg"><svg viewBox="0 0 20 20" fill="none"><rect x="4" y="3" width="12" height="14" rx="2" stroke="#10B981" stroke-width="1.2"/><path d="M7 7h6M7 10h4" stroke="#10B981" stroke-width="1" stroke-linecap="round" opacity="0.5"/><circle cx="13" cy="13" r="3.5" fill="var(--bg)" stroke="var(--red)" stroke-width="1.2"/><path d="M11.5 11.5l3 3M14.5 11.5l-3 3" stroke="var(--red)" stroke-width="1" stroke-linecap="round"/></svg></span>
                        <div>
                            <div class="comp-rule-name">EA / Bots</div>
                            <div class="comp-rule-val red">Not allowed</div>
                        </div>
                    </div>
                    <div class="comp-rule">
                        <span class="comp-rule-icon-svg"><svg viewBox="0 0 20 20" fill="none"><path d="M4 10h3l2-4 2 8 2-6 2 2h3" stroke="#10B981" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                        <div>
                            <div class="comp-rule-name">Positions</div>
                            <div class="comp-rule-val">Max based on margin</div>
                        </div>
                    </div>
                    <div class="comp-rule">
                        <span class="comp-rule-icon-svg"><svg viewBox="0 0 20 20" fill="none"><circle cx="10" cy="8" r="4" stroke="#10B981" stroke-width="1.2"/><path d="M4 17c0-3.3 2.7-6 6-6s6 2.7 6 6" stroke="#10B981" stroke-width="1.2" stroke-linecap="round"/></svg></span>
                        <div>
                            <div class="comp-rule-name">Account</div>
                            <div class="comp-rule-val">One free per person (profile/household)</div>
                        </div>
                    </div>
                    <div class="comp-rule">
                        <span class="comp-rule-icon-svg"><svg viewBox="0 0 20 20" fill="none"><path d="M4 10a6 6 0 1 1 12 0" stroke="#10B981" stroke-width="1.2" stroke-linecap="round"/><path d="M7 10l3-3 3 3" stroke="#10B981" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 7v7" stroke="#10B981" stroke-width="1.2" stroke-linecap="round"/></svg></span>
                        <div>
                            <div class="comp-rule-name">Re-entry</div>
                            <div class="comp-rule-val green">Unlimited</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══ PAID COMPETITION ═══ -->
            <div class="comp-column">
                <div class="comp-card comp-card-premium scroll-reveal">
                    <div class="comp-badge comp-badge-orange">PREMIUM</div>
                    <div class="comp-icon-wrap">
                        <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="comp-icon-svg">
                            <defs>
                                <filter id="paidGlow"><feGaussianBlur stdDeviation="3" result="g"/><feMerge><feMergeNode in="g"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
                                <linearGradient id="paidGrad" x1="20" y1="20" x2="60" y2="65" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#ff9f1a" stop-opacity="0.3"/><stop offset="1" stop-color="#ff9f1a" stop-opacity="0.05"/>
                                </linearGradient>
                                <linearGradient id="gemGrad" x1="30" y1="24" x2="50" y2="50" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#ffcc4d" stop-opacity="0.4"/><stop offset="1" stop-color="#ff9f1a" stop-opacity="0.15"/>
                                </linearGradient>
                            </defs>
                            <!-- Outer ring -->
                            <circle cx="40" cy="40" r="37" stroke="rgba(255,159,26,0.08)" stroke-width="0.5"/>
                            <circle cx="40" cy="40" r="33" stroke="rgba(255,159,26,0.12)" stroke-width="0.5" stroke-dasharray="4 3"/>
                            <!-- Trophy body -->
                            <path d="M28 26h24v6c0 8-5 14-12 16c-7-2-12-8-12-16v-6z" fill="url(#paidGrad)" stroke="#ff9f1a" stroke-width="1.2" stroke-linejoin="round"/>
                            <!-- Trophy handles -->
                            <path d="M28 30c-4 0-7 3-7 7s3 7 6 7" stroke="#ff9f1a" stroke-width="1" fill="none" stroke-linecap="round" opacity="0.6"/>
                            <path d="M52 30c4 0 7 3 7 7s-3 7-6 7" stroke="#ff9f1a" stroke-width="1" fill="none" stroke-linecap="round" opacity="0.6"/>
                            <!-- Trophy base -->
                            <path d="M36 48v4h8v-4" stroke="#ff9f1a" stroke-width="1" stroke-linecap="round"/>
                            <rect x="32" y="52" width="16" height="4" rx="1.5" fill="url(#paidGrad)" stroke="#ff9f1a" stroke-width="1"/>
                            <!-- Diamond inside trophy -->
                            <path d="M34 34 L37 30 L43 30 L46 34 L40 42 Z" fill="url(#gemGrad)" stroke="#ffcc4d" stroke-width="0.8" stroke-linejoin="round"/>
                            <path d="M34 34h12" stroke="#ffcc4d" stroke-width="0.5" opacity="0.6"/>
                            <path d="M37 30l1 4M43 30l-1 4M40 34v8" stroke="#ffcc4d" stroke-width="0.4" opacity="0.5"/>
                            <!-- Sparkles -->
                            <circle cx="20" cy="24" r="1.2" fill="#ff9f1a" opacity="0.5" filter="url(#paidGlow)"/>
                            <circle cx="60" cy="22" r="1" fill="#ffcc4d" opacity="0.4"/>
                            <circle cx="64" cy="40" r="0.8" fill="#ff9f1a" opacity="0.3"/>
                            <circle cx="16" cy="44" r="0.8" fill="#ff9f1a" opacity="0.25"/>
                            <!-- Light rays -->
                            <path d="M40 16v-3M30 18l-2-2M50 18l2-2" stroke="#ff9f1a" stroke-width="0.8" stroke-linecap="round" opacity="0.3"/>
                        </svg>
                    </div>
                    <h2 class="comp-title">Paid Competition</h2>
                    <p class="comp-desc">Higher stakes, higher rewards. Compete for a massive cash prize pool and funded accounts. The ultimate test of your trading skills.</p>
                    <div class="comp-prize-row">
                        <div class="comp-prize">
                            <span class="comp-prize-label">Entry</span>
                            <span class="comp-prize-val orange">From $49</span>
                        </div>
                        <div class="comp-prize">
                            <span class="comp-prize-label">Prize Pool</span>
                            <span class="comp-prize-val">$5,000+</span>
                        </div>
                        <div class="comp-prize">
                            <span class="comp-prize-label">Duration</span>
                            <span class="comp-prize-val">1 Month</span>
                        </div>
                    </div>
                    <button class="comp-btn comp-btn-orange" disabled>Coming Soon</button>
                </div>

                <!-- PAID RULES -->
                <div class="comp-rules comp-rules-premium scroll-reveal">
                    <h3 class="comp-rules-title">Paid Competition Rules</h3>
                    <div class="comp-rule">
                        <span class="comp-rule-icon-svg"><svg viewBox="0 0 20 20" fill="none"><rect x="2" y="6" width="16" height="11" rx="2" stroke="#ff9f1a" stroke-width="1.2"/><path d="M2 9h16" stroke="#ff9f1a" stroke-width="1.2"/><rect x="4" y="12" width="5" height="2" rx="0.5" fill="rgba(255,159,26,0.4)"/></svg></span>
                        <div>
                            <div class="comp-rule-name">Starting Balance</div>
                            <div class="comp-rule-val">$100,000</div>
                        </div>
                    </div>
                    <div class="comp-rule">
                        <span class="comp-rule-icon-svg"><svg viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="7.5" stroke="#ff9f1a" stroke-width="1.2"/><path d="M10 5v5l3.5 2" stroke="#ff9f1a" stroke-width="1.2" stroke-linecap="round"/></svg></span>
                        <div>
                            <div class="comp-rule-name">Daily Loss Limit</div>
                            <div class="comp-rule-val">No Daily Loss</div>
                        </div>
                    </div>
                    <div class="comp-rule">
                        <span class="comp-rule-icon-svg"><svg viewBox="0 0 20 20" fill="none"><path d="M3 4v12h14" stroke="#ff9f1a" stroke-width="1.2" stroke-linecap="round"/><polyline points="6,13 9,9 12,11 16,5" stroke="#ff9f1a" stroke-width="1.2" fill="none" stroke-linecap="round" stroke-linejoin="round"/><circle cx="16" cy="5" r="1.5" fill="#ff9f1a"/></svg></span>
                        <div>
                            <div class="comp-rule-name">Max Loss</div>
                            <div class="comp-rule-val">10% overall drawdown (Static)</div>
                        </div>
                    </div>
                    <div class="comp-rule">
                        <span class="comp-rule-icon-svg"><svg viewBox="0 0 20 20" fill="none"><rect x="3" y="3" width="14" height="14" rx="2" stroke="#ff9f1a" stroke-width="1.2"/><path d="M3 7h14" stroke="#ff9f1a" stroke-width="1.2"/><circle cx="7" cy="11" r="1" fill="#ff9f1a"/><circle cx="10" cy="11" r="1" fill="#ff9f1a"/><circle cx="13" cy="11" r="1" fill="#ff9f1a"/><circle cx="7" cy="14" r="1" fill="#ff9f1a"/><circle cx="10" cy="14" r="1" fill="#ff9f1a"/><circle cx="13" cy="14" r="1" fill="#ff9f1a" opacity="0.4"/></svg></span>
                        <div>
                            <div class="comp-rule-name">Min Trading Days</div>
                            <div class="comp-rule-val">10 days minimum</div>
                        </div>
                    </div>
                    <div class="comp-rule">
                        <span class="comp-rule-icon-svg"><svg viewBox="0 0 20 20" fill="none"><rect x="3" y="4" width="14" height="12" rx="1.5" stroke="#ff9f1a" stroke-width="1.2"/><path d="M6 4V2M14 4V2" stroke="#ff9f1a" stroke-width="1.2" stroke-linecap="round"/><path d="M7 9h6M7 12h3" stroke="#ff9f1a" stroke-width="1" stroke-linecap="round" opacity="0.6"/></svg></span>
                        <div>
                            <div class="comp-rule-name">News Trading</div>
                            <div class="comp-rule-val green">Allowed</div>
                        </div>
                    </div>
                    <div class="comp-rule">
                        <span class="comp-rule-icon-svg"><svg viewBox="0 0 20 20" fill="none"><circle cx="10" cy="8" r="3" stroke="#ff9f1a" stroke-width="1.2"/><path d="M10 11v2M7 15h6" stroke="#ff9f1a" stroke-width="1.2" stroke-linecap="round"/><path d="M5 5l-2-2M15 5l2-2" stroke="#ff9f1a" stroke-width="1" stroke-linecap="round" opacity="0.5"/></svg></span>
                        <div>
                            <div class="comp-rule-name">Overnight / Overweek</div>
                            <div class="comp-rule-val green">Allowed</div>
                        </div>
                    </div>
                    <div class="comp-rule">
                        <span class="comp-rule-icon-svg"><svg viewBox="0 0 20 20" fill="none"><rect x="4" y="3" width="12" height="14" rx="2" stroke="#ff9f1a" stroke-width="1.2"/><path d="M7 7h6M7 10h4" stroke="#ff9f1a" stroke-width="1" stroke-linecap="round" opacity="0.5"/><circle cx="13" cy="13" r="3.5" fill="var(--bg)" stroke="var(--red)" stroke-width="1.2"/><path d="M11.5 11.5l3 3M14.5 11.5l-3 3" stroke="var(--red)" stroke-width="1" stroke-linecap="round"/></svg></span>
                        <div>
                            <div class="comp-rule-name">EA / Bots</div>
                            <div class="comp-rule-val red">Not allowed</div>
                        </div>
                    </div>
                    <div class="comp-rule">
                        <span class="comp-rule-icon-svg"><svg viewBox="0 0 20 20" fill="none"><path d="M4 10h3l2-4 2 8 2-6 2 2h3" stroke="#ff9f1a" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                        <div>
                            <div class="comp-rule-name">Positions</div>
                            <div class="comp-rule-val">Max based on margin</div>
                        </div>
                    </div>
                    <div class="comp-rule">
                        <span class="comp-rule-icon-svg"><svg viewBox="0 0 20 20" fill="none"><circle cx="10" cy="8" r="4" stroke="#ff9f1a" stroke-width="1.2"/><path d="M4 17c0-3.3 2.7-6 6-6s6 2.7 6 6" stroke="#ff9f1a" stroke-width="1.2" stroke-linecap="round"/></svg></span>
                        <div>
                            <div class="comp-rule-name">Account</div>
                            <div class="comp-rule-val">One paid per person (profile/household)</div>
                        </div>
                    </div>
                    <div class="comp-rule">
                        <span class="comp-rule-icon-svg"><svg viewBox="0 0 20 20" fill="none"><path d="M4 10a6 6 0 1 1 12 0" stroke="#ff9f1a" stroke-width="1.2" stroke-linecap="round"/><path d="M7 10l3-3 3 3" stroke="#ff9f1a" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 7v7" stroke="#ff9f1a" stroke-width="1.2" stroke-linecap="round"/></svg></span>
                        <div>
                            <div class="comp-rule-name">Re-entry</div>
                            <div class="comp-rule-val">Unlimited at -50% fee</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- ═══ PRIZE POOLS ═══ -->
        <div class="comp-prizes-section scroll-reveal">
            <h2 class="comp-section-heading">Prize <span class="green">Pools</span></h2>
            <p class="comp-section-sub">Top performers will be rewarded. Full prize breakdown coming soon.</p>

            <div class="comp-prizes-grid">
                <!-- FREE PRIZES -->
                <div class="comp-prize-card">
                    <div class="comp-prize-card-header comp-prize-card-header-green">
                        <svg viewBox="0 0 24 24" fill="none" width="18" height="18"><path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 16.8l-6.2 4.5 2.4-7.4L2 9.4h7.6z" fill="#10B981"/></svg>
                        Free Competition
                    </div>
                    <div class="comp-prize-card-body">
                        <div class="comp-podium">
                            <div class="comp-podium-item">
                                <div class="comp-podium-rank comp-podium-gold">1<span>st</span></div>
                                <div class="comp-podium-reward">To be announced</div>
                            </div>
                            <div class="comp-podium-item">
                                <div class="comp-podium-rank comp-podium-silver">2<span>nd</span></div>
                                <div class="comp-podium-reward">To be announced</div>
                            </div>
                            <div class="comp-podium-item">
                                <div class="comp-podium-rank comp-podium-bronze">3<span>rd</span></div>
                                <div class="comp-podium-reward">To be announced</div>
                            </div>
                        </div>
                        <div class="comp-prize-more">+ more prizes to be revealed</div>
                    </div>
                </div>

                <!-- PAID PRIZES -->
                <div class="comp-prize-card comp-prize-card-premium">
                    <div class="comp-prize-card-header comp-prize-card-header-orange">
                        <svg viewBox="0 0 24 24" fill="none" width="18" height="18"><path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 16.8l-6.2 4.5 2.4-7.4L2 9.4h7.6z" fill="#ff9f1a"/></svg>
                        Paid Competition
                    </div>
                    <div class="comp-prize-card-body">
                        <div class="comp-podium">
                            <div class="comp-podium-item">
                                <div class="comp-podium-rank comp-podium-gold">1<span>st</span></div>
                                <div class="comp-podium-reward">To be announced</div>
                            </div>
                            <div class="comp-podium-item">
                                <div class="comp-podium-rank comp-podium-silver">2<span>nd</span></div>
                                <div class="comp-podium-reward">To be announced</div>
                            </div>
                            <div class="comp-podium-item">
                                <div class="comp-podium-rank comp-podium-bronze">3<span>rd</span></div>
                                <div class="comp-podium-reward">To be announced</div>
                            </div>
                        </div>
                        <div class="comp-prize-more">+ more prizes to be revealed</div>
                    </div>
                </div>
            </div>

            <p class="comp-prizes-note">Winners must complete <strong>KYC verification</strong> to receive their prizes. Results will be announced during the first week following each competition's end date.</p>
        </div>

    </div>
</section>

<!-- ═══ FREQUENTLY ASKED QUESTIONS ═══ -->
<section class="section" style="background:var(--bg2)">
    <div class="section-inner" style="max-width:900px">
        <h2 class="comp-section-heading scroll-reveal">Frequently Asked <span class="green">Questions</span></h2>
        <p class="comp-section-sub scroll-reveal">Everything you need to know about Doji Funding competitions.</p>

        <div class="comp-faq scroll-reveal">

            <div class="comp-faq-item">
                <button class="comp-faq-q" onclick="this.parentElement.classList.toggle('open')">
                    <span>How do I join a competition?</span>
                    <svg viewBox="0 0 20 20" fill="none" width="18" height="18"><path d="M5 8l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="comp-faq-a">
                    <p>Create a free Doji Funding account, then navigate to the Competitions page in your dashboard. For Free Competitions, simply click "Join" — no payment required. For Paid Competitions, select your entry and complete the payment process. You'll receive your competition trading credentials instantly.</p>
                </div>
            </div>

            <div class="comp-faq-item">
                <button class="comp-faq-q" onclick="this.parentElement.classList.toggle('open')">
                    <span>Is the competition free to enter?</span>
                    <svg viewBox="0 0 20 20" fill="none" width="18" height="18"><path d="M5 8l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="comp-faq-a">
                    <p>We offer two types of competitions. The <strong>Free Competition</strong> has absolutely no entry fee — it's completely free to join and compete. The <strong>Paid Competition</strong> requires an entry fee starting from $49, with a significantly larger prize pool and higher rewards.</p>
                </div>
            </div>

            <div class="comp-faq-item">
                <button class="comp-faq-q" onclick="this.parentElement.classList.toggle('open')">
                    <span>What are the competition trading rules?</span>
                    <svg viewBox="0 0 20 20" fill="none" width="18" height="18"><path d="M5 8l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="comp-faq-a">
                    <p>Both competitions have no daily loss limit, a 10% static overall drawdown, and allow news trading and overnight/overweek holding. EA and bots are not permitted. Free competitions require a minimum of 5 trading days, while paid competitions require 10. Full rules are listed above for each competition type.</p>
                </div>
            </div>

            <div class="comp-faq-item">
                <button class="comp-faq-q" onclick="this.parentElement.classList.toggle('open')">
                    <span>How is the leaderboard ranked?</span>
                    <svg viewBox="0 0 20 20" fill="none" width="18" height="18"><path d="M5 8l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="comp-faq-a">
                    <p>The leaderboard is ranked by percentage return on the simulated account. It will be live and updated 24/7 once the competition begins, and visible to all users including non-participants. Being on the leaderboard does not guarantee winning — all top traders will be reviewed for consistency and rule compliance before prizes are awarded.</p>
                </div>
            </div>

            <div class="comp-faq-item">
                <button class="comp-faq-q" onclick="this.parentElement.classList.toggle('open')">
                    <span>Do I need to complete KYC to participate?</span>
                    <svg viewBox="0 0 20 20" fill="none" width="18" height="18"><path d="M5 8l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="comp-faq-a">
                    <p>You do <strong>not</strong> need to complete KYC to join or participate in a competition. However, <strong>KYC verification is mandatory before any prize can be distributed</strong>. If you finish in a winning position, you will be asked to complete identity verification before receiving your reward. We recommend completing KYC early in your dashboard to avoid delays.</p>
                </div>
            </div>

            <div class="comp-faq-item">
                <button class="comp-faq-q" onclick="this.parentElement.classList.toggle('open')">
                    <span>Can I use Expert Advisors (EAs) or bots?</span>
                    <svg viewBox="0 0 20 20" fill="none" width="18" height="18"><path d="M5 8l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="comp-faq-a">
                    <p>No. All trades must be placed manually. Expert Advisors, bots, automated systems, and high-frequency trading strategies are strictly prohibited in both competition types. Any use of automated trading will result in immediate disqualification.</p>
                </div>
            </div>

            <div class="comp-faq-item">
                <button class="comp-faq-q" onclick="this.parentElement.classList.toggle('open')">
                    <span>Can I re-enter a competition?</span>
                    <svg viewBox="0 0 20 20" fill="none" width="18" height="18"><path d="M5 8l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="comp-faq-a">
                    <p>For the <strong>Free Competition</strong>, re-entry is unlimited — you can restart anytime. For the <strong>Paid Competition</strong>, re-entry is also unlimited but at a 50% discounted fee compared to your original entry. Only one active entry per person is allowed at any time.</p>
                </div>
            </div>

            <div class="comp-faq-item">
                <button class="comp-faq-q" onclick="this.parentElement.classList.toggle('open')">
                    <span>When are winners announced?</span>
                    <svg viewBox="0 0 20 20" fill="none" width="18" height="18"><path d="M5 8l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="comp-faq-a">
                    <p>Winners will be announced during the first week following the competition's end date. All top-ranked traders will be reviewed for consistency score and rule compliance before prizes are awarded. Results are final and binding.</p>
                </div>
            </div>

        </div>

        <p class="comp-faq-link scroll-reveal">For full competition rules and conditions, see our <a href="faq.php#competitions">FAQ & Terms</a>.</p>
    </div>
</section>

<!-- ═══ IMPORTANT INFORMATION & DISCLAIMER ═══ -->
<section class="section" style="background:var(--bg)">
    <div class="section-inner" style="max-width:900px">
        <div class="comp-disclaimer scroll-reveal">

            <h3 class="comp-disclaimer-title">
                <svg viewBox="0 0 20 20" fill="none" width="18" height="18"><path d="M10 2l8 14H2L10 2z" fill="rgba(255,159,26,0.15)" stroke="#ff9f1a" stroke-width="1.2" stroke-linejoin="round"/><text x="10" y="13" text-anchor="middle" fill="#ff9f1a" font-size="9" font-weight="700" font-family="Inter">!</text></svg>
                You Must Know
            </h3>

            <div class="comp-disclaimer-section">
                <h4>Leaderboard Updates</h4>
                <p>The competition leaderboard will be live and updated 24/7 once the competition begins. It will be visible to all, including non-participants. Being on the leaderboard does not guarantee winning prizes. All top traders will be reviewed for consistency score and rule compliance before being awarded.</p>
            </div>

            <div class="comp-disclaimer-section">
                <h4>KYC Verification</h4>
                <p>Winners must complete KYC (Know Your Customer) identity verification in order to receive their prize. KYC is not required to participate, but must be completed before any prize distribution. We recommend completing KYC in your dashboard as early as possible to avoid delays.</p>
            </div>

            <div class="comp-disclaimer-section">
                <h4>Legal Notes</h4>
                <p>Doji Funding reserves the right to disqualify any participant for rule violations. Doji Funding may terminate or amend any competition at any time without prior notice. Doji Funding has the right to exclude participants at its sole discretion. Competition is void where prohibited by law. Doji Funding is not responsible for technical issues, submission failures, or other interruptions. By participating, users consent to performance data being used for marketing and promotional purposes.</p>
            </div>

            <div class="comp-disclaimer-section">
                <h4>Disclaimer</h4>
                <p>Doji Funding reserves the right to investigate and monitor all competition activity to ensure fairness and integrity. Any form of suspicious behavior, misconduct, or violation of the rules may result in immediate disqualification at the sole discretion of the organizers. All decisions made by Doji Funding regarding eligibility, disqualification, and prize distribution are final and binding.</p>
            </div>

        </div>
    </div>
</section>

<!-- ═══ CTA ═══ -->
<section class="section" style="background:var(--bg2)">
    <div class="section-inner" style="max-width:900px">
        <div class="comp-notify scroll-reveal">
            <p class="comp-notify-title">🔔 Be the first to know when competitions launch</p>
            <p class="comp-notify-sub">Create your account now and you'll be notified as soon as competitions go live.</p>
            <?php if (!isLoggedIn()): ?>
            <button class="btn-primary comp-notify-btn" onclick="AuthModal.open('signup')">Create Account</button>
            <?php else: ?>
            <div class="comp-notify-ok"><?= icon('check', 14) ?> You'll be notified when competitions are available</div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/community.php'; ?>
