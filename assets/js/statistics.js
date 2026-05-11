/**
 * Doji Funding — Statistics Tab
 * Chart.js v4 charts, seeded demo data, draggable KPI + chart grids.
 * Lazy-loads Chart.js from CDN on first tab activation.
 */

const StatisticsTab = (function () {
    'use strict';

    var _charts     = {};
    var _filter     = 'all';
    var _period     = 180;
    var _ready      = false;
    var _dragSrc    = null;
    var _chartJsCbs = [];

    /* ─── Seeded LCG RNG ────────────────────────────────── */
    function makeRng(seed) {
        var s = seed >>> 0;
        return function () {
            s = (Math.imul(s, 1664525) + 1013904223) >>> 0;
            return s / 4294967296;
        };
    }

    /* ─── Generate synthetic account data ──────────────── */
    function genData(seed, accountSize, acctStatus) {
        var rand = makeRng(seed);
        /* Trading character varies by account status */
        var ch = acctStatus === 'funded'
            ? { wBias: 0.32, rWB: 1.0, rWR: 2.0, rLB: 0.40, rLR: 0.50, rMod: 0.85 }
            : acctStatus === 'passed'
            ? { wBias: 0.34, rWB: 1.0, rWR: 1.8, rLB: 0.50, rLR: 0.60, rMod: 0.90 }
            : acctStatus === 'failed'
            ? { wBias: 0.55, rWB: 0.5, rWR: 0.8, rLB: 0.75, rLR: 0.80, rMod: 1.30 }
            : { wBias: 0.38, rWB: 0.9, rWR: 1.8, rLB: 0.60, rLR: 0.70, rMod: 1.00 };
        var baseN    = 180 + Math.floor(rand() * 80);
        var nTrades  = Math.max(5, Math.round(baseN * Math.min(_period, 180) / 180));
        var risk     = accountSize * 0.006 * ch.rMod;
        /* long/short directional bias per status */
        var longBias = acctStatus === 'funded' ? 0.45
                     : acctStatus === 'passed'  ? 0.48
                     : acctStatus === 'failed'  ? 0.60
                     : 0.52;
        var trades   = [];
        var dailyMap = {};
        var startMs  = Date.now() - _period * 86400000;

        for (var i = 0; i < nTrades; i++) {
            var win       = rand() > ch.wBias;
            var rMultiple = win ? ch.rWB + rand() * ch.rWR : -(ch.rLB + rand() * ch.rLR);
            var pnl       = rMultiple * risk * (0.7 + rand() * 0.6);
            var isLong    = rand() > longBias;
            var session     = ['london','newyork','asian','sydney'][Math.floor(rand() * 4)];
            var dow         = Math.floor(rand() * 5);
            var durationMin = Math.floor(rand() * 280);
            var tradeMs     = startMs + rand() * _period * 86400000;
            var dt          = new Date(tradeMs);
            /* skip weekends */
            var wd = dt.getDay();
            if (wd === 0) dt.setDate(dt.getDate() + 1);
            if (wd === 6) dt.setDate(dt.getDate() - 1);
            var dk = dt.toISOString().slice(0, 10);
            dailyMap[dk] = (dailyMap[dk] || 0) + pnl;
            trades.push({ win: win, pnl: pnl, long: isLong, session: session, dow: dow, durationMin: durationMin });
        }

        /* build equity & drawdown curves */
        var dates   = [];
        var equity  = [];
        var ddCurve = [];
        var running = 0;
        var peak    = 0;

        for (var d = 0; d < _period; d++) {
            var dt2 = new Date(startMs + d * 86400000);
            if (dt2.getDay() === 0 || dt2.getDay() === 6) continue;
            var dk2     = dt2.toISOString().slice(0, 10);
            /* fill days with no trades: gentle drift */
            var dayPnl  = dailyMap[dk2] !== undefined
                ? dailyMap[dk2]
                : (rand() - 0.47) * risk * 0.4;
            running += dayPnl;
            peak     = Math.max(peak, running);
            var dd   = peak > 0 ? -(peak - running) / peak * 100 : 0;
            dates.push(dk2);
            equity.push(running);
            ddCurve.push(dd);
        }

        return { trades: trades, equity: equity, drawdown: ddCurve, dates: dates };
    }

    /* ─── Compute KPI metrics ───────────────────────────── */
    function computeMetrics(data) {
        var trades  = data.trades;
        var equity  = data.equity;
        var wins    = trades.filter(function (t) { return t.win; });
        var losses  = trades.filter(function (t) { return !t.win; });

        var winRate    = wins.length / (trades.length || 1);
        var avgWin     = wins.length   ? wins.reduce(function (s, t) { return s + t.pnl; }, 0) / wins.length   : 0;
        var avgLoss    = losses.length ? Math.abs(losses.reduce(function (s, t) { return s + t.pnl; }, 0) / losses.length) : 0;
        var grossWin   = wins.reduce(function (s, t) { return s + t.pnl; }, 0);
        var grossLoss  = Math.abs(losses.reduce(function (s, t) { return s + t.pnl; }, 0));
        var pf         = grossLoss > 0 ? grossWin / grossLoss : 9.99;
        var expectancy = winRate * avgWin - (1 - winRate) * avgLoss;

        /* daily returns for Sharpe / Sortino */
        var dailyRets  = [];
        for (var i = 1; i < equity.length; i++) dailyRets.push(equity[i] - equity[i - 1]);
        var meanRet    = dailyRets.reduce(function (s, r) { return s + r; }, 0) / (dailyRets.length || 1);
        var variance   = dailyRets.reduce(function (s, r) { return s + Math.pow(r - meanRet, 2); }, 0) / (dailyRets.length || 1);
        var stdDev     = Math.sqrt(variance);
        var sharpe     = stdDev > 0 ? (meanRet / stdDev) * Math.sqrt(252) : 0;
        var negRets    = dailyRets.filter(function (r) { return r < 0; });
        var downVar    = negRets.reduce(function (s, r) { return s + r * r; }, 0) / (dailyRets.length || 1);
        var sortino    = downVar > 0 ? (meanRet / Math.sqrt(downVar)) * Math.sqrt(252) : 0;

        /* max drawdown */
        var peak2 = -Infinity, maxDD = 0;
        equity.forEach(function (v) {
            peak2 = Math.max(peak2, v);
            var dd = peak2 > 0 ? (peak2 - v) / peak2 : 0;
            maxDD = Math.max(maxDD, dd);
        });

        /* Calmar: annualised return / MDD */
        var annRet = equity.length > 1 ? (equity[equity.length - 1] / equity.length) * 252 : 0;
        var calmar = maxDD > 0 ? annRet / (maxDD * Math.max(Math.abs(equity[equity.length - 1]), 1)) : 0;

        /* Consistency (0–10): low coefficient of variation = high score */
        var cv     = meanRet !== 0 ? Math.abs(stdDev / meanRet) : 10;
        var consist = Math.max(0, Math.min(10, 10 / (1 + cv * 0.45)));

        var avgRR = avgLoss > 0 ? avgWin / avgLoss : 0;

        var empty = { pnl: 0, session: '', long: true };
        var bestTrade  = trades.length ? trades.reduce(function (b, t) { return t.pnl > b.pnl ? t : b; }, trades[0]) : empty;
        var worstTrade = trades.length ? trades.reduce(function (b, t) { return t.pnl < b.pnl ? t : b; }, trades[0]) : empty;
        var nLong      = trades.filter(function (t) { return t.long; }).length;
        var nShort     = trades.length - nLong;
        var nLongWins  = trades.filter(function (t) { return t.long && t.win; }).length;
        var nShortWins = trades.filter(function (t) { return !t.long && t.win; }).length;

        /* day-level win rate */
        var dayWins = 0, dayLosses = 0, dayBe = 0;
        for (var di = 1; di < equity.length; di++) {
            var diff = equity[di] - equity[di - 1];
            if (diff > 0.5) dayWins++;
            else if (diff < -0.5) dayLosses++;
            else dayBe++;
        }
        var dayTotal   = dayWins + dayLosses + dayBe;
        var dayWinRate = dayTotal > 0 ? dayWins / dayTotal * 100 : 0;

        return {
            netPnl: equity[equity.length - 1] || 0,
            winRate: winRate, avgWin: avgWin, avgLoss: avgLoss,
            grossWin: grossWin, grossLoss: grossLoss,
            pf: pf, expectancy: expectancy, avgRR: avgRR,
            sharpe: sharpe, sortino: sortino,
            maxDDPct: maxDD * 100, calmar: calmar, consist: consist,
            nTrades: trades.length, nWins: wins.length, nLosses: losses.length,
            nBe: Math.round(trades.length * 0.02),
            dayWins: dayWins, dayLosses: dayLosses, dayBe: dayBe, dayWinRate: dayWinRate,
            bestTrade: bestTrade, worstTrade: worstTrade,
            nLong: nLong, nShort: nShort, nLongWins: nLongWins, nShortWins: nShortWins,
        };
    }

    /* ─── Theme-aware colour palette ───────────────────── */
    function pal() {
        var light = document.documentElement.getAttribute('data-theme') === 'light';
        return {
            /* data colors — darker/more saturated in light mode for contrast */
            cyan:    light ? '#0891B2' : '#22D3EE',
            green:   light ? '#16A34A' : '#4ADE80',
            amber:   light ? '#B45309' : '#FBBF24',
            error:   '#D71921',
            indigo:  light ? '#4338CA' : '#818CF8',
            /* UI chrome */
            chartBg: light ? 'rgba(0,0,0,0.05)'  : 'rgba(255,255,255,0.06)',
            grid:    light ? 'rgba(0,0,0,0.10)'  : 'rgba(255,255,255,0.05)',
            tick:    light ? 'rgba(0,0,0,0.65)'  : 'rgba(255,255,255,0.32)',
            ttBg:    light ? '#FFFFFF'            : '#1A1A1A',
            ttBorder:light ? '#DDDDDD'            : '#333333',
            ttText:  light ? '#1A1A1A'            : '#E8E8E8',
        };
    }

    /* ─── rgba helper ───────────────────────────────────── */
    function hex2rgba(hex, alpha) {
        var r = parseInt(hex.slice(1, 3), 16);
        var g = parseInt(hex.slice(3, 5), 16);
        var b = parseInt(hex.slice(5, 7), 16);
        return 'rgba(' + r + ',' + g + ',' + b + ',' + alpha + ')';
    }

    /* ─── Apply Chart.js global defaults ───────────────── */
    function applyDefaults() {
        var c = pal();
        Chart.defaults.color                                  = c.tick;
        Chart.defaults.borderColor                            = c.grid;
        Chart.defaults.font.family                            = "'Chivo Mono', monospace";
        Chart.defaults.font.size                              = 10;
        Chart.defaults.plugins.legend.display                 = false;
        Chart.defaults.plugins.tooltip.backgroundColor        = c.ttBg;
        Chart.defaults.plugins.tooltip.borderColor            = c.ttBorder;
        Chart.defaults.plugins.tooltip.borderWidth            = 1;
        Chart.defaults.plugins.tooltip.titleColor             = c.ttText;
        Chart.defaults.plugins.tooltip.bodyColor              = c.tick;
        Chart.defaults.plugins.tooltip.titleFont              = { family: "'Chivo Mono', monospace", size: 10, weight: '700' };
        Chart.defaults.plugins.tooltip.bodyFont               = { family: "'Chivo Mono', monospace", size: 10 };
        Chart.defaults.plugins.tooltip.padding                = 10;
        Chart.defaults.plugins.tooltip.cornerRadius           = 0;
        Chart.defaults.animation.duration                     = 750;
        Chart.defaults.animation.easing                       = 'easeOutQuart';
        if (!Chart.registry.plugins.get('chartBg')) {
            Chart.register({
                id: 'chartBg',
                beforeDraw: function (chart) {
                    var light = document.documentElement.getAttribute('data-theme') === 'light';
                    var ctx   = chart.ctx;
                    ctx.save();
                    ctx.fillStyle = light ? 'rgba(0,0,0,0.04)' : 'rgba(255,255,255,0.06)';
                    ctx.fillRect(0, 0, chart.width, chart.height);
                    ctx.restore();
                }
            });
        }
    }

    /* ─── Build chart datasets ──────────────────────────── */
    function monthlyBuckets(data) {
        var map = {};
        data.dates.forEach(function (dk, i) {
            var m = dk.slice(0, 7);
            if (!map[m]) map[m] = 0;
            map[m] += i > 0 ? data.equity[i] - data.equity[i - 1] : data.equity[0];
        });
        var keys    = Object.keys(map).sort();
        var labels  = keys.map(function (k) {
            var d = new Date(k + '-01');
            return d.toLocaleString('en', { month: 'short' }).toUpperCase() + ' \'' + String(d.getFullYear()).slice(2);
        });
        return { labels: labels, values: keys.map(function (k) { return map[k]; }) };
    }

    function sessionBuckets(trades) {
        var keys    = ['london', 'newyork', 'asian', 'sydney'];
        var lbls    = ['LONDON', 'NEW YORK', 'ASIAN', 'SYDNEY'];
        var pnl     = {}; var cnt = {};
        keys.forEach(function (k) { pnl[k] = 0; cnt[k] = 0; });
        trades.forEach(function (t) { pnl[t.session] += t.pnl; cnt[t.session]++; });
        return {
            labels: lbls,
            values: keys.map(function (k) { return pnl[k]; }),
            counts: keys.map(function (k) { return cnt[k]; }),
        };
    }

    function dowBuckets(trades) {
        var sum = [0,0,0,0,0]; var cnt = [0,0,0,0,0];
        trades.forEach(function (t) {
            if (t.dow >= 0 && t.dow <= 4) { sum[t.dow] += t.pnl; cnt[t.dow]++; }
        });
        return {
            labels: ['MON', 'TUE', 'WED', 'THU', 'FRI'],
            values: sum.map(function (v, i) { return cnt[i] ? v / cnt[i] : 0; }),
        };
    }

    function durationBuckets(trades) {
        var bins = ['SCALP\n<5m', 'SHORT\n5–60m', 'INTRADAY\n1–4h', 'SWING\n>4h'];
        var cnt  = [0, 0, 0, 0];
        var pnl  = [0, 0, 0, 0];
        trades.forEach(function (t) {
            var idx = t.durationMin < 5 ? 0 : t.durationMin < 60 ? 1 : t.durationMin < 240 ? 2 : 3;
            cnt[idx]++;
            pnl[idx] += t.pnl;
        });
        return {
            labels:  bins.map(function (b) { return b.replace('\n', ' '); }),
            counts:  cnt,
            avgPnl:  pnl.map(function (v, i) { return cnt[i] ? v / cnt[i] : 0; }),
        };
    }

    function dnaScores(metrics) {
        return [
            Math.min(10, metrics.winRate * 14),                   /* WIN %            0%→0  50%→7  71%+→10 */
            Math.min(10, Math.max(0, (metrics.pf - 1) * 5)),      /* PROFIT FACTOR    1→0   2→5   3→10    */
            Math.min(10, Math.max(0, metrics.avgRR * 5)),          /* AVG WIN/LOSS     0→0   1→5   2→10    */
            Math.min(10, Math.max(0, metrics.calmar * 1.5)),       /* RECOVERY FACTOR  0→0   4→6   7+→10   */
            Math.max(0,  10 - metrics.maxDDPct * 0.4),             /* MAX DRAWDOWN     0%→10 10%→6 25%+→0  */
            metrics.consist,                                        /* CONSISTENCY                          */
        ];
    }

    /* ─── Demo asset profile for one account ────────────── */
    function genAccountAssets(acct) {
        var rand = makeRng(acct.id * 5381 + 99991);
        /* 5 trading styles assigned by account id mod 5 */
        var STYLES = [
            { primary: ['XAUUSD', 'XAGUSD', 'USOIL'],            secondary: ['NAS100', 'EURUSD']  },
            { primary: ['EURUSD', 'GBPUSD', 'USDJPY'],           secondary: ['XAUUSD', 'NAS100']  },
            { primary: ['NAS100', 'SP500',   'US30'],             secondary: ['BTCUSD', 'EURUSD']  },
            { primary: ['BTCUSD', 'ETHUSD',  'NAS100'],          secondary: ['XAUUSD', 'SP500']   },
            { primary: ['AUDUSD', 'NZDUSD',  'USDCAD', 'DAX40'], secondary: ['XAUUSD', 'NAS100']  },
        ];
        var style    = STYLES[acct.id % STYLES.length];
        var isFunded = acct.status === 'funded' || acct.status === 'passed';
        var risk     = (acct.size || 50000) * 0.006;
        var lotScale = (acct.size || 50000) / 100000;

        var primPool = style.primary.slice();
        var secPool  = style.secondary.slice();
        var chosen   = [];
        var nPrim    = 2 + Math.floor(rand() * Math.min(3, primPool.length - 1));
        for (var i = 0; i < nPrim; i++) {
            var idx = Math.floor(rand() * primPool.length);
            chosen.push({ sym: primPool.splice(idx, 1)[0], isPrimary: true });
        }
        var nSec = 1 + Math.floor(rand() * Math.min(2, secPool.length));
        for (var j = 0; j < nSec; j++) {
            var idx2 = Math.floor(rand() * secPool.length);
            chosen.push({ sym: secPool.splice(idx2, 1)[0], isPrimary: false });
        }

        return chosen.map(function (c) {
            var trades  = c.isPrimary ? 14 + Math.floor(rand() * 28) : 4 + Math.floor(rand() * 14);
            var lots    = Math.max(0.01, +(lotScale * (0.3 + rand() * 2.8)).toFixed(2));
            var wr      = isFunded ? 54 + rand() * 20 : 44 + rand() * 24;
            var avgWin  = risk * (0.85 + rand() * 1.5);
            var avgLoss = risk * (0.40 + rand() * 0.70);
            var pnl     = trades * ((wr / 100) * avgWin - (1 - wr / 100) * avgLoss);
            return { symbol: c.sym, trades: trades, lots: lots, pnl: pnl, win_rate: +wr.toFixed(1) };
        });
    }

    /* ─── Traded asset data (real or demo) ──────────────── */
    function buildAssetData(filteredAccts) {
        if (!filteredAccts || !filteredAccts.length) return [];
        var real = window.DojiStatAssets;

        if (real && real.length) {
            /* filter by relevant challenge IDs only */
            var ids      = filteredAccts.map(function (a) { return a.id; });
            var relevant = real.filter(function (r) { return ids.indexOf(r.challenge_id) !== -1; });
            if (relevant.length) {
                var rmap = {};
                relevant.forEach(function (row) {
                    var s = row.symbol;
                    if (!rmap[s]) rmap[s] = { trades: 0, lots: 0, pnl: 0, wins: 0 };
                    rmap[s].trades += (row.trades   || 0);
                    rmap[s].lots   += (row.lots     || 0);
                    rmap[s].pnl    += (row.pnl      || 0);
                    rmap[s].wins   += Math.round((row.win_rate / 100) * (row.trades || 0));
                });
                return Object.keys(rmap).map(function (s) {
                    var r = rmap[s];
                    return { symbol: s, trades: r.trades, lots: r.lots, pnl: r.pnl,
                             win_rate: r.trades > 0 ? (r.wins / r.trades) * 100 : 0 };
                }).sort(function (a, b) { return Math.abs(b.pnl) - Math.abs(a.pnl); });
            }
        }

        /* demo: generate per-account assets and aggregate */
        var dmap = {};
        filteredAccts.forEach(function (acct) {
            genAccountAssets(acct).forEach(function (a) {
                if (!dmap[a.symbol]) dmap[a.symbol] = { trades: 0, lots: 0, pnl: 0, wins: 0 };
                dmap[a.symbol].trades += a.trades;
                dmap[a.symbol].lots   += a.lots;
                dmap[a.symbol].pnl    += a.pnl;
                dmap[a.symbol].wins   += Math.round(a.win_rate / 100 * a.trades);
            });
        });
        return Object.keys(dmap).map(function (s) {
            var r = dmap[s];
            return { symbol: s, trades: r.trades, lots: r.lots, pnl: r.pnl,
                     win_rate: r.trades > 0 ? (r.wins / r.trades) * 100 : 0 };
        }).sort(function (a, b) { return Math.abs(b.pnl) - Math.abs(a.pnl); });
    }

    /* ─── Initialise all charts ─────────────────────────── */
    function initCharts(data, metrics) {
        /* robust cleanup: destroy tracked charts + any orphaned Chart.js instances */
        Object.keys(_charts).forEach(function (k) {
            if (_charts[k]) { _charts[k].destroy(); _charts[k] = null; }
        });
        _charts = {};
        var c = pal();

        /* safe canvas getter — clears orphaned Chart instances on the element */
        function getCanvas(id) {
            var el = document.getElementById(id);
            if (!el) return null;
            if (window.Chart && typeof Chart.getChart === 'function') {
                var orphan = Chart.getChart(el);
                if (orphan) orphan.destroy();
            }
            return el;
        }

        var showStops = !!data.isSingleAcct;

        /* 1. Equity Curve + Drawdown — single canvas, stacked axes ── */
        var cvEq = getCanvas('chartEquity');
        if (cvEq) {
            var ctx   = cvEq.getContext('2d');
            var totalH = 310;
            var splitY = totalH * 0.78; /* ~80% equity, ~20% drawdown */

            var gradEq = ctx.createLinearGradient(0, 0, 0, splitY);
            gradEq.addColorStop(0,    hex2rgba(c.green, 0.52));
            gradEq.addColorStop(0.65, hex2rgba(c.green, 0.18));
            gradEq.addColorStop(1,    hex2rgba(c.green, 0.02));

            var gradDD = ctx.createLinearGradient(0, splitY, 0, totalH);
            gradDD.addColorStop(0,   'rgba(215,25,33,0.04)');
            gradDD.addColorStop(0.5, 'rgba(215,25,33,0.30)');
            gradDD.addColorStop(1,   'rgba(215,25,33,0.65)');

            var eqDatasets = [
                {
                    label: 'EQUITY',
                    data: data.equity,
                    borderColor: c.green, borderWidth: 1.5,
                    fill: 'origin', backgroundColor: gradEq,
                    pointRadius: 0, pointHoverRadius: 4,
                    pointHoverBackgroundColor: c.green,
                    tension: 0.35, order: 0, yAxisID: 'yE',
                },
                {
                    label: 'DRAWDOWN',
                    data: data.drawdown,
                    borderColor: '#D71921', borderWidth: 1.5,
                    fill: 'origin', backgroundColor: gradDD,
                    pointRadius: 0, tension: 0.3, order: 1, yAxisID: 'yD',
                },
            ];

            if (showStops) {
                var runPeak = 0;
                var stopDailyCurve = data.equity.map(function (v) {
                    runPeak = Math.max(runPeak, v);
                    return runPeak - data.stopDailyAmt;
                });
                eqDatasets.push({
                    label: 'DAILY STOP',
                    data: stopDailyCurve,
                    borderColor: hex2rgba(c.amber, 0.80), borderWidth: 1.5,
                    borderDash: [5, 3],
                    pointRadius: 0, fill: false, tension: 0.2, order: 2, yAxisID: 'yE',
                });
                eqDatasets.push({
                    label: 'MAX STOP',
                    data: data.dates.map(function () { return data.stopMaxLevel; }),
                    borderColor: hex2rgba(c.error, 0.75), borderWidth: 1.5,
                    borderDash: [5, 3],
                    pointRadius: 0, fill: false, tension: 0, order: 3, yAxisID: 'yE',
                });
            }

            _charts.equity = new Chart(cvEq, {
                type: 'line',
                data: { labels: data.dates, datasets: eqDatasets },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                maxTicksLimit: 7,
                                callback: function (v, i) { return data.dates[i] ? data.dates[i].slice(5) : ''; }
                            }
                        },
                        yE: {
                            type: 'linear', position: 'right',
                            stack: 'eqdd', stackWeight: 4,
                            min: 0,
                            grid: { color: c.grid },
                            ticks: { callback: function (v) { return v === 0 ? '0' : '$' + (v / 1000).toFixed(0) + 'K'; } }
                        },
                        yD: {
                            type: 'linear', position: 'right',
                            stack: 'eqdd', stackWeight: 1,
                            max: 0,
                            grid: { color: c.grid },
                            ticks: { callback: function (v) { return v.toFixed(1) + '%'; } },
                            afterBuildTicks: function (axis) {
                                axis.ticks = axis.ticks.filter(function (t) { return t.value !== 0; });
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: showStops, position: 'top', align: 'end',
                            labels: {
                                font: { family: "'Chivo Mono', monospace", size: 9 },
                                color: c.tick, boxWidth: 10, padding: 12,
                                filter: function (item) { return item.text !== 'EQUITY' && item.text !== 'DRAWDOWN'; }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                title: function (items) { return items[0].label; },
                                label: function (item) {
                                    var v = item.raw;
                                    if (item.dataset.yAxisID === 'yD') return ' DD: ' + v.toFixed(2) + '%';
                                    return ' ' + item.dataset.label + ': ' + (v >= 0 ? '+$' : '−$') + Math.abs(v).toLocaleString('en', { maximumFractionDigits: 0 });
                                }
                            }
                        }
                    }
                }
            });
        }

        /* populate equity footer */
        var eqVals    = data.equity;
        var eqCurrent = eqVals.length ? eqVals[eqVals.length - 1] : 0;
        var eqHigh    = eqVals.length ? Math.max.apply(null, eqVals) : 0;
        var eqLow     = eqVals.length ? Math.min.apply(null, eqVals) : 0;
        function fmtEq(v) { return (v >= 0 ? '+$' : '−$') + Math.abs(v).toLocaleString('en', { maximumFractionDigits: 0 }); }
        function setEqEl(id, txt, cls) {
            var el = document.getElementById(id);
            if (!el) return;
            el.textContent = txt;
            if (cls !== undefined) el.className = 'stat-eq-val ' + (cls || '');
        }
        setEqEl('eqCurrent', fmtEq(eqCurrent), eqCurrent >= 0 ? 'green' : 'red');
        setEqEl('eqHigh', fmtEq(eqHigh), 'green');
        setEqEl('eqLow',  fmtEq(eqLow),  'red');

        /* extra footer stats */
        var m = metrics;
        var eqAccounts = window.DojiStatAccounts || [];
        var eqLotTotal = 0;
        var eqHasLots  = eqAccounts.some(function (a) { return a.lots > 0; });
        if (eqHasLots) {
            eqAccounts.forEach(function (a) { eqLotTotal += (a.lots || 0); });
        } else {
            eqLotTotal = m.nTrades * 0.018;
        }
        setEqEl('eqTrades', m.nTrades.toString(), '');
        setEqEl('eqLots',   eqLotTotal.toFixed(2), '');
        setEqEl('eqExpect', fmtEq(m.expectancy), m.expectancy >= 0 ? 'green' : 'red');

        /* stop section — only for single account */
        var stopSection = document.getElementById('eqStopSection');
        if (stopSection) stopSection.style.display = showStops ? 'flex' : 'none';
        if (showStops) {
            var currentStopDaily = eqCurrent - (Math.max(eqCurrent, 0) - data.stopDailyAmt + Math.max(0, eqCurrent));
            /* simpler: distance from current equity to trailing daily stop */
            var trailPeak      = eqVals.length ? Math.max.apply(null, eqVals) : 0;
            var trailStopLevel = trailPeak - data.stopDailyAmt;
            var dailyDist      = eqCurrent - trailStopLevel;
            var maxDist        = eqCurrent - data.stopMaxLevel;
            setEqEl('eqDailyDist', '+$' + Math.max(0, dailyDist).toLocaleString('en', { maximumFractionDigits: 0 }) + ' BUFFER',
                dailyDist < data.accountSize * 0.01 ? 'red' : dailyDist < data.accountSize * 0.025 ? 'amber' : 'green');
            setEqEl('eqMaxDist',   '+$' + Math.max(0, maxDist).toLocaleString('en', { maximumFractionDigits: 0 }) + ' BUFFER',
                maxDist   < data.accountSize * 0.02 ? 'red' : maxDist < data.accountSize * 0.05 ? 'amber' : '');
            var dslEl = document.getElementById('eqDailyStopType');
            var mslEl = document.getElementById('eqMaxStopType');
            if (dslEl) dslEl.textContent = '· ' + (data.stopDailyLbl || 'STATIC');
            if (mslEl) mslEl.textContent = '· ' + (data.stopMaxLbl   || 'STATIC');
        }

        /* 2. Monthly P&L ──────────────────────────────── */
        var mo = monthlyBuckets(data);
        var cvMonthly = getCanvas('chartMonthly');
        if (cvMonthly) _charts.monthly = new Chart(cvMonthly, {
            type: 'bar',
            data: {
                labels: mo.labels,
                datasets: [{
                    data: mo.values,
                    backgroundColor: mo.values.map(function (v) { return v >= 0 ? hex2rgba(c.green, 0.72) : hex2rgba(c.error, 0.62); }),
                    borderColor:     mo.values.map(function (v) { return v >= 0 ? c.green : c.error; }),
                    borderWidth: 1, borderRadius: 2,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        grid: { color: c.grid },
                        ticks: {
                            callback: function (v) {
                                return (v >= 0 ? '+$' : '−$') + Math.abs(v / 1000).toFixed(0) + 'K';
                            }
                        }
                    }
                }
            }
        });

        /* 3. Win / Loss / BE Donut ────────────────────── */
        var cvWinloss = getCanvas('chartWinloss');
        if (cvWinloss) _charts.winloss = new Chart(cvWinloss, {
            type: 'doughnut',
            data: {
                labels: ['WIN', 'LOSS', 'BREAKEVEN'],
                datasets: [{
                    data: [metrics.nWins, metrics.nLosses, metrics.nBe],
                    backgroundColor: [hex2rgba(c.green, 0.78), hex2rgba(c.error, 0.68), hex2rgba(c.amber, 0.55)],
                    borderColor:     [c.green, c.error, c.amber],
                    borderWidth: 1, hoverOffset: 6,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '68%',
                plugins: {
                    legend: {
                        display: true, position: 'right',
                        labels: {
                            font: { family: "'Chivo Mono', monospace", size: 9 },
                            color: c.tick, boxWidth: 10, padding: 14,
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (item) {
                                var total = metrics.nWins + metrics.nLosses + metrics.nBe;
                                return ' ' + item.raw + ' · ' + ((item.raw / total) * 100).toFixed(1) + '%';
                            }
                        }
                    }
                }
            }
        });

        /* 4. Trading DNA Radar ────────────────────────── */
        var dna    = dnaScores(metrics);
        var dnaAvg = dna.reduce(function (s, v) { return s + v; }, 0) / dna.length;
        var _dnaGradeMap = [
            { min: 9.5, letter: 'S',  color: c.green },
            { min: 8.5, letter: 'A+', color: c.green },
            { min: 7.5, letter: 'A',  color: c.green },
            { min: 6.5, letter: 'B+', color: c.amber },
            { min: 5.5, letter: 'B',  color: c.amber },
            { min: 4.5, letter: 'C+', color: c.amber },
            { min: 3.5, letter: 'C',  color: '#888888' },
            { min: 0,   letter: 'D',  color: c.error },
        ];
        var _dnaDescMap = { S: '[ ELITE TRADER ]', 'A+': '[ EXCEPTIONAL ]', A: '[ EXCELLENT ]', 'B+': '[ VERY GOOD ]', B: '[ GOOD ]', 'C+': '[ AVERAGE ]', C: '[ BELOW AVERAGE ]', D: '[ NEEDS IMPROVEMENT ]' };
        var _dnaGrade   = _dnaGradeMap.find(function (g) { return dnaAvg >= g.min; }) || _dnaGradeMap[_dnaGradeMap.length - 1];
        var _dnaGradeIdx = _dnaGradeMap.indexOf(_dnaGrade);
        var _dnaSegClass = _dnaGradeIdx <= 2 ? 'stat-seg-on' : _dnaGradeIdx <= 5 ? 'stat-seg-on-amber' : _dnaGradeIdx === 6 ? 'stat-seg-on-gray' : 'stat-seg-on-red';
        var _dnaRadarBg  = hex2rgba(_dnaGrade.color, 0.12);
        function _applyDnaGrade(gradeId, lblId, descId, segsId) {
            var gEl = document.getElementById(gradeId);
            var lEl = document.getElementById(lblId);
            var dEl = document.getElementById(descId);
            if (gEl) { gEl.textContent = _dnaGrade.letter; gEl.style.color = _dnaGrade.color; }
            if (lEl)   lEl.textContent = dnaAvg.toFixed(1) + ' / 10';
            if (dEl)   dEl.textContent = _dnaDescMap[_dnaGrade.letter] || '';
            setSegs(segsId, dnaAvg * 10, _dnaSegClass);
        }
        _applyDnaGrade('statDnaGrade', 'statDnaGradeLbl', 'statDnaGradeDesc', 'statDnaSegs');
        _applyDnaGrade('ovDnaGrade',   'ovDnaGradeLbl',   'ovDnaGradeDesc',   'ovDnaSegs');

        /* v2 score bar */
        var _dna100     = dnaAvg * 10;
        var _dnaScoreEl = document.getElementById('statDnaScoreVal');
        var _dnaMarkEl  = document.getElementById('statDnaBarMarker');
        (function () {
            var p = Math.min(100, Math.max(0, _dna100));
            var r1, g1, b1, r2, g2, b2, t;
            if (p <= 50) {
                r1=0xD7; g1=0x19; b1=0x21;  /* #D71921 red  */
                r2=0xF5; g2=0x9E; b2=0x0B;  /* #F59E0B amber */
                t = p / 50;
            } else {
                r1=0xF5; g1=0x9E; b1=0x0B;  /* #F59E0B amber */
                r2=0x10; g2=0xB9; b2=0x81;  /* #10B981 green */
                t = (p - 50) / 50;
            }
            var col = 'rgb(' + Math.round(r1+(r2-r1)*t) + ',' + Math.round(g1+(g2-g1)*t) + ',' + Math.round(b1+(b2-b1)*t) + ')';
            if (_dnaScoreEl) { _dnaScoreEl.textContent = _dna100.toFixed(1); _dnaScoreEl.style.color = col; }
            if (_dnaMarkEl)  _dnaMarkEl.style.left = Math.min(98, Math.max(2, _dna100)).toFixed(1) + '%';
        }());

        var _isLight = document.documentElement.getAttribute('data-theme') === 'light';
        var _dnaGrid = _isLight ? 'rgba(0,0,0,0.13)' : 'rgba(255,255,255,0.13)';
        var _dnaLbl  = _isLight ? 'rgba(0,0,0,0.72)'  : 'rgba(255,255,255,0.72)';

        var cvDna = getCanvas('chartDna');
        if (cvDna) _charts.dna = new Chart(cvDna, {
            type: 'radar',
            data: {
                labels: ['WIN %', 'PROFIT FACTOR', 'AVG WIN/LOSS', 'RECOVERY FACTOR', 'MAX DRAWDOWN', 'CONSISTENCY'],
                datasets: [{
                    data: dna,
                    borderColor: c.indigo, borderWidth: 2,
                    backgroundColor: hex2rgba(c.indigo, 0.22),
                    pointBackgroundColor: c.indigo,
                    pointBorderColor: 'rgba(0,0,0,0.55)',
                    pointRadius: 4, pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: {
                    r: {
                        min: 0, max: 10,
                        ticks: { display: false, stepSize: 2 },
                        grid: { color: _dnaGrid },
                        angleLines: { color: _dnaGrid },
                        pointLabels: {
                            font: { family: "'Chivo Mono', monospace", size: 9 },
                            color: _dnaLbl,
                            padding: 4,
                        }
                    }
                },
                plugins: { legend: { display: false } }
            }
        });

        /* 5. Session Performance ──────────────────────── */
        var sess = sessionBuckets(data.trades);
        var cvSession = getCanvas('chartSession');
        if (cvSession) _charts.session = new Chart(cvSession, {
            type: 'bar',
            data: {
                labels: sess.labels,
                datasets: [{
                    data: sess.values,
                    backgroundColor: [hex2rgba(c.cyan, 0.70), hex2rgba(c.amber, 0.70), hex2rgba(c.indigo, 0.65), 'rgba(153,153,153,0.50)'],
                    borderColor:     [c.cyan, c.amber, c.indigo, '#666'],
                    borderWidth: 1, borderRadius: 2,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true, maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: { color: c.grid },
                        ticks: {
                            callback: function (v) { return (v >= 0 ? '+$' : '−$') + Math.abs(v / 1000).toFixed(1) + 'K'; }
                        }
                    },
                    y: { grid: { display: false } }
                }
            }
        });

        /* 6. Day of Week P&L ──────────────────────────── */
        var dow = dowBuckets(data.trades);
        var cvDow = getCanvas('chartDow');
        if (cvDow) _charts.dow = new Chart(cvDow, {
            type: 'bar',
            data: {
                labels: dow.labels,
                datasets: [{
                    data: dow.values,
                    backgroundColor: dow.values.map(function (v) { return v >= 0 ? hex2rgba(c.green, 0.68) : hex2rgba(c.error, 0.58); }),
                    borderColor:     dow.values.map(function (v) { return v >= 0 ? c.green : c.error; }),
                    borderWidth: 1, borderRadius: 2,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        grid: { color: c.grid },
                        ticks: {
                            callback: function (v) { return (v >= 0 ? '+$' : '−$') + Math.abs(v).toFixed(0); }
                        }
                    }
                }
            }
        });

        /* 7. Trade Duration Profile (mixed bar + line) ── */
        var dur = durationBuckets(data.trades);
        var cvDuration = getCanvas('chartDuration');
        if (cvDuration) _charts.duration = new Chart(cvDuration, {
            type: 'bar',
            data: {
                labels: dur.labels,
                datasets: [
                    {
                        label: '# TRADES',
                        data: dur.counts,
                        backgroundColor: hex2rgba(c.cyan, 0.62),
                        borderColor: c.cyan,
                        borderWidth: 1, borderRadius: 2,
                        yAxisID: 'yN',
                    },
                    {
                        label: 'AVG P&L',
                        type: 'line',
                        data: dur.avgPnl,
                        borderColor: c.amber, borderWidth: 2,
                        pointBackgroundColor: c.amber,
                        pointRadius: 4, pointHoverRadius: 6,
                        tension: 0.3, yAxisID: 'yP',
                    }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: {
                    x: { grid: { display: false } },
                    yN: { position: 'left', grid: { color: c.grid } },
                    yP: {
                        position: 'right', grid: { display: false },
                        ticks: {
                            callback: function (v) { return (v >= 0 ? '+$' : '−$') + Math.abs(v).toFixed(0); }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true, position: 'top', align: 'end',
                        labels: {
                            font: { family: "'Chivo Mono', monospace", size: 9 },
                            color: c.tick, boxWidth: 8, padding: 10,
                        }
                    }
                }
            }
        });

        /* 8. Drawdown Timeline ─────────────────────────── */
        var cvDD = getCanvas('chartDrawdown');
        if (cvDD) {
            var gradDD = cvDD.getContext('2d').createLinearGradient(0, 0, 0, 80);
            gradDD.addColorStop(0,   'rgba(215,25,33,0.04)');
            gradDD.addColorStop(0.4, 'rgba(215,25,33,0.28)');
            gradDD.addColorStop(1,   'rgba(215,25,33,0.62)');

        _charts.drawdown = new Chart(cvDD, {
            type: 'line',
            data: {
                labels: data.dates,
                datasets: [{
                    data: data.drawdown,
                    borderColor: '#D71921', borderWidth: 1.5,
                    fill: 'origin', backgroundColor: gradDD,
                    pointRadius: 0, tension: 0.3,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                layout: { padding: { top: 2 } },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            maxTicksLimit: 7,
                            callback: function (v, i) { return data.dates[i] ? data.dates[i].slice(5) : ''; }
                        }
                    },
                    y: {
                        position: 'right', max: 0, grid: { color: c.grid },
                        ticks: { callback: function (v) { return v.toFixed(1) + '%'; } }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function (item) { return ' DD: ' + item.raw.toFixed(2) + '%'; }
                        }
                    }
                }
            }
        });
        } /* end if (cvDD) */

        /* 9. Traded Assets — HTML segmented bars ──────── */
        renderAssetBars(buildAssetData(data.filteredAccounts || []));
    }

    /* ─── Update semicircle gauge SVG paths ─────────────── */
    function updateGauge(greenId, redId, dotId, pct) {
        var gEl = document.getElementById(greenId);
        var rEl = document.getElementById(redId);
        var dEl = document.getElementById(dotId);
        if (!gEl || !rEl || !dEl) return;
        var p  = Math.max(0.02, Math.min(0.98, pct / 100));
        var a  = Math.PI * (1 - p);
        var sx = (50 + 40 * Math.cos(a)).toFixed(2);
        var sy = (52 - 40 * Math.sin(a)).toFixed(2);
        gEl.setAttribute('d', 'M 10 52 A 40 40 0 0 1 ' + sx + ' ' + sy);
        rEl.setAttribute('d', 'M ' + sx + ' ' + sy + ' A 40 40 0 0 1 90 52');
        dEl.setAttribute('cx', sx);
        dEl.setAttribute('cy', sy);
    }

    /* ─── Full-circle donut gauge (Profit Factor) ───────── */
    function updateCircGauge(greenId, redId, pct) {
        var gEl = document.getElementById(greenId);
        var rEl = document.getElementById(redId);
        if (!gEl || !rEl) return;
        var r      = 38;
        var circum = 2 * Math.PI * r;                   // 238.76
        var p      = Math.max(0.02, Math.min(0.98, pct / 100));
        var gLen   = (circum * p).toFixed(2);
        var rLen   = (circum * (1 - p)).toFixed(2);
        var c      = circum.toFixed(2);
        var off    = (-(circum * 0.25)).toFixed(2);      // start from 12 o'clock
        gEl.setAttribute('stroke-dasharray',  gLen + ' ' + c);
        gEl.setAttribute('stroke-dashoffset', off);
        rEl.setAttribute('stroke-dasharray',  rLen + ' ' + c);
        rEl.setAttribute('stroke-dashoffset', (parseFloat(off) - parseFloat(gLen)).toFixed(2));
    }

    /* ─── Fill segmented bar (10 blocks) ───────────────── */
    function setSegs(id, pct, onClass) {
        var el = document.getElementById(id);
        if (!el) return;
        var segs   = el.querySelectorAll('.stat-seg');
        var filled = Math.round(Math.max(0, Math.min(100, pct)) / 10);
        segs.forEach(function (s, i) {
            s.className = 'stat-seg ' + (i < filled ? (onClass || 'stat-seg-on') : '');
        });
    }

    /* ─── Render traded-asset HTML rows ─────────────────── */
    function renderAssetBars(assets) {
        var el = document.getElementById('statAssetBars');
        if (!el) return;
        if (!assets || !assets.length) {
            el.innerHTML = '<div class="stat-asset-empty">NO DATA</div>';
            return;
        }
        var maxAbs = Math.max.apply(null, assets.map(function (a) { return Math.abs(a.pnl); })) || 1;
        el.innerHTML = assets.map(function (a) {
            var pct    = Math.abs(a.pnl) / maxAbs * 100;
            var isPos  = a.pnl >= 0;
            var pnlStr = (isPos ? '+$' : '−$') + Math.abs(a.pnl).toLocaleString('en', { maximumFractionDigits: 0 });
            return '<div class="stat-asset-row">' +
                '<span class="stat-asset-sym">' + a.symbol + '</span>' +
                '<div class="stat-asset-bar-wrap"><div class="stat-asset-fill ' + (isPos ? 'stat-asset-pos' : 'stat-asset-neg') + '" style="width:' + pct.toFixed(1) + '%"></div></div>' +
                '<span class="stat-asset-pnl ' + (isPos ? 'green' : 'red') + '">' + pnlStr + '</span>' +
                '<span class="stat-asset-wr">' + a.win_rate.toFixed(1) + '%&nbsp;WR</span>' +
                '<span class="stat-asset-lots">' + (a.lots || 0).toFixed(2) + 'L</span>' +
                '<span class="stat-asset-trades">' + a.trades + 'T</span>' +
            '</div>';
        }).join('');
    }

    /* ─── Update KPI cards ──────────────────────────────── */
    function updateKpis(m) {
        var $ = function (id) { return document.getElementById(id); };

        function setVal(id, txt, cls) {
            var el = $(id);
            if (!el) return;
            el.textContent = txt;
            if (cls !== undefined) el.className = 'stat-kpi-val ' + (cls || '');
        }
        function setSub(id, txt) { var el = $(id); if (el) el.textContent = txt; }
        function fmtPnl(v) { return (v >= 0 ? '+$' : '−$') + Math.abs(v).toLocaleString('en', { maximumFractionDigits: 0 }); }
        function f2(v) { return v.toFixed(2); }
        function f1(v) { return v.toFixed(1); }

        setVal('skPnl', fmtPnl(m.netPnl), m.netPnl >= 0 ? 'green' : 'red');
        setSub('skPnlSub', m.nTrades + ' TRADES · ' + f1(m.winRate * 100) + '% WIN');

        var wrPct = m.winRate * 100;
        setVal('skWr', f1(wrPct) + '%', wrPct >= 55 ? 'green' : wrPct >= 45 ? '' : 'red');
        updateGauge('skWrGaugeGn', 'skWrGaugeRd', 'skWrGaugeDt', wrPct);
        setSub('skWrWins',   m.nWins);
        setSub('skWrBe',     m.nBe);
        setSub('skWrLosses', m.nLosses);

        var pfCls = m.pf >= 1.8 ? 'green' : m.pf >= 1 ? '' : 'red';
        setVal('skPf', f2(m.pf), pfCls);
        var pfGaugePct = Math.min(98, m.pf / (m.pf + 1) * 100);
        updateCircGauge('skPfCircGn', 'skPfCircRd', pfGaugePct);
        setSub('skPfGrossWin',  '+$' + Math.round(m.grossWin  / 1000) + 'K');
        setSub('skPfGrossLoss', '−$' + Math.round(m.grossLoss / 1000) + 'K');

        setVal('skDwr', f1(m.dayWinRate) + '%', m.dayWinRate >= 55 ? 'green' : m.dayWinRate >= 45 ? '' : 'red');
        updateGauge('skDwrGaugeGn', 'skDwrGaugeRd', 'skDwrGaugeDt', m.dayWinRate);
        setSub('skDwrWins',   m.dayWins);
        setSub('skDwrBe',     m.dayBe);
        setSub('skDwrLosses', m.dayLosses);

        var shCls = m.sharpe >= 1.5 ? 'green' : m.sharpe >= 0.5 ? '' : 'red';
        setVal('skSharpe', f2(m.sharpe), shCls);
        setSub('skSharpeSub', m.sharpe >= 2 ? '[ EXCELLENT ]' : m.sharpe >= 1 ? '[ GOOD ]' : '[ BELOW TARGET ]');

        var mddPct = m.maxDDPct;
        setVal('skMdd', '−' + f1(mddPct) + '%', mddPct > 8 ? 'red' : 'amber');
        setSegs('skMddSegs', Math.min(100, mddPct * 5), mddPct < 4 ? 'stat-seg-on' : mddPct < 8 ? 'stat-seg-on-amber' : 'stat-seg-on-red');
        setSub('skMddSub', mddPct < 4 ? '[ EXCELLENT ]' : mddPct < 8 ? '[ WITHIN LIMITS ]' : '[ HIGH RISK ]');

        setVal('skExpect', fmtPnl(m.expectancy), m.expectancy > 0 ? 'green' : 'red');
        setSub('skExpectSub', 'EXPECTED PER TRADE');

        setVal('skConsist', f1(m.consist) + ' / 10', m.consist >= 7 ? 'green' : m.consist >= 5 ? '' : 'red');
        setSegs('skConsistSegs', m.consist * 10, m.consist >= 7 ? 'stat-seg-on' : m.consist >= 5 ? 'stat-seg-on-amber' : 'stat-seg-on-red');
        setSub('skConsistSub', m.consist >= 7 ? '[ CONSISTENT ]' : m.consist >= 5 ? '[ MODERATE ]' : '[ VOLATILE ]');

        var calCls = m.calmar >= 1 ? 'green' : '';
        setVal('skCalmar', f2(Math.min(Math.abs(m.calmar), 9.99)), calCls);
        setSub('skCalmarSub', 'ANNUAL / MAX DRAWDOWN');

        /* Total Lots — from real account data when available */
        var accounts = window.DojiStatAccounts || [];
        var totalLots = 0;
        var realHasLots = accounts.some(function (a) { return a.lots > 0; });
        if (realHasLots) {
            accounts.forEach(function (a) { totalLots += (a.lots || 0); });
        } else {
            totalLots = m.nTrades * 0.018; /* ~0.018 lots/trade synthetic estimate */
        }
        var totalFees = totalLots * 3.5; /* ~$3.50/lot round-trip commission estimate */
        setVal('skLots', totalLots.toFixed(2), '');
        setSub('skLotsSub', m.nTrades + ' TRADES');
        setSub('skFeesSub', '~$' + totalFees.toLocaleString('en', { maximumFractionDigits: 0 }) + ' FEES');

        var rrCls = m.avgRR >= 2 ? 'green' : m.avgRR >= 1 ? '' : 'red';
        setVal('skRR', f2(m.avgRR), rrCls);
        var rrTotal   = m.avgWin + m.avgLoss || 1;
        var rrWinFlex = (m.avgWin / rrTotal * 10).toFixed(2);
        var rrLossFlex= (m.avgLoss / rrTotal * 10).toFixed(2);
        var rrGreen = document.getElementById('skRRBarGreen');
        var rrRed   = document.getElementById('skRRBarRed');
        if (rrGreen) rrGreen.style.flex = rrWinFlex;
        if (rrRed)   rrRed.style.flex   = rrLossFlex;
        setSub('skRRAvgWin',  '+$' + Math.round(m.avgWin));
        setSub('skRRAvgLoss', '−$' + Math.round(m.avgLoss));

        /* Best Trade */
        setVal('skBest', fmtPnl(m.bestTrade.pnl), 'green');
        setSub('skBestSub', (m.bestTrade.long ? 'LONG' : 'SHORT') + ' · ' + (m.bestTrade.session || '').toUpperCase());

        /* Worst Trade */
        setVal('skWorst', fmtPnl(m.worstTrade.pnl), 'red');
        setSub('skWorstSub', (m.worstTrade.long ? 'LONG' : 'SHORT') + ' · ' + (m.worstTrade.session || '').toUpperCase());

        /* Total Trades */
        setVal('skTotal', m.nTrades.toString(), '');
        setSub('skTotalSub', m.nWins + ' W · ' + m.nLosses + ' L');

        /* Bias */
        var biasTotal    = m.nLong + m.nShort;
        var biasLongPct  = biasTotal > 0 ? Math.round(m.nLong  / biasTotal * 100) : 50;
        var biasShortPct = 100 - biasLongPct;
        var biasColor    = biasTotal === 0 ? '#999999'
                         : biasLongPct  > 52 ? '#10B981'
                         : biasShortPct > 52 ? '#D71921' : '#999999';
        var biasDir      = biasTotal === 0 ? 'N/A'
                         : biasLongPct > biasShortPct ? 'LONG BIAS'
                         : biasShortPct > biasLongPct ? 'SHORT BIAS' : 'NEUTRAL';
        var biasLongWR   = m.nLong  > 0 ? Math.round(m.nLongWins  / m.nLong  * 100) : null;
        var biasShortWR  = m.nShort > 0 ? Math.round(m.nShortWins / m.nShort * 100) : null;

        var bDirEl = document.getElementById('skBiasDir');
        if (bDirEl) { bDirEl.textContent = biasDir; bDirEl.style.color = biasColor; }
        var bSplitEl = document.getElementById('skBiasSplit');
        if (bSplitEl) bSplitEl.textContent = biasShortPct + '%  ·  ' + biasLongPct + '%';

        var bBarEl = document.getElementById('skBiasBar');
        if (bBarEl) {
            var _bShortSegs = Math.round(20 * biasShortPct / 100);
            var bHtml = '';
            for (var _bi = 0; _bi < 20; _bi++) {
                var bCls = _bi < _bShortSegs ? 'bias-db-seg short' : 'bias-db-seg long';
                if (_bi === 9)  bCls += ' center-l';
                if (_bi === 10) bCls += ' center-r';
                bHtml += '<div class="' + bCls + '"></div>';
            }
            bBarEl.innerHTML = bHtml;
        }

        var bShortNEl  = document.getElementById('skBiasShortN');  if (bShortNEl)  bShortNEl.textContent  = m.nShort;
        var bShortWREl = document.getElementById('skBiasShortWR'); if (bShortWREl) bShortWREl.textContent = biasShortWR !== null ? biasShortWR + '%' : '';
        var bLongNEl   = document.getElementById('skBiasLongN');   if (bLongNEl)   bLongNEl.textContent   = m.nLong;
        var bLongWREl  = document.getElementById('skBiasLongWR');  if (bLongWREl)  bLongWREl.textContent  = biasLongWR  !== null ? biasLongWR  + '%' : '';
    }

    /* ─── Demo account sets (used when no real accounts match) */
    var _DEMO_SETS = {
        'all': [
            { id: 1001, size: 10000,  status: 'active', dailyLossPct: 5,  maxLossPct: 10, dailyLossType: 'static',   maxLossType: 'static' },
            { id: 1002, size: 25000,  status: 'active', dailyLossPct: 5,  maxLossPct: 10, dailyLossType: 'static',   maxLossType: 'static' },
            { id: 3001, size: 50000,  status: 'funded', dailyLossPct: 5,  maxLossPct: 10, dailyLossType: 'trailing', maxLossType: 'trailing' },
            { id: 3002, size: 100000, status: 'funded', dailyLossPct: 5,  maxLossPct: 10, dailyLossType: 'trailing', maxLossType: 'static' },
        ],
        'evaluation': [
            { id: 1001, size: 10000,  status: 'active', dailyLossPct: 5,  maxLossPct: 10, dailyLossType: 'static', maxLossType: 'static' },
            { id: 1002, size: 25000,  status: 'active', dailyLossPct: 5,  maxLossPct: 10, dailyLossType: 'static', maxLossType: 'static' },
        ],
        'funded': [
            { id: 3001, size: 50000,  status: 'funded', dailyLossPct: 5,  maxLossPct: 10, dailyLossType: 'trailing', maxLossType: 'trailing' },
            { id: 3002, size: 100000, status: 'funded', dailyLossPct: 5,  maxLossPct: 10, dailyLossType: 'trailing', maxLossType: 'static' },
        ],
    };

    /* ─── Aggregate data across filtered accounts ─────── */
    function buildData() {
        var accounts     = window.DojiStatAccounts || [];
        var isSingleAcct = _filter.slice(0, 5) === 'acct-';
        var filtered;

        if (_filter === 'evaluation') {
            filtered = accounts.filter(function (a) { return a.status === 'active' || a.status === 'passed'; });
        } else if (_filter === 'funded') {
            filtered = accounts.filter(function (a) { return a.status === 'funded'; });
        } else if (isSingleAcct) {
            var acctId = parseInt(_filter.slice(5));
            filtered = accounts.filter(function (a) { return a.id === acctId; });
            /* demo single-account fallback with unique seed */
            if (!filtered.length) {
                filtered = [{ id: acctId || 3001, size: 50000, status: 'funded', dailyLossPct: 5, maxLossPct: 10, dailyLossType: 'trailing', maxLossType: 'static' }];
            }
        } else {
            filtered = accounts; /* 'all' */
        }

        /* fall back to demo sets if nothing matched */
        if (!filtered.length) {
            filtered = _DEMO_SETS[_filter] || _DEMO_SETS['all'];
        }

        /* merge data across filtered accounts */
        var allTrades  = [];
        var baseEquity = null;
        var baseDates  = null;

        filtered.forEach(function (acct) {
            var d = genData(acct.id * 7919 + 31337, acct.size || 100000, acct.status || 'active');
            allTrades = allTrades.concat(d.trades);
            if (!baseEquity) {
                baseEquity = d.equity.slice();
                baseDates  = d.dates.slice();
            } else {
                for (var i = 0; i < Math.min(baseEquity.length, d.equity.length); i++) {
                    baseEquity[i] += d.equity[i];
                }
            }
        });

        if (!baseEquity) { baseEquity = []; baseDates = []; }

        /* recompute drawdown from combined equity */
        var pk = -Infinity, dd = [];
        baseEquity.forEach(function (v) {
            pk = Math.max(pk, v);
            dd.push(pk > 0 ? -(pk - v) / pk * 100 : 0);
        });

        /* stop levels — only meaningful for single account */
        var totalSize     = 0;
        filtered.forEach(function (a) { totalSize += (a.size || 100000); });
        var dailyLossPct  = filtered[0] ? (filtered[0].dailyLossPct  || 5)  : 5;
        var maxLossPct    = filtered[0] ? (filtered[0].maxLossPct    || 10) : 10;
        var dailyLossType = filtered[0] ? (filtered[0].dailyLossType || 'static') : 'static';
        var maxLossType   = filtered[0] ? (filtered[0].maxLossType   || 'static') : 'static';
        var stopDailyLbl = (dailyLossType === 'trailing' || dailyLossType === 'intraday') ? 'TRAILING' : (dailyLossType === 'eod' ? 'END OF DAY' : 'STATIC');
        var stopMaxLbl   = (maxLossType   === 'trailing' || maxLossType   === 'intraday') ? 'TRAILING' : 'STATIC';

        return {
            trades: allTrades, equity: baseEquity, drawdown: dd, dates: baseDates,
            filteredAccounts: filtered,
            isSingleAcct:  isSingleAcct,
            stopDailyAmt:  (dailyLossPct / 100) * totalSize,
            stopMaxLevel:  -(maxLossPct  / 100) * totalSize,
            stopDailyLbl:  stopDailyLbl,
            stopMaxLbl:    stopMaxLbl,
            accountSize:   totalSize,
        };
    }

    /* ─── Full refresh ──────────────────────────────────── */
    function refresh() {
        var data    = buildData();
        var metrics = computeMetrics(data);
        updateKpis(metrics);
        initCharts(data, metrics);
    }

    /* ─── Drag-and-drop for a card grid ─────────────────── */
    function initDrag(gridId) {
        var grid = document.getElementById(gridId);
        if (!grid) return;

        function getCards() { return Array.from(grid.querySelectorAll('[draggable="true"]')); }

        function bindCard(card) {
            card.addEventListener('dragstart', function (e) {
                _dragSrc = this;
                this.classList.add('stat-dragging');
                e.dataTransfer.effectAllowed = 'move';
            });
            card.addEventListener('dragend', function () {
                this.classList.remove('stat-dragging');
                getCards().forEach(function (c) { c.classList.remove('stat-drag-over'); });
            });
            card.addEventListener('dragover', function (e) {
                e.preventDefault();
                if (this !== _dragSrc) this.classList.add('stat-drag-over');
            });
            card.addEventListener('dragleave', function () { this.classList.remove('stat-drag-over'); });
            card.addEventListener('drop', function (e) {
                e.stopPropagation();
                this.classList.remove('stat-drag-over');
                if (_dragSrc && _dragSrc !== this) {
                    var cards  = getCards();
                    var srcIdx = cards.indexOf(_dragSrc);
                    var tgtIdx = cards.indexOf(this);
                    if (srcIdx < tgtIdx) grid.insertBefore(_dragSrc, this.nextSibling);
                    else                 grid.insertBefore(_dragSrc, this);
                    /* allow charts to resize inside rearranged containers */
                    setTimeout(function () {
                        Object.keys(_charts).forEach(function (k) { if (_charts[k]) _charts[k].resize(); });
                    }, 120);
                }
            });
        }

        getCards().forEach(bindCard);
    }

    /* ─── Lazy-load Chart.js then bootstrap ─────────────── */
    function loadChartJs(cb) {
        if (window.Chart) { cb(); return; }
        _chartJsCbs.push(cb);
        if (_chartJsCbs.length > 1) return; /* already loading — cb queued */
        var s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js';
        s.onload  = function () { var fns = _chartJsCbs.splice(0); fns.forEach(function (fn) { fn(); }); };
        s.onerror = function () { _chartJsCbs = []; console.warn('[StatisticsTab] Chart.js failed to load'); };
        document.head.appendChild(s);
    }

    /* ─── Public: activate tab ──────────────────────────── */
    function activate() {
        if (_ready) { refresh(); return; }
        loadChartJs(function () {
            if (_ready) { refresh(); return; } /* guard: 2nd queued cb */
            applyDefaults();
            refresh();
            initDrag('statKpiGrid');
            initDrag('statChartGrid');
            _ready = true;
        });
    }

    /* ─── Wire up filter + period buttons ──────────────── */
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.stat-filter-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.stat-filter-btn').forEach(function (b) { b.classList.remove('stat-filter-active'); });
                this.classList.add('stat-filter-active');
                _filter = this.getAttribute('data-filter') || 'all';
                activate(); /* always safe: handles both ready and not-yet-ready */
            });
        });

        document.querySelectorAll('.stat-period-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.stat-period-btn').forEach(function (b) { b.classList.remove('stat-period-active'); });
                this.classList.add('stat-period-active');
                _period = parseInt(this.getAttribute('data-period') || '180');
                activate();
            });
        });

        /* Activate when tab becomes visible — robust against LaserBurn delay.
           The click-based approach (setTimeout 60ms) initialised charts on a
           hidden canvas (0×0), causing blank charts. MutationObserver fires
           AFTER the tab's 'active' class is added, when the canvas is visible. */
        var _tabEl = document.getElementById('tab-statistics');
        if (_tabEl) {
            new MutationObserver(function () {
                if (_tabEl.classList.contains('active')) activate();
            }).observe(_tabEl, { attributes: true, attributeFilter: ['class'] });
        }

        /* Populate overview DNA card immediately — no Chart.js needed, pure math */
        (function () {
            var _d   = buildData();
            var _m   = computeMetrics(_d);
            var _dna = dnaScores(_m);
            var _avg = _dna.reduce(function (s, v) { return s + v; }, 0) / _dna.length;
            var _lm  = [
                { min: 9.5, letter: 'S',  color: '#10B981' },
                { min: 8.5, letter: 'A+', color: '#10B981' },
                { min: 7.5, letter: 'A',  color: '#10B981' },
                { min: 6.5, letter: 'B+', color: '#D4A843' },
                { min: 5.5, letter: 'B',  color: '#D4A843' },
                { min: 4.5, letter: 'C+', color: '#D4A843' },
                { min: 3.5, letter: 'C',  color: '#999999' },
                { min: 0,   letter: 'D',  color: '#D71921' },
            ];
            var _dm  = { S: '[ ELITE TRADER ]', 'A+': '[ EXCEPTIONAL ]', A: '[ EXCELLENT ]', 'B+': '[ VERY GOOD ]', B: '[ GOOD ]', 'C+': '[ AVERAGE ]', C: '[ BELOW AVERAGE ]', D: '[ NEEDS IMPROVEMENT ]' };
            var _dg  = _lm.find(function (g) { return _avg >= g.min; }) || _lm[_lm.length - 1];
            var e1 = document.getElementById('ovDnaGrade');
            var e2 = document.getElementById('ovDnaGradeLbl');
            var e3 = document.getElementById('ovDnaGradeDesc');
            if (e1) { e1.textContent = _dg.letter; e1.style.color = _dg.color; }
            if (e2)   e2.textContent = _avg.toFixed(1) + ' / 10';
            if (e3)   e3.textContent = _dm[_dg.letter] || '';
            var _ovSegCls = { '#10B981': 'stat-seg-on', '#D4A843': 'stat-seg-on-amber', '#999999': 'stat-seg-on-gray', '#D71921': 'stat-seg-on-red' }[_dg.color] || 'stat-seg-on';
            setSegs('ovDnaSegs', _avg * 10, _ovSegCls);
        }());

        /* Re-apply Chart.js palette + redraw when data-theme changes on <html> */
        new MutationObserver(function () {
            var tabEl = document.getElementById('tab-statistics');
            if (_ready && window.Chart && tabEl && tabEl.classList.contains('active')) {
                applyDefaults();
                refresh();
            }
        }).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
    });

    return { activate: activate, refresh: refresh };
}());
