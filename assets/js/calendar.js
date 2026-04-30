/**
 * Doji Funding — Calendar Tab · Trading Journal
 * Seeded synthetic trade data, monthly calendar view, day-detail drawer.
 */

const CalendarTab = (function () {
    'use strict';

    var _filter  = 'all';
    var _year    = new Date().getFullYear();
    var _month   = new Date().getMonth();   /* 0-indexed */
    var _ready   = false;
    var _byDay   = {};                      /* cached daily trade map */

    var PAIRS   = ['EURUSD','GBPUSD','NAS100','US30','XAUUSD','USDJPY','AUDUSD','GBPJPY','SP500','USDCAD'];
    var MONTHS  = ['JANUARY','FEBRUARY','MARCH','APRIL','MAY','JUNE',
                   'JULY','AUGUST','SEPTEMBER','OCTOBER','NOVEMBER','DECEMBER'];
    var DAYS    = ['SUNDAY','MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY'];

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

        /* find max |dayPnl| in this month for tint scaling */
        var mn     = _month + 1;
        var prefix = _year + '-' + (mn < 10 ? '0' : '') + mn;
        var maxAbs = 1;
        Object.keys(byDay).forEach(function (dk) {
            if (dk.slice(0, 7) !== prefix) return;
            var v = Math.abs(byDay[dk].reduce(function (s, t) { return s + t.pnl; }, 0));
            if (v > maxAbs) maxAbs = v;
        });

        var html = '';
        for (var i = 0; i < offset; i++) {
            html += '<div class="cal-cell cal-cell--empty"></div>';
        }

        for (var d = 1; d <= daysInMonth; d++) {
            var dk      = _year + '-' + (mn < 10 ? '0' : '') + mn + '-' + (d < 10 ? '0' : '') + d;
            var trades  = byDay[dk];
            var dayDt   = new Date(_year, _month, d);
            var isFuture  = dayDt > today;
            var isToday   = dk === todayDk;
            var isWeekend = dayDt.getDay() === 0 || dayDt.getDay() === 6;
            var dayPnl    = trades ? trades.reduce(function (s, t) { return s + t.pnl; }, 0) : 0;
            var hasTrades = trades && !isFuture;

            var cls = 'cal-cell';
            if (isToday)   cls += ' cal-cell--today';
            if (isFuture)  cls += ' cal-cell--future';

            /* tinted background via inline rgba */
            var bgStyle = '';
            if (hasTrades) {
                var rgb       = dayPnl >= 0 ? '16,185,129' : '215,25,33';
                var alpha     = 0.04 + (Math.abs(dayPnl) / maxAbs) * 0.16;
                bgStyle = ' style="background-color:rgba(' + rgb + ',' + alpha.toFixed(3) + ');"';
            }

            var pnlHtml    = hasTrades ? '<div class="cal-cell-pnl ' + (dayPnl >= 0 ? 'green' : 'red') + '">' + fmtPnl(dayPnl) + '</div>' : '';
            var countHtml  = hasTrades ? '<div class="cal-cell-trades">' + trades.length + ' TR</div>' : '';
            var dayNumCls  = 'cal-cell-day' + (isWeekend ? ' cal-cell-day--we' : '');

            html += '<div class="' + cls + '"'
                  + (hasTrades ? ' data-dk="' + dk + '" tabindex="0"' : '')
                  + bgStyle + '>'
                  + '<div class="' + dayNumCls + '">' + d + '</div>'
                  + pnlHtml + countHtml
                  + '</div>';
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
        });

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
