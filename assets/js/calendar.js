/**
 * Doji Funding — Calendar Tab · Trading Journal
 * Seeded synthetic trade data, monthly calendar view, day-detail drawer.
 */

const CalendarTab = (function () {
    'use strict';

    var _filter        = 'all';
    var _year          = new Date().getFullYear();
    var _month         = new Date().getMonth();   /* 0-indexed */
    var _ready         = false;
    var _byDay         = {};                      /* cached daily trade map */
    var _journalDk     = null;                    /* currently open day key */
    var _autoSaveTimer = null;
    var _savedMsgTimer = null;

    var PAIRS   = ['EURUSD','GBPUSD','NAS100','US30','XAUUSD','USDJPY','AUDUSD','GBPJPY','SP500','USDCAD'];
    var MONTHS  = ['JANUARY','FEBRUARY','MARCH','APRIL','MAY','JUNE',
                   'JULY','AUGUST','SEPTEMBER','OCTOBER','NOVEMBER','DECEMBER'];
    var DAYS    = ['SUNDAY','MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY'];

    /* small face SVGs for calendar cells — use currentColor, sized 16px */
    var JRNL_FACES = [
        '',
        '<svg viewBox="0 0 22 22" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="9.5"/><circle cx="8" cy="9" r="0.9" fill="currentColor" stroke="none"/><circle cx="14" cy="9" r="0.9" fill="currentColor" stroke="none"/><path d="M6.5 15 Q11 11 15.5 15"/></svg>',
        '<svg viewBox="0 0 22 22" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="9.5"/><circle cx="8" cy="9" r="0.9" fill="currentColor" stroke="none"/><circle cx="14" cy="9" r="0.9" fill="currentColor" stroke="none"/><path d="M7.5 14.5 Q11 12.5 14.5 14.5"/></svg>',
        '<svg viewBox="0 0 22 22" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="9.5"/><circle cx="8" cy="9" r="0.9" fill="currentColor" stroke="none"/><circle cx="14" cy="9" r="0.9" fill="currentColor" stroke="none"/><path d="M7.5 13.5 L14.5 13.5"/></svg>',
        '<svg viewBox="0 0 22 22" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="9.5"/><circle cx="8" cy="9" r="0.9" fill="currentColor" stroke="none"/><circle cx="14" cy="9" r="0.9" fill="currentColor" stroke="none"/><path d="M7.5 13 Q11 15.5 14.5 13"/></svg>',
        '<svg viewBox="0 0 22 22" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="9.5"/><path d="M6.5 9 Q8 7.5 9.5 9"/><path d="M12.5 9 Q14 7.5 15.5 9"/><path d="M6 13 Q11 17.5 16 13"/></svg>'
    ];

    /* ── Seeded LCG RNG (identical to statistics.js) ────────── */
    function makeRng(seed) {
        var s = seed >>> 0;
        return function () {
            s = (Math.imul(s, 1664525) + 1013904223) >>> 0;
            return s / 4294967296;
        };
    }

    /* ── Generate per-trade data for one account ─────────────── */
    function genAccountTrades(seed, accountSize, acctStatus) {
        var rand = makeRng(seed);
        var ch   = acctStatus === 'funded'
            ? { wBias: 0.32, rWB: 1.0, rWR: 2.0, rLB: 0.40, rLR: 0.50, rMod: 0.85 }
            : acctStatus === 'passed'
            ? { wBias: 0.34, rWB: 1.0, rWR: 1.8, rLB: 0.50, rLR: 0.60, rMod: 0.90 }
            : acctStatus === 'failed'
            ? { wBias: 0.55, rWB: 0.5, rWR: 0.8, rLB: 0.75, rLR: 0.80, rMod: 1.30 }
            : { wBias: 0.38, rWB: 0.9, rWR: 1.8, rLB: 0.60, rLR: 0.70, rMod: 1.00 };

        var risk     = accountSize * 0.006 * ch.rMod;
        var nTrades  = 220 + Math.floor(rand() * 130);
        var startMs  = Date.now() - 365 * 86400000;
        var byDay    = {};

        for (var i = 0; i < nTrades; i++) {
            var win      = rand() > ch.wBias;
            var rMult    = win ? ch.rWB + rand() * ch.rWR : -(ch.rLB + rand() * ch.rLR);
            var pnl      = rMult * risk * (0.7 + rand() * 0.6);
            var isLong   = rand() > 0.48;
            var pair     = PAIRS[Math.floor(rand() * PAIRS.length)];
            var tradeMs  = startMs + rand() * 365 * 86400000;
            var dt       = new Date(tradeMs);
            /* skip weekends */
            if (dt.getDay() === 0) dt.setDate(dt.getDate() + 1);
            if (dt.getDay() === 6) dt.setDate(dt.getDate() - 1);
            var h   = 8 + Math.floor(rand() * 10);
            var m   = Math.floor(rand() * 60);
            var dur = 3 + Math.floor(rand() * 290);
            var dk  = dt.toISOString().slice(0, 10);
            if (!byDay[dk]) byDay[dk] = [];
            byDay[dk].push({
                pair: pair,
                time: (h < 10 ? '0' : '') + h + ':' + (m < 10 ? '0' : '') + m,
                dir:  isLong ? 'BUY' : 'SELL',
                pnl:  pnl,
                win:  win,
                dur:  dur
            });
        }
        /* sort each day by time ascending */
        Object.keys(byDay).forEach(function (dk) {
            byDay[dk].sort(function (a, b) { return a.time < b.time ? -1 : 1; });
        });
        return byDay;
    }

    /* ── Merge multiple daily maps ───────────────────────────── */
    function mergeDays(maps) {
        var merged = {};
        maps.forEach(function (m) {
            Object.keys(m).forEach(function (dk) {
                merged[dk] = (merged[dk] || []).concat(m[dk]);
            });
        });
        Object.keys(merged).forEach(function (dk) {
            merged[dk].sort(function (a, b) { return a.time < b.time ? -1 : 1; });
        });
        return merged;
    }

    /* ── Build filtered daily data ───────────────────────────── */
    function buildData() {
        var accounts = window.DojiStatAccounts || [];
        var filtered;

        if (_filter === 'evaluation') {
            filtered = accounts.filter(function (a) { return a.status === 'active' || a.status === 'passed'; });
        } else if (_filter === 'funded') {
            filtered = accounts.filter(function (a) { return a.status === 'funded'; });
        } else if (_filter.slice(0, 5) === 'acct-') {
            var id = parseInt(_filter.slice(5), 10);
            filtered = accounts.filter(function (a) { return a.id === id; });
        } else {
            filtered = accounts;
        }
        if (!filtered.length) {
            filtered = [{ id: 9999, size: 100000, status: 'active' }];
        }
        var maps = filtered.map(function (a) {
            return genAccountTrades(a.id * 31 + 17, a.size || 100000, a.status);
        });
        return mergeDays(maps);
    }

    /* ── Monthly KPI aggregation ─────────────────────────────── */
    function monthKpis(byDay, year, month) {
        var mn     = month + 1;
        var prefix = year + '-' + (mn < 10 ? '0' : '') + mn;
        var dayPnls = [], totalTrades = 0, wins = 0;

        Object.keys(byDay).forEach(function (dk) {
            if (dk.slice(0, 7) !== prefix) return;
            var dayPnl = byDay[dk].reduce(function (s, t) { return s + t.pnl; }, 0);
            dayPnls.push(dayPnl);
            totalTrades += byDay[dk].length;
            byDay[dk].forEach(function (t) { if (t.win) wins++; });
        });

        return {
            totalPnl:    dayPnls.reduce(function (s, v) { return s + v; }, 0),
            wr:          totalTrades > 0 ? wins / totalTrades : 0,
            best:        dayPnls.length ? Math.max.apply(null, dayPnls) : 0,
            worst:       dayPnls.length ? Math.min.apply(null, dayPnls) : 0,
            tradingDays: dayPnls.length,
            totalTrades: totalTrades
        };
    }

    /* ── Formatters ──────────────────────────────────────────── */
    function fmtPnl(v) {
        var abs = Math.abs(v);
        var s   = abs >= 1000 ? (abs / 1000).toFixed(1) + 'K' : abs.toFixed(0);
        return (v >= 0 ? '+$' : '−$') + s;
    }
    function fmtFull(v) {
        return (v >= 0 ? '+$' : '−$') + Math.abs(v).toFixed(2);
    }
    function fmtDur(min) {
        if (min < 60) return min + 'm';
        var h = Math.floor(min / 60), m = min % 60;
        return h + 'h' + (m > 0 ? m + 'm' : '');
    }

    /* ── Journal (localStorage) ─────────────────────────────── */
    function jKey(dk) { return 'doji-journal-' + _filter + '-' + dk; }

    function escHtml(s) {
        return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function jrnlIndicator(data) {
        var hasMood = data && data.mood > 0 && data.mood <= 5;
        var hasNote = data && data.note && data.note.trim().length > 0;
        if (!hasMood && !hasNote) return { icon: '', note: '' };

        var iconHtml = hasMood
            ? '<div class="cal-cell-jrnl" data-mood="' + data.mood + '">' + JRNL_FACES[data.mood] + '</div>'
            : '<div class="cal-cell-jrnl cal-cell-jrnl--dot" data-mood="0"></div>';

        var noteHtml = '';
        if (hasNote) {
            noteHtml = '<div class="cal-cell-note">' + escHtml(data.note.trim()) + '</div>';
        }
        return { icon: iconHtml, note: noteHtml };
    }

    function loadJournal(dk) {
        var noteEl = document.getElementById('calJournalNote');
        if (!noteEl) return;
        var raw = null;
        try { raw = localStorage.getItem(jKey(dk)); } catch (e) {}
        var data = raw ? JSON.parse(raw) : { mood: 0, note: '' };
        noteEl.value = data.note || '';
        document.querySelectorAll('.cal-mood-btn').forEach(function (btn) {
            btn.classList.remove('cal-mood-btn--active');
        });
        if (data.mood) {
            var activeBtn = document.querySelector('.cal-mood-btn[data-mood="' + data.mood + '"]');
            if (activeBtn) activeBtn.classList.add('cal-mood-btn--active');
        }
        var savedEl = document.getElementById('calJournalSaved');
        if (savedEl) savedEl.textContent = '';
    }

    function saveJournal() {
        if (!_journalDk) return;
        var noteEl    = document.getElementById('calJournalNote');
        var activeBtn = document.querySelector('.cal-mood-btn--active');
        var savedEl   = document.getElementById('calJournalSaved');
        var data = {
            mood: activeBtn ? parseInt(activeBtn.getAttribute('data-mood'), 10) : 0,
            note: noteEl ? noteEl.value : ''
        };
        try { localStorage.setItem(jKey(_journalDk), JSON.stringify(data)); } catch (e) {}
        if (savedEl) {
            savedEl.textContent = '[ SAVED ]';
            clearTimeout(_savedMsgTimer);
            _savedMsgTimer = setTimeout(function () { if (savedEl) savedEl.textContent = ''; }, 2000);
        }
        /* update cell live: replace face icon + note snippet */
        var cellEl = document.querySelector('[data-dk="' + _journalDk + '"]');
        if (cellEl) {
            var oldIcon = cellEl.querySelector('.cal-cell-jrnl');
            var oldNote = cellEl.querySelector('.cal-cell-note');
            if (oldIcon) oldIcon.parentNode.removeChild(oldIcon);
            if (oldNote) oldNote.parentNode.removeChild(oldNote);

            var ji = jrnlIndicator(data);
            if (ji.icon) {
                var t1 = document.createElement('div');
                t1.innerHTML = ji.icon;
                var dayEl = cellEl.querySelector('.cal-cell-day');
                cellEl.insertBefore(t1.firstChild, dayEl ? dayEl.nextSibling : null);
            }
            if (ji.note) {
                var t2 = document.createElement('div');
                t2.innerHTML = ji.note;
                cellEl.appendChild(t2.firstChild);
            }
        }
    }

    /* ── Render KPI strip ────────────────────────────────────── */
    function renderKpis(byDay) {
        var k = monthKpis(byDay, _year, _month);

        function set(id, val) {
            var el = document.getElementById(id);
            if (el) el.textContent = val;
        }
        function setPnl(id, v) {
            var el = document.getElementById(id);
            if (!el) return;
            el.textContent = fmtPnl(v);
            el.className   = 'cal-kpi-val ' + (v >= 0 ? 'green' : 'red');
        }

        setPnl('calKpiPnl',   k.totalPnl);
        set('calKpiWr',       Math.round(k.wr * 100) + '%');
        setPnl('calKpiBest',  k.best);
        setPnl('calKpiWorst', k.worst);
        set('calKpiDays',     k.tradingDays);
        set('calKpiTrades',   k.totalTrades);
    }

    /* ── Render one week-summary cell ───────────────────────── */
    function weekCell(pnl, hasActivity) {
        if (!hasActivity) {
            return '<div class="cal-cell-week"><div class="cal-cell-week-lbl">WK</div></div>';
        }
        var mod = Math.abs(pnl) < 5 ? '' : (pnl > 0 ? ' cal-cell-week--profit' : ' cal-cell-week--loss');
        return '<div class="cal-cell-week' + mod + '">'
             + '<div class="cal-cell-week-lbl">WK</div>'
             + '<div class="cal-cell-week-pnl">' + fmtPnl(pnl) + '</div>'
             + '</div>';
    }

    /* ── Render calendar grid ────────────────────────────────── */
    function renderCalendar(byDay) {
        var grid  = document.getElementById('calGrid');
        var title = document.getElementById('calMonthTitle');
        if (!grid) return;

        if (title) title.textContent = MONTHS[_month] + ' ' + _year;

        var today       = new Date();
        var todayDk     = today.toISOString().slice(0, 10);
        var firstDow    = new Date(_year, _month, 1).getDay();
        var offset      = (firstDow + 6) % 7;   /* Mon-based: Mon=0 … Sun=6 */
        var daysInMonth = new Date(_year, _month + 1, 0).getDate();
        var mn          = _month + 1;

        var html        = '';
        var col         = 0;    /* 0–6: current column in the 7-day row */
        var weekPnl     = 0;
        var weekActive  = false;

        /* emit offset empty cells, flushing any completed week rows */
        for (var i = 0; i < offset; i++) {
            html += '<div class="cal-cell cal-cell--empty"></div>';
            col++;
            if (col === 7) { html += weekCell(0, false); col = 0; weekPnl = 0; weekActive = false; }
        }

        /* emit day cells */
        for (var d = 1; d <= daysInMonth; d++) {
            var dk       = _year + '-' + (mn < 10 ? '0' : '') + mn + '-' + (d < 10 ? '0' : '') + d;
            var trades   = byDay[dk];
            var dayDt    = new Date(_year, _month, d);
            var isFuture = dayDt > today;
            var isToday  = dk === todayDk;
            var isWknd   = dayDt.getDay() === 0 || dayDt.getDay() === 6;
            var dayPnl   = trades ? trades.reduce(function (s, t) { return s + t.pnl; }, 0) : 0;
            var hasTr    = !!(trades && !isFuture);

            if (hasTr) { weekPnl += dayPnl; weekActive = true; }

            var cls = 'cal-cell';
            if (isToday)  cls += ' cal-cell--today';
            if (isFuture) cls += ' cal-cell--future';
            if (hasTr) {
                if (Math.abs(dayPnl) < 5) cls += ' cal-cell--be';
                else cls += dayPnl > 0 ? ' cal-cell--profit' : ' cal-cell--loss';
            }

            var pnlHtml  = hasTr ? '<div class="cal-cell-pnl">' + fmtPnl(dayPnl) + '</div>' : '';
            var cntHtml  = hasTr ? '<div class="cal-cell-trades">' + trades.length + ' TR</div>' : '';
            var dnCls    = 'cal-cell-day' + (isWknd ? ' cal-cell-day--we' : '');

            /* journal indicator (face + note snippet) */
            var jIcon = '', jNote = '';
            if (hasTr) {
                var jRaw = null;
                try { jRaw = localStorage.getItem(jKey(dk)); } catch (e) {}
                if (jRaw) {
                    var ji = jrnlIndicator(JSON.parse(jRaw));
                    jIcon = ji.icon;
                    jNote = ji.note;
                }
            }

            html += '<div class="' + cls + '"'
                  + (hasTr ? ' data-dk="' + dk + '" tabindex="0"' : '') + '>'
                  + '<div class="' + dnCls + '">' + d + '</div>'
                  + jIcon + pnlHtml + cntHtml + jNote + '</div>';

            col++;
            if (col === 7) {
                html += weekCell(weekPnl, weekActive);
                col = 0; weekPnl = 0; weekActive = false;
            }
        }

        /* flush last partial row */
        if (col > 0) {
            for (var j = col; j < 7; j++) {
                html += '<div class="cal-cell cal-cell--empty"></div>';
            }
            html += weekCell(weekPnl, weekActive);
        }

        grid.innerHTML = html;

        /* attach click listeners */
        grid.querySelectorAll('[data-dk]').forEach(function (cell) {
            cell.addEventListener('click', function () {
                showDay(this.getAttribute('data-dk'));
            });
        });
    }

    /* ── Show day detail ─────────────────────────────────────── */
    function showDay(dk) {
        var panel   = document.getElementById('calDetail');
        var title   = document.getElementById('calDetailTitle');
        var list    = document.getElementById('calDetailList');
        var summary = document.getElementById('calDetailSummary');
        if (!panel) return;

        _journalDk = dk;
        loadJournal(dk);

        var trades  = (_byDay[dk] || []);
        var dt      = new Date(dk + 'T12:00:00');
        var titleTx = 'TRADES · ' + DAYS[dt.getDay()] + ' ' + dt.getDate() + ' ' + MONTHS[dt.getMonth()] + ' ' + dt.getFullYear();
        if (title) title.textContent = titleTx;

        if (!trades.length) {
            if (list)    list.innerHTML    = '<div class="cal-detail-empty-row">[ NO TRADES THIS DAY ]</div>';
            if (summary) summary.innerHTML = '';
        } else {
            var rows = trades.map(function (t) {
                var dirCls = t.dir === 'BUY' ? 'ov-badge-win' : 'ov-badge-loss';
                var pnlCls = t.pnl >= 0 ? 'green' : 'red';
                return '<div class="ov-activity-row cal-trade-row">'
                     + '<span class="ov-badge ' + dirCls + '">' + t.dir + '</span>'
                     + '<span class="cal-trade-pair">' + t.pair + '</span>'
                     + '<span class="cal-trade-time">' + t.time + '</span>'
                     + '<span class="cal-trade-dur">' + fmtDur(t.dur) + '</span>'
                     + '<span class="ov-act-val ' + pnlCls + '">' + fmtFull(t.pnl) + '</span>'
                     + '</div>';
            }).join('');
            if (list) list.innerHTML = rows;

            if (summary) {
                var dayPnl = trades.reduce(function (s, t) { return s + t.pnl; }, 0);
                var wins   = trades.filter(function (t) { return t.win; }).length;
                summary.innerHTML =
                      '<span class="cal-ds-item">' + trades.length + ' TRADES</span>'
                    + '<span class="cal-ds-sep">·</span>'
                    + '<span class="cal-ds-item">' + wins + ' W / ' + (trades.length - wins) + ' L</span>'
                    + '<span class="cal-ds-spacer"></span>'
                    + '<span class="cal-ds-total ' + (dayPnl >= 0 ? 'green' : 'red') + '">' + fmtFull(dayPnl) + '</span>';
            }
        }

        panel.style.display = '';
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    /* ── Full refresh ────────────────────────────────────────── */
    function refresh() {
        _byDay = buildData();
        renderKpis(_byDay);
        renderCalendar(_byDay);
        var panel = document.getElementById('calDetail');
        if (panel) panel.style.display = 'none';
    }

    /* ── Init ────────────────────────────────────────────────── */
    document.addEventListener('DOMContentLoaded', function () {

        /* filter buttons */
        document.querySelectorAll('.cal-filter-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.cal-filter-btn').forEach(function (b) {
                    b.classList.remove('cal-filter-active');
                });
                this.classList.add('cal-filter-active');
                _filter = this.getAttribute('data-filter') || 'all';
                refresh();
            });
        });

        /* month navigation */
        var prev = document.getElementById('calPrev');
        var next = document.getElementById('calNext');
        var now  = new Date();

        if (prev) prev.addEventListener('click', function () {
            _month--;
            if (_month < 0) { _month = 11; _year--; }
            /* don't go further than 1 year back */
            if (_year < now.getFullYear() - 1) { _month = now.getMonth(); _year = now.getFullYear() - 1; }
            refresh();
        });

        if (next) next.addEventListener('click', function () {
            /* don't go past current month */
            if (_year > now.getFullYear() || (_year === now.getFullYear() && _month >= now.getMonth())) return;
            _month++;
            if (_month > 11) { _month = 0; _year++; }
            refresh();
        });

        /* close detail panel */
        var closeBtn = document.getElementById('calDetailClose');
        if (closeBtn) closeBtn.addEventListener('click', function () {
            var panel = document.getElementById('calDetail');
            if (panel) panel.style.display = 'none';
            _journalDk = null;
        });

        /* journal — mood buttons */
        document.querySelectorAll('.cal-mood-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var wasActive = this.classList.contains('cal-mood-btn--active');
                document.querySelectorAll('.cal-mood-btn').forEach(function (b) {
                    b.classList.remove('cal-mood-btn--active');
                });
                if (!wasActive) this.classList.add('cal-mood-btn--active');
                saveJournal();
            });
        });

        /* journal — textarea auto-save (debounced 1.2 s) */
        var journalNote = document.getElementById('calJournalNote');
        if (journalNote) {
            journalNote.addEventListener('input', function () {
                clearTimeout(_autoSaveTimer);
                _autoSaveTimer = setTimeout(saveJournal, 1200);
            });
        }

        /* journal — manual save button */
        var journalSaveBtn = document.getElementById('calJournalSave');
        if (journalSaveBtn) journalSaveBtn.addEventListener('click', saveJournal);

        /* activate when tab becomes visible */
        var tabEl = document.getElementById('tab-calendar');
        if (tabEl) {
            new MutationObserver(function () {
                if (tabEl.classList.contains('active') && !_ready) {
                    refresh();
                    _ready = true;
                }
            }).observe(tabEl, { attributes: true, attributeFilter: ['class'] });
        }

        /* re-render on theme change */
        new MutationObserver(function () {
            if (_ready) {
                var el = document.getElementById('tab-calendar');
                if (el && el.classList.contains('active')) renderCalendar(_byDay);
            }
        }).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
    });

    return { refresh: refresh };
}());

