/**
 * Doji Funding — Market Intelligence Panel
 * Fetches AI multi-agent market analysis and renders in the Calendar tab.
 */
const MarketOverview = (function () {

    const REGIME_LABELS = {
        RISK_ON:  'RISK ON',
        RISK_OFF: 'RISK OFF',
        NEUTRAL:  'NEUTRAL',
    };

    const REGIME_COLOR = {
        RISK_ON:  '#10B981',
        RISK_OFF: '#F43F5E',
        NEUTRAL:  '#F59E0B',
    };

    const REGIME_SEG_CLASS = {
        RISK_ON:  'mkt-seg-green',
        RISK_OFF: 'mkt-seg-red',
        NEUTRAL:  'mkt-seg-amber',
    };

    var SEG_COUNT      = 20;
    var MINI_SEG_COUNT = 10;

    function init() {
        load(false);
    }

    function refresh() {
        var btn = document.getElementById('mktRefresh');
        if (btn) btn.disabled = true;
        load(true, function () {
            if (btn) btn.disabled = false;
        });
    }

    function load(force, done) {
        showLoading();
        var url = 'api/market-overview.php' + (force ? '?refresh=1' : '');
        fetch(url, { credentials: 'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) render(data);
                else showError(data.error || 'Analysis unavailable.');
                if (done) done();
            })
            .catch(function () {
                showError('Network error — check your connection.');
                if (done) done();
            });
    }

    function render(data) {
        var regime     = data.regime  || 'NEUTRAL';
        var conviction = data.conviction != null ? data.conviction : 50;

        // Update panel accent color CSS variable
        var panel = document.getElementById('mktPanel');
        if (panel) panel.style.setProperty('--mkt-c', REGIME_COLOR[regime] || '#10B981');

        // Regime label
        var regimeEl = document.getElementById('mktRegime');
        if (regimeEl) regimeEl.textContent = REGIME_LABELS[regime] || regime.replace('_', ' ');

        // Conviction segmented bar (20 segments)
        var segsEl   = document.getElementById('mktConvictionSegs');
        if (segsEl) {
            var segColor = REGIME_SEG_CLASS[regime] || 'mkt-seg-green';
            var filled   = Math.round((conviction / 100) * SEG_COUNT);
            segsEl.innerHTML = '';
            for (var i = 0; i < SEG_COUNT; i++) {
                var seg = document.createElement('div');
                seg.className = 'mkt-seg' + (i < filled ? ' on ' + segColor : '');
                segsEl.appendChild(seg);
            }
        }

        // Conviction score
        setText('mktConvictionScore', conviction + '%');

        // Reasoning
        setText('mktReasoning', data.reasoning || '—');

        // Timestamp
        var ts = data.generated_at || '';
        setText('mktAge', ts ? 'TODAY ' + ts : 'JUST NOW');

        // Stale badge
        var staleEl = document.getElementById('mktStaleBadge');
        if (staleEl) staleEl.style.display = data.stale ? 'inline' : 'none';

        // Agent cards
        renderAgents(data.agents || []);

        showContent();
    }

    function renderAgents(agents) {
        var grid = document.getElementById('mktAgentsGrid');
        if (!grid) return;
        grid.innerHTML = '';
        agents.forEach(function (a) {
            var opinion = (a.opinion || 'NEUTRAL').toUpperCase();
            var slug    = opinion.toLowerCase();
            var conf    = a.confidence || 0;
            var filled  = Math.round((conf / 100) * MINI_SEG_COUNT);

            var miniHTML = '<div class="mkt-agent-mini-bar">';
            for (var i = 0; i < MINI_SEG_COUNT; i++) {
                miniHTML += '<div class="mkt-agent-mini-seg' + (i < filled ? ' on' : '') + '"></div>';
            }
            miniHTML += '</div>';

            var card = document.createElement('div');
            card.className = 'mkt-agent-card mkt-op-' + slug;
            card.innerHTML =
                '<div class="mkt-agent-name">' + esc(a.name || '') + '</div>'
                + '<div class="mkt-agent-opinion">' + opinion + '</div>'
                + '<div class="mkt-agent-conf">' + conf + '<span class="mkt-agent-pct">%</span></div>'
                + miniHTML
                + '<div class="mkt-agent-summary">' + esc(a.summary || '') + '</div>';
            grid.appendChild(card);
        });
    }

    function setText(id, val) {
        var el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    function esc(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function showLoading() {
        setVis('mktLoading', true);
        setVis('mktContent', false);
        setVis('mktError',   false);
    }

    function showContent() {
        setVis('mktLoading', false);
        setVis('mktContent', true);
        setVis('mktError',   false);
    }

    function showError(msg) {
        setVis('mktLoading', false);
        setVis('mktContent', false);
        setVis('mktError',   true);
        setText('mktErrorTxt', msg);
    }

    function setVis(id, visible) {
        var el = document.getElementById(id);
        if (el) el.style.display = visible ? '' : 'none';
    }

    return { init: init, refresh: refresh };
})();
