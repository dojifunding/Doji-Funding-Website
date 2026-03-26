/**
 * Doji Funding — Challenge Configurator
 * 
 * Complete pricing calculator with:
 * - Custom styled sliders
 * - Toggle groups (loss type, platform, payout)
 * - Checkbox options (overnight, overweek)
 * - Real-time price calculation
 * - Promo code system
 * - Share configuration URL
 * 
 * Reads pricing data from window.DOJI_CONFIG.pricing
 */

const Configurator = (function() {
    'use strict';

    // ─── Load data from PHP config ───
    const data = window.DOJI_CONFIG?.pricing || {};
    const accountSizes = data.accountSizes || [];
    const basePricesData = data.basePrices || {};
    const promoCodes = data.promoCodes || {};

    // ─── State ───
    const S = {
        tab: 'onestep',
        sizeIdx: 9,
        target: 10, target1: 8, target2: 5,
        daily: 5, max: 8,
        split: 80, days: 5, consistency: 30,
        dailyType: 'intraday', maxType: 'intraday',
        platform: 'mt5', payout: 'monthly',
        overnight: false, overweek: false,
        activePromo: null,
    };

    // ─── Loss type hints ───
    const lossHints = {
        daily: {
            intraday: 'Cheapest — Real-time balance tracking',
            eod: 'Standard — Calculated at end of day',
            static: 'Premium — Fixed at start of day balance',
        },
        max: {
            intraday: 'Cheapest — Real-time equity tracking',
            eod: 'Standard — Evaluated at end of day',
            static: 'Premium — Based on initial balance',
        },
    };

    // ─── Dollar value helper ───
    // IDs that represent a % of account size
    const pctIds = ['target', 'target1', 'target2', 'daily', 'max'];

    function dollarStr(pct) {
        const size = accountSizes[S.sizeIdx];
        const val = Math.round(size * pct / 100);
        return ' ($' + val.toLocaleString() + ')';
    }

    function pctDisplay(id, val) {
        if (pctIds.includes(id)) return val + '%' + dollarStr(val);
        return val + '%';
    }

    // ═══════════════════════
    //  UI BUILDER FUNCTIONS
    // ═══════════════════════

    function makeSlider(id, label, min, max, step, val, suffix, tooltip, ticks) {
        const pct = ((val - min) / (max - min)) * 100;
        const displayVal = (suffix === '%' && pctIds.includes(id)) ? pctDisplay(id, val) : val + suffix;
        let tipHtml = '';
        if (tooltip) {
            tipHtml = `<span class="tip-wrap"><span class="tip-icon">?</span><div class="tip-popup">${tooltip}</div></span>`;
        }
        let ticksHtml = '';
        if (ticks) {
            ticksHtml = `<div class="slider-ticks">${ticks.map(t => `<span>${t}</span>`).join('')}</div>`;
        }
        return `<div class="slider-wrap" data-slider-id="${id}">
            <div class="slider-header">
                <span class="slider-label">${label}${tipHtml}</span>
                <span class="slider-val">${displayVal}</span>
            </div>
            <div class="slider-track">
                <div class="slider-bg"></div>
                <div class="slider-fill" style="width:${pct}%"></div>
                <div class="slider-thumb" style="left:${pct}%"></div>
                <input class="slider-input" type="range" min="${min}" max="${max}" step="${step}" value="${val}"
                       oninput="Configurator.onSliderLive('${id}',this)"
                       onchange="Configurator.onSliderDone('${id}',this.value)">
            </div>
            ${ticksHtml}
        </div>`;
    }

    function makeToggle(id, options, active, small) {
        const cls = small ? 'toggle-group toggle-sm' : 'toggle-group';
        return `<div class="${cls}">${options.map(o =>
            `<button class="toggle-btn${o.id === active ? ' active' : ''}" onclick="Configurator.onToggle('${id}','${o.id}')">${o.label}</button>`
        ).join('')}</div>`;
    }

    function makeSwitch(id, checked, label, disabled, subtext, icon) {
        const cls = `switch-row${checked ? ' active' : ''}${disabled ? ' disabled' : ''}`;
        const iconSvg = icon === 'moon'
            ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>'
            : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>';
        return `<div class="${cls}" onclick="${disabled ? '' : `Configurator.onCheck('${id}',!this.classList.contains('active'))`}">
            <div class="switch-left">
                <span class="switch-icon">${iconSvg}</span>
                <div class="switch-text">
                    <span class="switch-label">${label}</span>
                    ${subtext ? `<span class="switch-sub">${subtext}</span>` : ''}
                </div>
            </div>
            <div class="switch-toggle${checked ? ' on' : ''}">
                <div class="switch-knob"></div>
            </div>
        </div>`;
    }

    // ═══════════════════════
    //  BUILD SLIDERS
    // ═══════════════════════

    function buildSliders() {
        const c = document.getElementById('slidersContainer');
        if (!c) return;

        const t = S.tab;
        const sizeVal = '$' + accountSizes[S.sizeIdx].toLocaleString();
        let html = '';

        // Account Size
        html += makeSlider('sizeIdx', 'Account Size', 0, 19, 1, S.sizeIdx, '', '', ['$5K', '$50K', '$100K']);

        // Profit Target(s)
        if (t === 'onestep') {
            html += makeSlider('target', 'Profit Target', 5, 15, 1, S.target, '%');
        } else {
            html += makeSlider('target1', 'Profit Target Phase 1', 5, 12, 1, S.target1, '%');
            html += makeSlider('target2', 'Profit Target Phase 2', 3, 8, 1, S.target2, '%');
        }

        // Daily Loss + type
        html += makeSlider('daily', 'Daily Loss', t === 'onestep' ? 2 : 3, 8, 1, S.daily, '%');
        html += `<div style="margin-top:-8px;margin-bottom:14px">
            <div class="loss-type-label">Loss Calculation Type
                <span class="tip-wrap"><span class="tip-icon">?</span><div class="tip-popup">Intraday: Cheapest — loss tracked in real-time against floating equity. End of Day: Standard — loss calculated at session close only. Static: Premium — loss measured against your starting balance, never resets.</div></span>
            </div>
            ${makeToggle('dailyType', [{id:'intraday',label:'Intraday'},{id:'eod',label:'End of Day'},{id:'static',label:'Static'}], S.dailyType, true)}
            <div class="loss-hint">${lossHints.daily[S.dailyType]}</div>
        </div>`;

        // Max Loss + type
        html += makeSlider('max', 'Max Loss', t === 'onestep' ? 4 : 6, t === 'onestep' ? 12 : 15, 1, S.max, '%');
        html += `<div style="margin-top:-8px;margin-bottom:14px">
            <div class="loss-type-label">Loss Calculation Type
                <span class="tip-wrap"><span class="tip-icon">?</span><div class="tip-popup">Intraday: Cheapest — max drawdown tracked in real-time. End of Day: Standard — evaluated at end of trading session. Static: Premium — drawdown measured from initial balance, most protective.</div></span>
            </div>
            ${makeToggle('maxType', [{id:'intraday',label:'Intraday'},{id:'eod',label:'End of Day'},{id:'static',label:'Static'}], S.maxType, true)}
            <div class="loss-hint">${lossHints.max[S.maxType]}</div>
        </div>`;

        // Split
        html += makeSlider('split', 'Profit Split', t === 'onestep' ? 50 : 70, 90, 5, S.split, '%');

        // Min Days
        html += makeSlider('days', 'Min Trading Days', t === 'onestep' ? 3 : 5, t === 'onestep' ? 10 : 20, 1, S.days, '',
            'Minimum number of active trading days before evaluation validation. Overweek Holding enforces min 5 days. Two Step: days split 60/40 between phases.');

        // Consistency
        html += makeSlider('consistency', 'Consistency Rule', 10, S.payout === 'weekly' ? 30 : 50, 10, S.consistency, '%',
            "No single day's profit can exceed this % of total profit. Prevents lucky trading. Weekly payout caps at 30%.");

        // Platform
        html += `<div style="margin-bottom:14px">
            <div class="slider-header"><span class="slider-label">Trading Platform</span><span class="slider-val">${S.platform === 'mt5' ? 'MetaTrader 5' : 'cTrader'}</span></div>
            ${makeToggle('platform', [{id:'mt5',label:'MetaTrader 5'},{id:'ctrader',label:'cTrader'}], S.platform)}
        </div>`;

        // Payout Frequency
        html += `<div style="margin-bottom:14px">
            <div class="slider-header"><span class="slider-label">Payout Frequency</span><span class="slider-val">${S.payout === 'monthly' ? 'Monthly' : S.payout === 'biweekly' ? 'Bi-Weekly' : 'Weekly'}</span></div>
            ${makeToggle('payout', [{id:'monthly',label:'Monthly'},{id:'biweekly',label:'Bi-Weekly (+$29)'},{id:'weekly',label:'Weekly (+$59)'}], S.payout)}
        </div>`;

        // Holding options (toggle switches)
        const onPrice = t === 'onestep' ? 19 : 25;
        const owPrice = t === 'onestep' ? 29 : 39;
        html += `<div class="switch-section">
            <div class="switch-section-label">Holding Options</div>
            ${makeSwitch('overnight', !S.overweek && S.overnight, `Overnight Holding <span class="switch-price">+$${onPrice}</span>`, S.overweek, S.overweek ? 'Included with Overweek Holding' : 'Hold positions through the night', 'moon')}
            ${makeSwitch('overweek', S.overweek, `Overweek Holding <span class="switch-price">+$${owPrice}</span>`, false, 'Hold through weekends · includes overnight', 'calendar')}
        </div>`;

        // Equal loss warning
        if (S.daily === S.max) {
            html += '<div class="warning-box"><strong>⚠ Daily Loss = Max Loss:</strong> This removes the daily loss safety net. A surcharge of +$100 is applied.</div>';
        }

        c.innerHTML = html;

        // Fix account size display (override generic suffix)
        const firstVal = c.querySelector('.slider-val');
        if (firstVal) firstVal.textContent = sizeVal;
    }

    // ═══════════════════════
    //  PRICING CALCULATION
    // ═══════════════════════

    function calculatePrice() {
        const t = S.tab;
        const size = accountSizes[S.sizeIdx];
        const bp = (basePricesData[t] || {})[size] || 249;

        let diff = 0, spAdj = 0, optAdj = 0, ltAdj = 0;

        if (t === 'onestep') {
            if (S.target < 10) diff += (10 - S.target) * 20;
            if (S.target > 12) diff -= (S.target - 12) * 15;
            if (S.daily > 5) diff += (S.daily - 5) * 60;
            if (S.daily < 4) diff -= (4 - S.daily) * 30;
            if (S.max > 8) diff += (S.max - 8) * 50;
            if (S.max < 6) diff -= (6 - S.max) * 25;
            if (S.days < 5) diff += (5 - S.days) * 15;
            if (S.consistency < 40) diff -= (40 - S.consistency) * 2;
            if (S.consistency > 40) diff += (S.consistency - 40) * 2;
            spAdj = (S.split - 70) * 4;
            if (S.overnight) optAdj += 19;
            if (S.overweek) optAdj += 29;
            if (S.dailyType === 'intraday') ltAdj -= 15;
            else if (S.dailyType === 'static') ltAdj += 25;
            if (S.maxType === 'intraday') ltAdj -= 15;
            else if (S.maxType === 'static') ltAdj += 25;
        } else {
            const tt = S.target1 + S.target2;
            if (tt < 10) diff += (10 - tt) * 15;
            if (tt > 12) diff -= (tt - 12) * 10;
            if (S.daily > 5) diff += (S.daily - 5) * 50;
            if (S.daily < 5) diff -= (5 - S.daily) * 25;
            if (S.max > 10) diff += (S.max - 10) * 45;
            if (S.max < 10) diff -= (10 - S.max) * 20;
            if (S.days < 10) diff += (10 - S.days) * 10;
            if (S.consistency < 45) diff -= (45 - S.consistency) * 3;
            if (S.consistency > 45) diff += (S.consistency - 45) * 3;
            spAdj = (S.split - 80) * 6;
            if (S.overnight) optAdj += 25;
            if (S.overweek) optAdj += 39;
            if (S.dailyType === 'intraday') ltAdj -= 19;
            else if (S.dailyType === 'static') ltAdj += 29;
            if (S.maxType === 'intraday') ltAdj -= 19;
            else if (S.maxType === 'static') ltAdj += 29;
        }

        const payAdj = S.payout === 'biweekly' ? 29 : S.payout === 'weekly' ? 59 : 0;
        const eqSur = S.daily === S.max ? 100 : 0;
        const raw = bp + diff + spAdj + optAdj + ltAdj + eqSur + payAdj;
        const total = Math.max(raw, Math.round(bp * 0.5));

        let final = total;
        if (S.activePromo) {
            const disc = S.activePromo.type === 'percent'
                ? total * S.activePromo.value / 100
                : S.activePromo.value;
            final = Math.max(Math.round(total - disc), Math.round(bp * 0.3));
        }

        return { basePrice: bp, total, final, display: S.activePromo ? final : total };
    }

    // ═══════════════════════
    //  UPDATE UI
    // ═══════════════════════

    function updateUI() {
        const t = S.tab;
        const size = accountSizes[S.sizeIdx];
        const price = calculatePrice();

        // Dollar value helper
        function dv(pct) {
            return '$' + Math.round(size * pct / 100).toLocaleString();
        }

        // Render summary with Evaluation / Funded tabs
        const grid = document.getElementById('summaryGrid');
        if (grid) {
            let html = '';

            // ── Account header card ──
            const typeLabel = t === 'onestep' ? '1 Step — Fast Track' : '2 Step — Classic';
            const platLabel = S.platform === 'mt5' ? 'MetaTrader 5' : 'cTrader';
            html += `<div class="summary-account">
                <div class="summary-account-size">$${size.toLocaleString()}</div>
                <div class="summary-account-info">
                    <span class="val-accent">${typeLabel}</span>
                    <span>${platLabel}</span>
                </div>
            </div>`;

            // ── Phase tabs: Evaluation / Funded ──
            const activeTab = grid.dataset.activeTab || 'evaluation';
            html += `<div class="summary-phase-tabs">
                <button class="summary-phase-tab${activeTab === 'evaluation' ? ' active' : ''}" data-phase="evaluation">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    Evaluation
                </button>
                <button class="summary-phase-tab${activeTab === 'funded' ? ' active' : ''}" data-phase="funded">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Funded
                </button>
            </div>`;

            // ── EVALUATION PANEL ──
            html += `<div class="summary-phase-panel${activeTab === 'evaluation' ? ' active' : ''}" data-panel="evaluation">`;

            // Target + Risk metric cards
            if (t === 'onestep') {
                html += `<div class="summary-cards cols-3">
                    <div class="summary-card">
                        <div class="summary-card-label">Target</div>
                        <div class="summary-card-pct">${S.target}%</div>
                        <div class="summary-card-dollar">${dv(S.target)}</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-card-label">Daily Loss</div>
                        <div class="summary-card-pct">${S.daily}%</div>
                        <div class="summary-card-dollar">${dv(S.daily)}</div>
                        <div class="summary-card-tag">${S.dailyType}</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-card-label">Max Loss</div>
                        <div class="summary-card-pct">${S.max}%</div>
                        <div class="summary-card-dollar">${dv(S.max)}</div>
                        <div class="summary-card-tag">${S.maxType}</div>
                    </div>
                </div>`;
            } else {
                html += `<div class="summary-cards cols-2">
                    <div class="summary-card">
                        <div class="summary-card-label">Phase 1 Target</div>
                        <div class="summary-card-pct">${S.target1}%</div>
                        <div class="summary-card-dollar">${dv(S.target1)}</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-card-label">Phase 2 Target</div>
                        <div class="summary-card-pct">${S.target2}%</div>
                        <div class="summary-card-dollar">${dv(S.target2)}</div>
                    </div>
                </div>`;
                html += `<div class="summary-cards cols-2">
                    <div class="summary-card">
                        <div class="summary-card-label">Daily Loss</div>
                        <div class="summary-card-pct">${S.daily}%</div>
                        <div class="summary-card-dollar">${dv(S.daily)}</div>
                        <div class="summary-card-tag">${S.dailyType}</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-card-label">Max Loss</div>
                        <div class="summary-card-pct">${S.max}%</div>
                        <div class="summary-card-dollar">${dv(S.max)}</div>
                        <div class="summary-card-tag">${S.maxType}</div>
                    </div>
                </div>`;
            }

            // Evaluation detail rows
            const evalDetails = [
                ['Min Trading Days', S.days + ' days'],
                ['Time Limit', 'None'],
                ['News Trading', 'Allowed'],
            ];
            if (S.overnight || S.overweek) {
                evalDetails.push(['Add-Ons', S.overweek ? 'Overweek + Overnight' : 'Overnight']);
            }
            html += `<div class="summary-details">
                ${evalDetails.map(([l, v]) =>
                    `<div class="summary-detail-row"><span class="lbl">${l}</span><span class="val">${v}</span></div>`
                ).join('')}
            </div>`;
            html += `</div>`; // end evaluation panel

            // ── FUNDED PANEL ──
            html += `<div class="summary-phase-panel${activeTab === 'funded' ? ' active' : ''}" data-panel="funded">`;

            // Funded metric cards
            html += `<div class="summary-cards cols-3">
                <div class="summary-card">
                    <div class="summary-card-label">Profit Split</div>
                    <div class="summary-card-pct">${S.split}%</div>
                    <div class="summary-card-dollar">Up to 90%</div>
                </div>
                <div class="summary-card">
                    <div class="summary-card-label">Daily Loss</div>
                    <div class="summary-card-pct">${S.daily}%</div>
                    <div class="summary-card-dollar">${dv(S.daily)}</div>
                    <div class="summary-card-tag">${S.dailyType}</div>
                </div>
                <div class="summary-card">
                    <div class="summary-card-label">Max Loss</div>
                    <div class="summary-card-pct">${S.max}%</div>
                    <div class="summary-card-dollar">${dv(S.max)}</div>
                    <div class="summary-card-tag">${S.maxType}</div>
                </div>
            </div>`;

            // Funded detail rows
            const payoutLabel = S.payout === 'monthly' ? 'Monthly' : S.payout === 'biweekly' ? 'Bi-Weekly' : 'Weekly';
            const fundedDetails = [
                ['Payout Frequency', payoutLabel],
                ['Payout Speed', '24h Guaranteed'],
                ['Consistency Rule', S.consistency + '%'],
                ['Scaling', 'Eligible'],
                ['Doji Coins™', 'Earn on every trade'],
            ];
            html += `<div class="summary-details">
                ${fundedDetails.map(([l, v]) =>
                    `<div class="summary-detail-row"><span class="lbl">${l}</span><span class="val">${v}</span></div>`
                ).join('')}
            </div>`;
            html += `</div>`; // end funded panel

            grid.innerHTML = html;

            // ── Attach tab click listeners ──
            grid.querySelectorAll('.summary-phase-tab').forEach(function(tab) {
                tab.addEventListener('click', function() {
                    const phase = this.dataset.phase;
                    grid.dataset.activeTab = phase;
                    grid.querySelectorAll('.summary-phase-tab').forEach(function(t) { t.classList.remove('active'); });
                    grid.querySelectorAll('.summary-phase-panel').forEach(function(p) { p.classList.remove('active'); });
                    this.classList.add('active');
                    grid.querySelector('[data-panel="' + phase + '"]').classList.add('active');
                });
            });
        }

        // Render price
        const pd = document.getElementById('priceDisplay');
        if (pd) {
            if (S.activePromo) {
                pd.innerHTML = `<div class="price-promo">
                    <span class="price-original">$${price.total}</span>
                    <span class="price-val">$${price.final}</span>
                    <span class="price-badge">-${S.activePromo.type === 'percent' ? S.activePromo.value + '%' : '$' + S.activePromo.value}</span>
                </div>`;
            } else {
                pd.innerHTML = `<div class="price-val">$${price.total}</div>`;
            }
        }

        // Purchase button
        const btn = document.getElementById('purchaseBtn');
        if (btn) btn.textContent = 'Purchase Challenge — $' + price.display;

        // Note
        const note = document.getElementById('noteBox');
        if (note) {
            let txt = '<strong style="color:var(--green)">Note:</strong> Consistency Rule and Payout Frequency apply to Funded accounts only.';
            if (t === 'twostep') txt += ' Days split 60/40 between Phase 1 and 2.';
            if (S.payout === 'weekly') txt += ' Weekly payout limits consistency to max 30%.';
            note.innerHTML = txt;
        }

        // Update objectives table
        updateObjectives();
    }

    // ═══════════════════════
    //  TRADING OBJECTIVES
    // ═══════════════════════

    function updateObjectives() {
        if (!document.getElementById('objectivesSection')) return;

        const t = S.tab;
        const size = accountSizes[S.sizeIdx];
        const is2 = t === 'twostep';
        function dv(pct) { return '$' + Math.round(size * pct / 100).toLocaleString(); }

        // Eval card number + title
        const evalNum = document.getElementById('objEvalNum');
        const evalTitle = document.getElementById('objEvalTitle');
        const fundedNum = document.getElementById('objFundedNum');
        if (evalNum) evalNum.textContent = is2 ? '1' : '1';
        if (evalTitle) evalTitle.textContent = is2 ? 'The Evaluation Stages' : 'The Evaluation Stage';
        if (fundedNum) fundedNum.textContent = is2 ? '3' : '2';

        // Target box
        const targetBox = document.getElementById('objTargetBox');
        if (targetBox) {
            const p1Days = is2 ? Math.ceil(S.days * 0.6) : S.days;
            const p2Days = is2 ? Math.floor(S.days * 0.4) : 0;
            let html = '';
            if (is2) {
                html += `<div class="obj-target-header"><span class="obj-target-phase">Phase 1</span><span class="obj-target-step">Step 1</span></div>
                <div class="obj-target-grid">
                    <div><div class="obj-target-item-label">⊙ TARGET</div><div class="obj-target-item-val">${S.target1}%</div></div>
                    <div><div class="obj-target-item-label">📅 MIN TRADING DAYS</div><div class="obj-target-item-val">${p1Days} days</div></div>
                </div>
                <div class="obj-target-meta"><span>⟳ Leverage: <strong>1:30</strong></span><span>∞ Trading Period: <strong>Unlimited</strong></span></div>`;
                html += `<div style="height:16px;border-top:1px solid var(--border);margin-top:16px"></div>`;
                html += `<div class="obj-target-header"><span class="obj-target-phase">Phase 2</span><span class="obj-target-step">Step 2</span></div>
                <div class="obj-target-grid">
                    <div><div class="obj-target-item-label">⊙ TARGET</div><div class="obj-target-item-val">${S.target2}%</div></div>
                    <div><div class="obj-target-item-label">📅 MIN TRADING DAYS</div><div class="obj-target-item-val">${p2Days} days</div></div>
                </div>
                <div class="obj-target-meta"><span>⟳ Leverage: <strong>1:30</strong></span><span>∞ Trading Period: <strong>Unlimited</strong></span></div>`;
            } else {
                html += `<div class="obj-target-header"><span class="obj-target-phase">Evaluation Phase</span><span class="obj-target-step">Step 1</span></div>
                <div class="obj-target-grid">
                    <div><div class="obj-target-item-label">⊙ TARGET</div><div class="obj-target-item-val">${S.target}%</div></div>
                    <div><div class="obj-target-item-label">📅 MIN TRADING DAYS</div><div class="obj-target-item-val">${p1Days} days</div></div>
                </div>
                <div class="obj-target-meta"><span>⟳ Leverage: <strong>1:30</strong></span><span>∞ Trading Period: <strong>Unlimited</strong></span></div>`;
            }
            targetBox.innerHTML = html;
        }

        // Eval limits
        const dailyEl = document.getElementById('objDailyVal');
        const maxEl = document.getElementById('objMaxVal');
        if (dailyEl) dailyEl.textContent = S.daily + '%';
        if (maxEl) maxEl.textContent = S.max + '%';

        // Funded limits
        const fdEl = document.getElementById('objFDailyVal');
        const fmEl = document.getElementById('objFMaxVal');
        const fcEl = document.getElementById('objFConsVal');
        if (fdEl) fdEl.textContent = S.daily + '%';
        if (fmEl) fmEl.textContent = S.max + '%';
        if (fcEl) fcEl.textContent = S.consistency + '%';

        // Eval Guidelines
        const guidelines = document.getElementById('objGuidelines');
        if (guidelines) {
            const newsAllowed = is2 ? true : S.target > 8;
            const weekendAllowed = is2 ? true : (S.days >= 5 && (S.overnight || S.overweek));
            const overnightStatus = S.overnight || S.overweek;

            guidelines.innerHTML = `
                <div class="obj-guideline-card">
                    <div class="obj-guideline-header">
                        <span class="obj-guideline-name">📰 News Trading</span>
                        <span class="obj-guideline-badge ${newsAllowed ? 'allowed' : 'restricted'}">${newsAllowed ? 'ALLOWED' : 'RESTRICTED'}</span>
                    </div>
                    <div class="obj-guideline-desc">${newsAllowed ? 'You are allowed to trade during high-impact news events.' : 'Not allowed 5 min before/after high-impact news. Set target > 8% to unlock.'}</div>
                </div>
                <div class="obj-guideline-card">
                    <div class="obj-guideline-header">
                        <span class="obj-guideline-name">🌙 Overnight & Weekend</span>
                        <span class="obj-guideline-badge ${overnightStatus ? 'allowed' : 'restricted'}">${overnightStatus ? 'ALLOWED' : 'NOT INCLUDED'}</span>
                    </div>
                    <div class="obj-guideline-desc">${overnightStatus ? 'You can hold trades overnight' + (S.overweek ? ' and over the weekend.' : '.') : 'Enable Overnight/Overweek add-ons in the configurator to unlock.'}</div>
                </div>
                <div class="obj-guideline-card">
                    <div class="obj-guideline-header">
                        <span class="obj-guideline-name">⏱ Inactivity</span>
                        <span class="obj-guideline-badge value">30 DAYS</span>
                    </div>
                    <div class="obj-guideline-desc">Close at least 1 trade every 30 days.</div>
                </div>
                <div class="obj-guideline-card">
                    <div class="obj-guideline-header">
                        <span class="obj-guideline-name">⟳ Leverage</span>
                        <span class="obj-guideline-badge value">1:30</span>
                    </div>
                    <div class="obj-guideline-desc">FX 1:30, Indices 1:5, Metals 1:10, Energies 1:10, Crypto 1:1</div>
                </div>`;
        }

        // Tags
        const tags = document.getElementById('objTags');
        if (tags) {
            const newsOk = is2 || S.target > 8;
            const platform = S.platform === 'mt5' ? 'MetaTrader 5' : 'cTrader';
            tags.innerHTML = `
                <div class="obj-tag-pill"><span class="dot green"></span> ${newsOk ? 'News Trading · Allowed' : 'News Trading · Restricted'}</div>
                <div class="obj-tag-pill"><span class="dot ${S.overnight || S.overweek ? 'green' : 'blue'}"></span> ${S.overnight || S.overweek ? 'Overnight' + (S.overweek ? ' & Weekend' : '') + ' · Allowed' : 'Overnight · Not included'}</div>
                <div class="obj-tag-pill"><span class="dot blue"></span> No Time Limit</div>
                <div class="obj-tag-pill"><span class="dot green"></span> 0.01 Lots for Min Days</div>
                <div class="obj-tag-pill"><span class="dot green"></span> Platform: ${platform}</div>`;
        }

        // Flow bar
        const flow = document.getElementById('objFlow');
        if (flow) {
            if (is2) {
                flow.innerHTML = `<span class="obj-flow-step active"><span class="flow-check">✓</span> PASSED</span><span class="obj-flow-sep">›</span>
                    <span class="obj-flow-step">KYC</span><span class="obj-flow-sep">›</span>
                    <span class="obj-flow-step">CONTRACT</span><span class="obj-flow-sep">›</span>
                    <span class="obj-flow-step" style="color:var(--green)">DOJI FUNDED</span>`;
            } else {
                flow.innerHTML = `<span class="obj-flow-step active"><span class="flow-check">✓</span> PASSED</span><span class="obj-flow-sep">›</span>
                    <span class="obj-flow-step">KYC</span><span class="obj-flow-sep">›</span>
                    <span class="obj-flow-step">CONTRACT</span><span class="obj-flow-sep">›</span>
                    <span class="obj-flow-step" style="color:var(--green)">DOJI FUNDED</span>`;
            }
        }

        // Funded Guidelines
        const fGuidelines = document.getElementById('objFGuidelines');
        if (fGuidelines) {
            fGuidelines.innerHTML = `
                <div class="obj-guideline-card">
                    <div class="obj-guideline-header">
                        <span class="obj-guideline-name">📰 News Trading</span>
                        <span class="obj-guideline-badge restricted">RESTRICTED</span>
                    </div>
                    <div class="obj-guideline-desc">Not allowed to open/close a position 5 min before/after high-impact news on affected currencies.</div>
                </div>
                <div class="obj-guideline-card">
                    <div class="obj-guideline-header">
                        <span class="obj-guideline-name">🌙 Overnight & Weekend</span>
                        <span class="obj-guideline-badge allowed">ALLOWED</span>
                    </div>
                    <div class="obj-guideline-desc">You are allowed to hold your trades overnight and over the weekend.</div>
                </div>
                <div class="obj-guideline-card">
                    <div class="obj-guideline-header">
                        <span class="obj-guideline-name">⏱ Inactivity</span>
                        <span class="obj-guideline-badge value">30 DAYS</span>
                    </div>
                    <div class="obj-guideline-desc">Close at least 1 trade every 30 days.</div>
                </div>
                <div class="obj-guideline-card">
                    <div class="obj-guideline-header">
                        <span class="obj-guideline-name">⟳ Leverage</span>
                        <span class="obj-guideline-badge value">1:30</span>
                    </div>
                    <div class="obj-guideline-desc">FX 1:30, Indices 1:5, Metals 1:10, Energies 1:10, Crypto 1:1</div>
                </div>`;
        }

        // Rewards
        const rewards = document.getElementById('objRewards');
        if (rewards) {
            const payoutLabel = S.payout === 'monthly' ? 'MONTHLY' : S.payout === 'biweekly' ? 'BI-WEEKLY' : 'WEEKLY';
            const payoutDesc = S.payout === 'monthly' ? 'Request every 30 days' : S.payout === 'biweekly' ? 'Request every 14 days' : 'Request every 7 days';
            const minPay = is2 ? '1%' : '2%';
            rewards.innerHTML = `
                <div class="obj-reward-card">
                    <div class="obj-reward-label">${payoutLabel}</div>
                    <div class="obj-reward-val">${S.split}%</div>
                    <div class="obj-reward-sub">Reward Split</div>
                    <div style="margin-top:8px;font-size:11px;color:var(--text3)">${payoutDesc}<br>Minimum Reward: <strong>${minPay}</strong></div>
                </div>
                <div class="obj-reward-card">
                    <div class="obj-reward-label">SCALING</div>
                    <div class="obj-reward-val">${is2 ? '10×' : '5×'}</div>
                    <div class="obj-reward-sub">Max Scaling</div>
                    <div style="margin-top:8px;font-size:11px;color:var(--text3)">Grow to $${(size * (is2 ? 10 : 5)).toLocaleString()}</div>
                </div>
                <div class="obj-reward-card">
                    <div class="obj-reward-label">STARTING CAPITAL</div>
                    <div class="obj-reward-val">$${size.toLocaleString()}</div>
                    <div class="obj-reward-sub">Account Size</div>
                    <div style="margin-top:8px;font-size:11px;color:var(--text3)">Simulated funded account</div>
                </div>`;
        }

        // Draw charts
        drawObjCharts();
    }

    // Mini-chart SVGs
    function drawObjCharts() {
        const size = accountSizes[S.sizeIdx];
        const dailyAmt = Math.round(size * S.daily / 100);
        const maxAmt = Math.round(size * S.max / 100);

        // Daily loss chart
        ['objChartDailySvg', 'objChartFdailySvg'].forEach(function(id) {
            const svg = document.getElementById(id);
            if (!svg) return;
            svg.innerHTML = `
                <rect x="0" y="0" width="280" height="120" fill="none"/>
                <text x="10" y="15" fill="var(--text3)" font-size="9" font-family="Inter">+2%</text>
                <text x="10" y="55" fill="var(--text3)" font-size="9" font-family="Inter">0%</text>
                <text x="10" y="95" fill="var(--text3)" font-size="9" font-family="Inter">-${S.daily}%</text>
                <line x1="35" y1="50" x2="270" y2="50" stroke="rgba(255,255,255,0.06)" stroke-width="0.5"/>
                <line x1="35" y1="90" x2="270" y2="90" stroke="var(--red)" stroke-width="1" stroke-dasharray="4 3" opacity="0.5"/>
                <text x="272" y="93" fill="var(--red)" font-size="8" opacity="0.6">BREACH</text>
                <rect x="50" y="30" width="22" height="20" rx="2" fill="rgba(16,185,129,0.3)"/>
                <rect x="82" y="35" width="22" height="15" rx="2" fill="rgba(16,185,129,0.3)"/>
                <rect x="114" y="25" width="22" height="25" rx="2" fill="rgba(16,185,129,0.3)"/>
                <rect x="146" y="50" width="22" height="35" rx="2" fill="rgba(255,59,59,0.4)" stroke="var(--red)" stroke-width="0.5"/>
                <text x="150" y="68" fill="var(--red)" font-size="7" font-weight="700">BREACH</text>
                <rect x="178" y="33" width="22" height="17" rx="2" fill="rgba(16,185,129,0.3)"/>
                <rect x="210" y="28" width="22" height="22" rx="2" fill="rgba(16,185,129,0.3)"/>
                <text x="60" y="108" fill="var(--text3)" font-size="8">Mon</text>
                <text x="92" y="108" fill="var(--text3)" font-size="8">Tue</text>
                <text x="120" y="108" fill="var(--text3)" font-size="8">Wed</text>
                <text x="152" y="108" fill="var(--red)" font-size="8" font-weight="600">Thu</text>
                <text x="188" y="108" fill="var(--text3)" font-size="8">Fri</text>
            `;
        });

        // Max loss chart
        ['objChartMaxSvg', 'objChartFmaxSvg'].forEach(function(id) {
            const svg = document.getElementById(id);
            if (!svg) return;
            const floorY = 85;
            svg.innerHTML = `
                <rect x="0" y="0" width="280" height="120" fill="none"/>
                <text x="5" y="15" fill="var(--text3)" font-size="8">$${(size*1.05/1000).toFixed(1)}K</text>
                <text x="5" y="40" fill="var(--text3)" font-size="8">$${(size/1000).toFixed(0)}K</text>
                <text x="5" y="${floorY+3}" fill="var(--red)" font-size="8">$${((size-maxAmt)/1000).toFixed(1)}K</text>
                <line x1="40" y1="${floorY}" x2="270" y2="${floorY}" stroke="var(--red)" stroke-width="1" stroke-dasharray="4 3" opacity="0.6"/>
                <text x="272" y="${floorY+3}" fill="var(--red)" font-size="7" opacity="0.6">FLOOR</text>
                <path d="M45,35 C70,30 90,25 110,20 C130,15 150,30 170,55 C180,70 185,${floorY-5} 190,${floorY-2}" stroke="var(--green)" stroke-width="2" fill="none" stroke-linecap="round"/>
                <circle cx="190" cy="${floorY-2}" r="3" fill="var(--red)"/>
                <text x="170" y="${floorY-12}" fill="var(--orange)" font-size="8" font-weight="600">NEAR BREACH</text>
                <path d="M190,${floorY-2} C200,${floorY-15} 220,${floorY-30} 250,${floorY-40}" stroke="var(--green)" stroke-width="2" fill="none" stroke-linecap="round" stroke-dasharray="3 2"/>
                <text x="235" y="${floorY-45}" fill="var(--green)" font-size="8">Recovery</text>
            `;
        });
    }

    // Toggle chart popover
    window.toggleObjChart = function(type) {
        const el = document.getElementById('objChart' + type.charAt(0).toUpperCase() + type.slice(1));
        if (!el) return;
        // Close others
        document.querySelectorAll('.obj-chart-popover.open').forEach(function(p) {
            if (p !== el) p.classList.remove('open');
        });
        el.classList.toggle('open');
    };

    // ═══════════════════════
    //  EVENT HANDLERS
    // ═══════════════════════

    /**
     * LIVE: Called on every drag tick (oninput).
     * Updates ONLY the visual feedback + state + price. No DOM rebuild.
     * Throttled via requestAnimationFrame for mobile smoothness.
     */
    let _rafPending = false;
    let _rafId = null;
    let _rafSlider = null;

    function onSliderLive(id, inputEl) {
        let val = parseFloat(inputEl.value);

        // Apply constraints silently
        if (id === 'daily') val = Math.min(val, S.max);
        if (id === 'days' && S.overweek) val = Math.max(val, 5);

        // Update state immediately (lightweight)
        switch (id) {
            case 'sizeIdx':     S.sizeIdx = val; break;
            case 'target':      S.target = val; break;
            case 'target1':     S.target1 = val; break;
            case 'target2':     S.target2 = val; break;
            case 'daily':       S.daily = val; break;
            case 'max':         S.max = val; if (S.daily > val) S.daily = val; break;
            case 'split':       S.split = val; break;
            case 'days':        S.days = val; break;
            case 'consistency': S.consistency = val; break;
        }

        // Update THIS slider's visuals immediately (cheap DOM ops)
        _updateSliderVisual(id, val, inputEl);

        // If max changed, update daily slider visual too
        if (id === 'max') {
            const dailyWrap = document.querySelector('[data-slider-id="daily"]');
            if (dailyWrap) {
                const dailyInput = dailyWrap.querySelector('.slider-input');
                if (dailyInput && parseFloat(dailyInput.value) > val) {
                    dailyInput.value = val;
                    _updateSliderVisual('daily', val, dailyInput);
                }
            }
        }

        // Throttle the heavy updateUI (summary + price) via rAF
        if (!_rafPending) {
            _rafPending = true;
            _rafId = requestAnimationFrame(function() {
                updateUI();
                _rafPending = false;
            });
        }
    }

    function _updateSliderVisual(id, val, inputEl) {
        const wrap = inputEl.closest('.slider-wrap');
        if (!wrap) return;
        const min = parseFloat(inputEl.min);
        const max = parseFloat(inputEl.max);
        const pct = ((val - min) / (max - min)) * 100;
        const fill = wrap.querySelector('.slider-fill');
        const thumb = wrap.querySelector('.slider-thumb');
        const valEl = wrap.querySelector('.slider-val');
        if (fill) fill.style.width = pct + '%';
        if (thumb) thumb.style.left = pct + '%';
        if (valEl) {
            if (id === 'sizeIdx') {
                valEl.textContent = '$' + accountSizes[val].toLocaleString();
            } else if (id === 'days') {
                valEl.textContent = val;
            } else if (pctIds.includes(id)) {
                valEl.textContent = pctDisplay(id, val);
            } else {
                valEl.textContent = val + '%';
            }
        }
    }

    /**
     * DONE: Called on drag release (onchange).
     * Full rebuild to handle constraint cascading, warning boxes, etc.
     */
    function onSliderDone(id, val) {
        val = parseFloat(val);
        switch (id) {
            case 'sizeIdx':     S.sizeIdx = val; break;
            case 'target':      S.target = val; break;
            case 'target1':     S.target1 = val; break;
            case 'target2':     S.target2 = val; break;
            case 'daily':       S.daily = Math.min(val, S.max); break;
            case 'max':         S.max = val; if (S.daily > val) S.daily = val; break;
            case 'split':       S.split = val; break;
            case 'days':        S.days = S.overweek ? Math.max(val, 5) : val; break;
            case 'consistency': S.consistency = val; break;
        }
        buildSliders();
        updateUI();
    }

    function onToggle(id, val) {
        switch (id) {
            case 'dailyType': S.dailyType = val; break;
            case 'maxType':   S.maxType = val; break;
            case 'platform':  S.platform = val; break;
            case 'payout':
                S.payout = val;
                if (val === 'weekly' && S.consistency > 30) S.consistency = 30;
                break;
        }
        buildSliders();
        updateUI();
    }

    function onCheck(id, val) {
        // val can be boolean or event — normalize
        const checked = typeof val === 'boolean' ? val : !!val;
        if (id === 'overnight') {
            S.overnight = checked;
        } else if (id === 'overweek') {
            S.overweek = checked;
            if (checked) { S.overnight = false; if (S.days < 5) S.days = 5; }
        }
        buildSliders();
        updateUI();
    }

    // ═══════════════════════
    //  PUBLIC API
    // ═══════════════════════

    return {
        onSliderLive,
        onSliderDone,
        onToggle,
        onCheck,

        setTab(t) {
            S.tab = t;
            document.getElementById('tab-onestep').classList.toggle('active', t === 'onestep');
            document.getElementById('tab-twostep').classList.toggle('active', t === 'twostep');

            const b = document.getElementById('cfgBadge');
            if (t === 'onestep') {
                b.textContent = 'FAST TRACK';
                b.style.cssText = 'border-radius:6px;padding:3px 10px;font-size:10px;font-weight:700;font-family:"JetBrains Mono",monospace;letter-spacing:.08em;background:rgba(74,158,255,0.12);color:var(--blue);border:1px solid rgba(74,158,255,0.25)';
                S.target = 10; S.daily = 5; S.max = 8; S.split = 80; S.days = 5; S.consistency = 30;
            } else {
                b.textContent = 'CLASSIC';
                b.style.cssText = 'border-radius:6px;padding:3px 10px;font-size:10px;font-weight:700;font-family:"JetBrains Mono",monospace;letter-spacing:.08em;background:rgba(200,200,210,0.08);color:#c8c8d0;border:1px solid rgba(200,200,210,0.15)';
                S.target1 = 8; S.target2 = 5; S.daily = 5; S.max = 10; S.split = 80; S.days = 10; S.consistency = 30;
            }
            S.dailyType = 'intraday'; S.maxType = 'intraday';
            S.platform = 'mt5'; S.payout = 'monthly';
            S.overnight = false; S.overweek = false; S.activePromo = null;
            document.getElementById('promoInput').value = '';
            document.getElementById('promoMsg').innerHTML = '';
            document.getElementById('promoBtn').classList.remove('applied');
            document.getElementById('promoBtn').textContent = 'Apply';
            const ps2 = document.getElementById('presetSelect');
            if (ps2) { ps2.value = ''; ps2.classList.remove('has-value'); }
            buildSliders();
            updateUI();
        },

        reset() {
            if (S.tab === 'onestep') { S.target = 10; S.daily = 5; S.max = 8; S.split = 80; S.days = 5; }
            else { S.target1 = 8; S.target2 = 5; S.daily = 5; S.max = 10; S.split = 80; S.days = 10; }
            S.consistency = 30; S.dailyType = 'intraday'; S.maxType = 'intraday';
            S.platform = 'mt5'; S.payout = 'monthly';
            S.overnight = false; S.overweek = false;
            S.activePromo = null; S.sizeIdx = 9;
            document.getElementById('promoInput').value = '';
            document.getElementById('promoMsg').innerHTML = '';
            document.getElementById('promoBtn').classList.remove('applied');
            document.getElementById('promoBtn').textContent = 'Apply';
            const ps = document.getElementById('presetSelect');
            if (ps) { ps.value = ''; ps.classList.remove('has-value'); }
            buildSliders();
            updateUI();
        },

        applyPromo() {
            const code = document.getElementById('promoInput').value.trim().toUpperCase();
            const msg = document.getElementById('promoMsg');
            if (promoCodes[code]) {
                S.activePromo = promoCodes[code];
                msg.innerHTML = `<div class="promo-msg ok">${promoCodes[code].label} applied!</div>`;
                document.getElementById('promoBtn').classList.add('applied');
                document.getElementById('promoBtn').textContent = '✓ Applied';
            } else if (!code) {
                msg.innerHTML = '<div class="promo-msg err">Please enter a code</div>';
            } else {
                msg.innerHTML = '<div class="promo-msg err">Invalid promo code</div>';
            }
            updateUI();
        },

        share() {
            const size = accountSizes[S.sizeIdx];
            const p = S.tab === 'onestep' ? 'tg=' + S.target : 't1=' + S.target1 + '&t2=' + S.target2;
            let url = location.origin + location.pathname.replace(/[^/]*$/, 'challenges.php')
                + '?t=' + (S.tab === 'onestep' ? 1 : 2)
                + '&s=' + size + '&d=' + S.daily + '&m=' + S.max
                + '&sp=' + S.split + '&dy=' + S.days + '&c=' + S.consistency
                + '&p=' + S.payout[0] + '&on=' + (S.overnight ? 1 : 0) + '&ow=' + (S.overweek ? 1 : 0)
                + '&dl=' + S.dailyType[0] + '&ml=' + S.maxType[0] + '&pl=' + S.platform[0]
                + '&' + p;
            if (S.activePromo) {
                const code = Object.keys(promoCodes).find(k => promoCodes[k] === S.activePromo);
                if (code) url += '&promo=' + encodeURIComponent(code);
            }
            navigator.clipboard?.writeText(url).then(() => {
                document.getElementById('shareMsg').innerHTML = '<div class="share-msg">✓ Link copied to clipboard!</div>';
                setTimeout(() => document.getElementById('shareMsg').innerHTML = '', 3000);
            });
        },

        purchase() {
            if (!window.DOJI_CONFIG?.isLoggedIn) {
                AuthModal.open('signup');
            } else {
                // TODO: proceed to payment flow
                alert('Redirecting to payment...');
            }
        },

        /**
         * Load a preset configuration (competitor or affiliate).
         * Called from challenges page preset picker only.
         */
        loadPreset(presetId) {
            const presets = window.DOJI_CONFIG?.presets;
            if (!presets) return;

            // Find the preset across all groups
            let preset = null;
            for (const group of presets) {
                preset = group.presets.find(p => p.id === presetId);
                if (preset) break;
            }
            if (!preset) return;

            // Switch tab if needed
            const needTab = preset.tab;
            if (needTab && needTab !== S.tab) {
                this.setTab(needTab);
                // Re-set the select value since setTab resets it
                const picker = document.getElementById('presetSelect');
                if (picker) { picker.value = presetId; picker.classList.add('has-value'); }
            }

            // Apply config values
            const cfg = preset.config;
            if (needTab === 'onestep') {
                if (cfg.target !== undefined) S.target = cfg.target;
            } else {
                if (cfg.target1 !== undefined) S.target1 = cfg.target1;
                if (cfg.target2 !== undefined) S.target2 = cfg.target2;
            }
            if (cfg.daily !== undefined) S.daily = cfg.daily;
            if (cfg.max !== undefined) S.max = cfg.max;
            if (cfg.split !== undefined) S.split = cfg.split;
            if (cfg.days !== undefined) S.days = cfg.days;
            if (cfg.consistency !== undefined) S.consistency = cfg.consistency;
            if (cfg.dailyType !== undefined) S.dailyType = cfg.dailyType;
            if (cfg.maxType !== undefined) S.maxType = cfg.maxType;

            // Ensure constraints
            if (S.daily > S.max) S.daily = S.max;

            buildSliders();
            updateUI();
        },

        // Initialize on page load — restore from URL if shared link
        init() {
            const params = new URLSearchParams(window.location.search);

            if (params.has('t')) {
                // ── Restore tab ──
                S.tab = params.get('t') === '2' ? 'twostep' : 'onestep';

                // ── Restore account size ──
                if (params.has('s')) {
                    const sizeVal = parseInt(params.get('s'));
                    const idx = accountSizes.indexOf(sizeVal);
                    if (idx !== -1) S.sizeIdx = idx;
                }

                // ── Restore targets ──
                if (S.tab === 'onestep') {
                    if (params.has('tg')) S.target = clamp(parseInt(params.get('tg')), 5, 15);
                } else {
                    if (params.has('t1')) S.target1 = clamp(parseInt(params.get('t1')), 5, 12);
                    if (params.has('t2')) S.target2 = clamp(parseInt(params.get('t2')), 3, 8);
                }

                // ── Restore risk params ──
                if (params.has('d'))  S.daily = clamp(parseInt(params.get('d')), 2, 8);
                if (params.has('m'))  S.max   = clamp(parseInt(params.get('m')), 4, 15);
                if (params.has('sp')) S.split = clamp(parseInt(params.get('sp')), 60, 90);
                if (params.has('dy')) S.days  = clamp(parseInt(params.get('dy')), 3, 20);
                if (params.has('c'))  S.consistency = clamp(parseInt(params.get('c')), 20, 50);

                // ── Restore toggles ──
                const dlMap = { i: 'intraday', e: 'eod', s: 'static' };
                const plMap = { m: 'mt5', c: 'ctrader' };
                const payMap = { m: 'monthly', b: 'biweekly', w: 'weekly' };

                if (params.has('dl')) S.dailyType = dlMap[params.get('dl')] || 'intraday';
                if (params.has('ml')) S.maxType   = dlMap[params.get('ml')] || 'intraday';
                if (params.has('pl')) S.platform  = plMap[params.get('pl')] || 'mt5';
                if (params.has('p'))  S.payout    = payMap[params.get('p')] || 'monthly';

                S.overnight = params.get('on') === '1';
                S.overweek  = params.get('ow') === '1';
                if (S.overweek) S.overnight = true;

                // ── Restore promo ──
                if (params.has('promo')) {
                    const code = decodeURIComponent(params.get('promo')).toUpperCase();
                    if (promoCodes[code]) {
                        S.activePromo = promoCodes[code];
                        const pi = document.getElementById('promoInput');
                        if (pi) pi.value = code;
                        const pb = document.getElementById('promoBtn');
                        if (pb) { pb.classList.add('applied'); pb.textContent = '✓ Applied'; }
                        const pm = document.getElementById('promoMsg');
                        if (pm) pm.innerHTML = `<div class="promo-msg ok">${promoCodes[code].label} applied!</div>`;
                    }
                }

                // ── Ensure daily ≤ max ──
                if (S.daily > S.max) S.daily = S.max;

                // ── Activate correct tab visually ──
                document.getElementById('tab-onestep').classList.toggle('active', S.tab === 'onestep');
                document.getElementById('tab-twostep').classList.toggle('active', S.tab === 'twostep');
                const b = document.getElementById('cfgBadge');
                if (S.tab === 'onestep') {
                    b.textContent = 'FAST TRACK';
                    b.style.cssText = 'border-radius:6px;padding:3px 10px;font-size:10px;font-weight:700;font-family:"JetBrains Mono",monospace;letter-spacing:.08em;background:rgba(74,158,255,0.12);color:var(--blue);border:1px solid rgba(74,158,255,0.25)';
                } else {
                    b.textContent = 'CLASSIC';
                    b.style.cssText = 'border-radius:6px;padding:3px 10px;font-size:10px;font-weight:700;font-family:"JetBrains Mono",monospace;letter-spacing:.08em;background:rgba(200,200,210,0.08);color:#c8c8d0;border:1px solid rgba(200,200,210,0.15)';
                }

                // Clean URL without reloading
                history.replaceState(null, '', location.pathname);
            }

            buildSliders();
            updateUI();
        },
    };

    // Helper: clamp value within range
    function clamp(val, min, max) {
        if (isNaN(val)) return min;
        return Math.min(Math.max(val, min), max);
    }

})();

// Auto-init when DOM ready
document.addEventListener('DOMContentLoaded', () => Configurator.init());