/* ═══════════════════════════════════════════════════════
   ECONOMIC CALENDAR — Forex Factory feed proxy
═══════════════════════════════════════════════════════ */
var EconCalendar = (function () {
    'use strict';

    var _weekStart = null;
    var _currencies = ['ALL']; /* array; ['ALL'] means no filter */
    var _impacts     = { high: true, medium: true, low: true, 'non-economic': true };
    var _eventTypes  = {
        'growth': true, 'inflation': true, 'employment': true, 'central-bank': true,
        'bonds': true, 'housing': true, 'consumer-surveys': true, 'business-surveys': true,
        'speeches': true, 'misc': true
    };
    var _cache     = {};
    var _inited    = false;

    var MONTHS_FULL  = ['JANUARY','FEBRUARY','MARCH','APRIL','MAY','JUNE',
                        'JULY','AUGUST','SEPTEMBER','OCTOBER','NOVEMBER','DECEMBER'];
    var MONTHS_SHORT = ['JAN','FEB','MAR','APR','MAY','JUN',
                        'JUL','AUG','SEP','OCT','NOV','DEC'];
    var DAYS_FULL    = ['SUNDAY','MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY'];

    var FLAGS = {
        USD:'🇺🇸', EUR:'🇪🇺', GBP:'🇬🇧', JPY:'🇯🇵',
        AUD:'🇦🇺', CAD:'🇨🇦', CHF:'🇨🇭', NZD:'🇳🇿',
        CNY:'🇨🇳', CHN:'🇨🇳', SGD:'🇸🇬', KRW:'🇰🇷'
    };

    /* Event type classification by title keyword — order matters (speeches first) */
    var TYPE_RULES = [
        ['speeches',         /speaks?|speech|testimony|press\s*conf|remarks?|appearance|interview|comment\b/i],
        ['central-bank',     /rate\s*decision|interest\s*rate|fomc|monetary\s*policy|quantitative|overnight\s*rate|cash\s*rate|repo\s*rate|\bminutes\b|mpc\s*(decision|meeting)|bank\s*rate\s*decision/i],
        ['employment',       /employ|unemploy|non.farm|payroll|jobless|claimant|labor|labour|\bwages?\b|earnings|adp\s*(national|employ)|participation\s*rate/i],
        ['inflation',        /\bcpi\b|\bppi\b|\bpce\b|\brpi\b|hicp|\bwpi\b|inflation|price\s*index|deflator|core\s*price/i],
        ['bonds',            /treasury|bond\s*auction|bill\s*auction|note\s*auction|yield\s*curve/i],
        ['housing',          /housing|home\s*sales|building\s*permit|construction\s*spend|mortgage|property\s*price|\bhpi\b|house\s*price/i],
        ['consumer-surveys', /consumer\s*conf|consumer\s*sent|consumer\s*clim|michigan|gfk|consumer\s*spend|retail\s*sales/i],
        ['business-surveys', /\bpmi\b|\bism\b|\bzew\b|\bifo\b|tankan|business\s*conf|business\s*clim|business\s*outlook|manufacturing\s*index|services\s*index|composite\s*index/i],
        ['growth',           /\bgdp\b|\bgnp\b|trade\s*bal|current\s*account|industrial\s*prod|factory\s*order|durable\s*good|manufacturing\s*output|business\s*invest/i]
    ];
    function classifyEvent(title) {
        for (var i = 0; i < TYPE_RULES.length; i++) {
            if (TYPE_RULES[i][1].test(title)) return TYPE_RULES[i][0];
        }
        return 'misc';
    }

    function pad2(n)  { return n < 10 ? '0' + n : '' + n; }
    function esc(s)   { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

    /* Monday of the week containing d */
    function weekStart(d) {
        var dt  = new Date(d);
        var day = dt.getDay();
        dt.setDate(dt.getDate() + (day === 0 ? -6 : 1 - day));
        dt.setHours(0, 0, 0, 0);
        return dt;
    }

    function weekLabel(ws) {
        var we = new Date(ws);
        we.setDate(we.getDate() + 6);
        return MONTHS_SHORT[ws.getMonth()] + ' ' + ws.getDate()
             + ' – '
             + MONTHS_SHORT[we.getMonth()] + ' ' + we.getDate()
             + ' ' + we.getFullYear();
    }

    /* "8:30am" → "08:30", "All Day" → "ALL DAY", "Tentative" → "TENT." */
    function parseTime(str) {
        if (!str || str === 'All Day')  return 'ALL DAY';
        if (str === 'Tentative')        return 'TENT.';
        var m = str.match(/^(\d+):(\d+)(am|pm)$/i);
        if (!m) return str.toUpperCase();
        var h = parseInt(m[1], 10), ap = m[3].toLowerCase();
        if (ap === 'pm' && h !== 12) h += 12;
        if (ap === 'am' && h === 12) h = 0;
        return pad2(h) + ':' + m[2];
    }

    function fv(v) { return (v && v.trim()) ? esc(v) : '—'; }

    /* XHR fetch for one month, result cached in _cache keyed 'YYYY-M' */
    function fetchMonth(year, month, cb) {
        var key = year + '-' + month;
        if (_cache[key]) { cb(null, _cache[key]); return; }
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'api/economic-calendar.php?year=' + year + '&month=' + month, true);
        xhr.timeout = 15000;
        xhr.onload = function () {
            if (xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data.error) console.warn('[EconCal] API error:', data.error, data.debug || '');
                    _cache[key] = data;
                    cb(null, data);
                } catch (e) { cb(e, null); }
            } else {
                console.warn('[EconCal] HTTP', xhr.status);
                cb(new Error('HTTP ' + xhr.status), null);
            }
        };
        xhr.onerror   = function () { console.warn('[EconCal] network error'); cb(new Error('network'), null); };
        xhr.ontimeout = function () { console.warn('[EconCal] timeout');       cb(new Error('timeout'),  null); };
        xhr.send();
    }

    /* Collect all events cached so far (for instant re-filter) */
    function cachedEvents() {
        var out = [];
        Object.keys(_cache).forEach(function (k) {
            if (_cache[k] && _cache[k].events) out = out.concat(_cache[k].events);
        });
        return out;
    }

    /* true when the displayed week contains today */
    function isCurrentWeek() {
        return weekStart(new Date()).getTime() === _weekStart.getTime();
    }

    /* ── Render ──────────────────────────────────────────── */
    /* silent=true → skip the LOADING indicator (used by auto-refresh) */
    function render(silent) {
        var body    = document.getElementById('econCalBody');
        var labelEl = document.getElementById('econWeekLabel');
        if (!body) return;
        if (labelEl) labelEl.textContent = weekLabel(_weekStart);
        if (!silent) body.innerHTML = '<div class="econ-loading">[ LOADING... ]</div>';

        /* collect which months the current week spans */
        var months = [], d = new Date(_weekStart);
        for (var i = 0; i < 7; i++) {
            var key = d.getFullYear() + '-' + (d.getMonth() + 1);
            if (months.indexOf(key) < 0) months.push(key);
            d.setDate(d.getDate() + 1);
        }

        var pending   = months.length;
        var fetchErr  = null;
        months.forEach(function (key) {
            var parts = key.split('-');
            fetchMonth(parseInt(parts[0], 10), parseInt(parts[1], 10), function (err, data) {
                if (err) fetchErr = err;
                else if (data && data.error && data.error !== 'not_current_month') fetchErr = data.error;
                pending--;
                if (pending === 0) {
                    var evts = cachedEvents();
                    if (!evts.length && fetchErr) {
                        body.innerHTML = '<div class="econ-empty">[ UNABLE TO LOAD FEED — CHECK CONSOLE ]</div>';
                    } else {
                        renderEvents(evts);
                    }
                }
            });
        });
    }

    function renderEvents(events) {
        var body = document.getElementById('econCalBody');
        if (!body) return;

        /* build 7 day-buckets Mon → Sun */
        var days = [];
        for (var i = 0; i < 7; i++) {
            var dt = new Date(_weekStart);
            dt.setDate(dt.getDate() + i);
            days.push({
                date: dt,
                dk:   dt.getFullYear() + '-' + pad2(dt.getMonth() + 1) + '-' + pad2(dt.getDate()),
                evts: []
            });
        }

        /* distribute + filter */
        events.forEach(function (ev) {
            var imp = (ev.impact || '').toLowerCase().replace(/\s+/g, '-');
            var impKey = (imp === 'holiday') ? 'non-economic' : imp;
            if (!_impacts[impKey]) return;
            if (!_eventTypes[classifyEvent(ev.title || '')]) return;
            if (_currencies[0] !== 'ALL' && _currencies.indexOf(ev.country) < 0) return;
            for (var j = 0; j < days.length; j++) {
                if (days[j].dk === ev.date) { days[j].evts.push(ev); break; }
            }
        });

        /* sort within each day by parsed time */
        days.forEach(function (day) {
            day.evts.sort(function (a, b) {
                return parseTime(a.time) < parseTime(b.time) ? -1 : 1;
            });
        });

        /* ── find next upcoming event ── */
        var now     = new Date();
        var todayDk = now.getFullYear() + '-' + pad2(now.getMonth() + 1) + '-' + pad2(now.getDate());
        var nowHHMM = pad2(now.getHours()) + ':' + pad2(now.getMinutes());
        var nextDk  = null, nextEi = -1;

        outer: for (var ni = 0; ni < days.length; ni++) {
            var nd = days[ni];
            if (nd.dk < todayDk) continue;
            for (var nj = 0; nj < nd.evts.length; nj++) {
                var nev = nd.evts[nj];
                if (nev.actual && nev.actual.trim()) continue; /* already released */
                if (nd.dk === todayDk) {
                    var nt = parseTime(nev.time);
                    if (nt === 'ALL DAY' || nt === 'TENT.' || nt >= nowHHMM) {
                        nextDk = nd.dk; nextEi = nj; break outer;
                    }
                } else {
                    nextDk = nd.dk; nextEi = nj; break outer;
                }
            }
        }

        var html = '', hasAny = false;
        days.forEach(function (day) {
            if (!day.evts.length) return;
            hasAny = true;
            var isToday = (day.dk === todayDk);
            var dt = day.date;
            html += '<div class="econ-day-hdr' + (isToday ? ' econ-day-hdr--today' : '') + '"'
                  + (isToday ? ' data-today="1"' : '') + '>'
                  + DAYS_FULL[dt.getDay()] + ' · ' + dt.getDate()
                  + ' ' + MONTHS_FULL[dt.getMonth()] + ' ' + dt.getFullYear()
                  + (isToday ? ' <span class="econ-today-badge">TODAY</span>' : '')
                  + '</div>';

            day.evts.forEach(function (ev, ei) {
                var isNext  = (day.dk === nextDk && ei === nextEi);
                var imp     = (ev.impact || '').toLowerCase().replace(/\s+/g, '-');
                var hasAct  = ev.actual && ev.actual.trim();
                html += '<div class="econ-event-row' + (isNext ? ' econ-event-row--next' : '') + '">'
                      + '<span class="econ-time">'     + parseTime(ev.time) + '</span>'
                      + '<span class="econ-currency" data-currency="' + esc(ev.country || '') + '">' + esc(ev.country || '') + '</span>'
                      + '<span class="econ-impact econ-impact--' + imp + '"></span>'
                      + '<span class="econ-title">'    + esc(ev.title || '') + '</span>'
                      + '<span class="econ-val econ-forecast">' + fv(ev.forecast) + '</span>'
                      + '<span class="econ-val econ-previous">' + fv(ev.previous) + '</span>'
                      + '<span class="econ-val econ-actual' + (hasAct ? ' econ-actual--live' : '') + '">' + fv(ev.actual) + '</span>'
                      + '</div>';
            });
        });

        body.innerHTML = hasAny
            ? html
            : '<div class="econ-empty">[ NO EVENTS MATCHING FILTERS ]</div>';

        /* scroll within the body div to the next event — never scrolls the page */
        if (nextDk !== null) {
            setTimeout(function () {
                var nextRow = body.querySelector('.econ-event-row--next');
                if (nextRow) {
                    var rowTop  = nextRow.getBoundingClientRect().top;
                    var bodyTop = body.getBoundingClientRect().top;
                    body.scrollTop += (rowTop - bodyTop) - 48;
                }
            }, 60);
        }
    }

    /* ── Auto-refresh every 5 min ───────────────────────── */
    function scheduleRefresh() {
        setInterval(function () {
            /* invalidate cache for the displayed week's months, then silently re-render */
            var d = new Date(_weekStart);
            for (var i = 0; i < 7; i++) {
                delete _cache[d.getFullYear() + '-' + (d.getMonth() + 1)];
                d.setDate(d.getDate() + 1);
            }
            render(true);
        }, 5 * 60 * 1000);
    }

    /* ── Init ────────────────────────────────────────────── */
    function init() {
        if (_inited) return;
        _inited    = true;
        _weekStart = weekStart(new Date());

        var prev = document.getElementById('econPrevWeek');
        var next = document.getElementById('econNextWeek');

        if (prev) prev.addEventListener('click', function () {
            _weekStart.setDate(_weekStart.getDate() - 7);
            render();
        });
        if (next) next.addEventListener('click', function () {
            _weekStart.setDate(_weekStart.getDate() + 7);
            render();
        });

        document.querySelectorAll('.econ-filter-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var cur = this.getAttribute('data-currency');

                if (cur === 'ALL') {
                    /* ALL resets everything */
                    _currencies = ['ALL'];
                } else {
                    /* remove ALL from selection */
                    var idx = _currencies.indexOf('ALL');
                    if (idx > -1) _currencies.splice(idx, 1);

                    /* toggle this currency */
                    var pos = _currencies.indexOf(cur);
                    if (pos > -1) {
                        _currencies.splice(pos, 1);
                    } else {
                        _currencies.push(cur);
                    }

                    /* if nothing selected, fall back to ALL */
                    if (_currencies.length === 0) _currencies = ['ALL'];
                }

                /* sync active classes */
                document.querySelectorAll('.econ-filter-btn').forEach(function (b) {
                    var bc = b.getAttribute('data-currency');
                    var active = _currencies[0] === 'ALL'
                        ? bc === 'ALL'
                        : _currencies.indexOf(bc) > -1;
                    b.classList.toggle('econ-filter-active', active);
                });

                renderEvents(cachedEvents());
            });
        });

        document.querySelectorAll('.econ-impact-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var key = this.getAttribute('data-impact');
                _impacts[key] = !_impacts[key];
                this.classList.toggle('econ-impact-active', _impacts[key]);
                renderEvents(cachedEvents());
            });
        });

        document.querySelectorAll('.econ-type-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var key = this.getAttribute('data-type');
                _eventTypes[key] = !_eventTypes[key];
                this.classList.toggle('econ-type-active', _eventTypes[key]);
                renderEvents(cachedEvents());
            });
        });

        scheduleRefresh();
        render();
    }

    /* watch for calendar tab activation */
    document.addEventListener('DOMContentLoaded', function () {
        var tabEl = document.getElementById('tab-calendar');
        if (!tabEl) return;
        new MutationObserver(function () {
            if (tabEl.classList.contains('active')) init();
        }).observe(tabEl, { attributes: true, attributeFilter: ['class'] });
    });

    return { init: init };
}());
