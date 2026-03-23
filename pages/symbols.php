<?php
/**
 * Doji Funding — Symbols / Instruments Page
 * Data sourced from GBE Prime Product Specifications v4.58
 */
?>

<section class="section" style="padding-top:48px">
    <div class="section-inner">
        <h1 class="page-title">Trading <span class="green">Instruments</span></h1>
        <p class="page-subtitle">1,000+ instruments across 8 asset classes. Institutional spreads from 0.0 pips, deep liquidity, and professional-grade execution.</p>
        <div style="height:48px"></div>

        <div class="symbol-tabs" id="symbolTabs">
            <button class="symbol-tab active" onclick="showSymbolCat('forex', this)">Forex <span class="symbol-tab-count">60+</span></button>
            <button class="symbol-tab" onclick="showSymbolCat('indices', this)">Indices <span class="symbol-tab-count">13</span></button>
            <button class="symbol-tab" onclick="showSymbolCat('metals', this)">Metals <span class="symbol-tab-count">6</span></button>
            <button class="symbol-tab" onclick="showSymbolCat('energies', this)">Energies <span class="symbol-tab-count">2</span></button>
            <button class="symbol-tab" onclick="showSymbolCat('futures', this)">Futures <span class="symbol-tab-count">10+</span></button>
            <button class="symbol-tab" onclick="showSymbolCat('crypto', this)">Crypto <span class="symbol-tab-count">15</span></button>
            <button class="symbol-tab" onclick="showSymbolCat('stocks', this)">Stocks <span class="symbol-tab-count">800+</span></button>
        </div>

        <div class="section-divider"></div>

        <div style="margin:24px 0 16px;position:relative;max-width:400px">
            <input type="text" id="symbolSearch" class="form-input" placeholder="Search instruments..." style="padding-left:36px;background:var(--bg3);border:1px solid var(--border)" oninput="filterSymbols(this.value)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--text3)" stroke-width="2" style="position:absolute;left:12px;top:50%;transform:translateY(-50%)"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </div>

        <!-- FOREX -->
        <div class="symbol-cat active" id="cat-forex">
            <h3 style="margin-bottom:16px">Forex Pairs <span style="color:var(--text3);font-size:14px;font-weight:400">— 60+ Major, Minor & Exotic pairs from 0.0 pips</span></h3>
            <div class="symbol-grid">
                <?php
                $forexMajor = ['EURUSD','GBPUSD','USDJPY','USDCHF','AUDUSD','USDCAD','NZDUSD'];
                $forexMinor = ['EURGBP','EURJPY','GBPJPY','EURCHF','EURAUD','GBPAUD','AUDNZD','AUDJPY','AUDCAD','AUDCHF','NZDJPY','NZDCAD','NZDCHF','GBPNZD','GBPCAD','GBPCHF','EURCAD','EURNZD','CADJPY','CADCHF','CHFJPY','AUDSGD','GBPSGD','EURSGD','NZDSGD'];
                $forexExotic = ['USDMXN','USDTRY','USDZAR','USDSGD','USDHKD','USDNOK','USDSEK','USDPLN','USDCZK','USDDKK','USDHUF','USDCNH','EURTRY','EURNOK','EURSEK','EURPLN','EURDKK','EURHUF','EURCZK','EURMXN','GBPNOK','GBPSEK','GBPPLN','GBPZAR','GBPDKK','HKDJPY','MXNJPY','NOKJPY','PLNJPY','SEKJPY','SGDJPY','TRYJPY','ZARJPY'];
                foreach ($forexMajor as $s): ?>
                    <div class="symbol-item major"><span class="symbol-name"><?= $s ?></span><span class="symbol-tag tag-major">Major</span></div>
                <?php endforeach;
                foreach ($forexMinor as $s): ?>
                    <div class="symbol-item"><span class="symbol-name"><?= $s ?></span><span class="symbol-tag tag-minor">Minor</span></div>
                <?php endforeach;
                foreach ($forexExotic as $s): ?>
                    <div class="symbol-item"><span class="symbol-name"><?= $s ?></span><span class="symbol-tag tag-exotic">Exotic</span></div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- INDICES -->
        <div class="symbol-cat" id="cat-indices">
            <h3 style="margin-bottom:16px">Indices <span style="color:var(--text3);font-size:14px;font-weight:400">— Global Cash Indices</span></h3>
            <div class="symbol-grid">
                <?php $indices = [
                    'DE40.c' => 'Germany 40 (DAX)', 'USTEC.c' => 'NASDAQ 100', 'US500.c' => 'S&P 500',
                    'DJ30.c' => 'Dow Jones 30', 'UK100.c' => 'FTSE 100', 'F40.c' => 'France 40 (CAC)',
                    'JP225.c' => 'Nikkei 225', 'STOXX50.c' => 'Euro Stoxx 50', 'ES35.c' => 'Spain 35 (IBEX)',
                    'SWI20.c' => 'Switzerland 20 (SMI)', 'HK50.c' => 'Hong Kong 50', 'NE25.c' => 'Netherlands 25 (AEX)',
                    'US2000.c' => 'US Small Cap 2000',
                ];
                foreach ($indices as $sym => $name): ?>
                    <div class="symbol-item"><span class="symbol-name"><?= $sym ?></span><span class="symbol-desc"><?= $name ?></span></div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- METALS -->
        <div class="symbol-cat" id="cat-metals">
            <h3 style="margin-bottom:16px">Metals <span style="color:var(--text3);font-size:14px;font-weight:400">— Precious Metals CFDs (Gold margin 5%, others 10%)</span></h3>
            <div class="symbol-grid">
                <?php $metals = [
                    'XAUUSD' => 'Gold vs USD', 'XAUEUR' => 'Gold vs EUR',
                    'XAGUSD' => 'Silver vs USD', 'XAGEUR' => 'Silver vs EUR',
                    'XPTUSD' => 'Platinum vs USD', 'XPDUSD' => 'Palladium vs USD',
                ];
                foreach ($metals as $sym => $name): ?>
                    <div class="symbol-item"><span class="symbol-name"><?= $sym ?></span><span class="symbol-desc"><?= $name ?></span></div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ENERGIES -->
        <div class="symbol-cat" id="cat-energies">
            <h3 style="margin-bottom:16px">Energies <span style="color:var(--text3);font-size:14px;font-weight:400">— Oil & Energy Markets</span></h3>
            <div class="symbol-grid">
                <?php $energies = [
                    'USOIL.c' => 'WTI Crude Oil Cash', 'UKOIL.c' => 'Brent Crude Oil Cash',
                ];
                foreach ($energies as $sym => $name): ?>
                    <div class="symbol-item"><span class="symbol-name"><?= $sym ?></span><span class="symbol-desc"><?= $name ?></span></div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- FUTURES -->
        <div class="symbol-cat" id="cat-futures">
            <h3 style="margin-bottom:16px">Futures <span style="color:var(--text3);font-size:14px;font-weight:400">— CFDs on Futures with Expiry</span></h3>
            <div class="symbol-grid">
                <?php $futures = [
                    'DE40.Exp' => 'Germany 40 Future', 'US500.Exp' => 'S&P 500 Future',
                    'USTEC.Exp' => 'NASDAQ 100 Future', 'USOIL.Exp' => 'WTI Crude Oil Future',
                    'UKBRENT.Exp' => 'Brent Oil Future', 'XAUUSD.Exp' => 'Gold Future',
                    'DX.Exp' => 'US Dollar Index Future', 'FLG.Exp' => 'Euro Bond Future',
                    'NGAS.Exp' => 'Natural Gas Future', 'SB.Exp' => 'Sugar Future',
                ];
                foreach ($futures as $sym => $name): ?>
                    <div class="symbol-item"><span class="symbol-name"><?= $sym ?></span><span class="symbol-desc"><?= $name ?></span></div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- CRYPTO -->
        <div class="symbol-cat" id="cat-crypto">
            <h3 style="margin-bottom:16px">Crypto <span style="color:var(--text3);font-size:14px;font-weight:400">— 15 Digital Assets CFDs</span></h3>
            <div class="symbol-grid">
                <?php $crypto = [
                    'BTCUSD' => 'Bitcoin', 'ETHUSD' => 'Ethereum', 'SOLUSD' => 'Solana',
                    'LTCUSD' => 'Litecoin', 'XRPUSD' => 'Ripple', 'BCHUSD' => 'Bitcoin Cash',
                    'ADAUSD' => 'Cardano', 'DOGUSD' => 'Dogecoin', 'DOTUSD' => 'Polkadot',
                    'EOSUSD' => 'EOS', 'LNKUSD' => 'Chainlink', 'XLMUSD' => 'Stellar',
                    'MTCUSD' => 'Polygon (MATIC)', 'AVEUSD' => 'AAVE', 'BNBUSD' => 'Binance Coin',
                ];
                foreach ($crypto as $sym => $name): ?>
                    <div class="symbol-item"><span class="symbol-name"><?= $sym ?></span><span class="symbol-desc"><?= $name ?></span></div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- STOCKS -->
        <div class="symbol-cat" id="cat-stocks">
            <h3 style="margin-bottom:16px">Stocks <span style="color:var(--text3);font-size:14px;font-weight:400">— 800+ US & European Equities (CFDs) — 10% margin, 0.2% commission</span></h3>
            <p style="color:var(--text3);font-size:13px;margin-bottom:20px">A selection of our most popular stock CFDs. Over 800 equities available including US (NASDAQ/NYSE) and European (XETRA, Euronext Paris, Euronext Amsterdam) markets.</p>

            <h4 style="color:var(--green);font-size:12px;font-weight:600;letter-spacing:0.08em;margin-bottom:12px;font-family:var(--font-heading)">US STOCKS</h4>
            <div class="symbol-grid" style="margin-bottom:28px">
                <?php $usStocks = [
                    'AAPL.OQ' => 'Apple', 'MSFT.OQ' => 'Microsoft', 'GOOGL.OQ' => 'Alphabet',
                    'AMZN.OQ' => 'Amazon', 'TSLA.OQ' => 'Tesla', 'META.OQ' => 'Meta Platforms',
                    'NVDA.OQ' => 'NVIDIA', 'NFLX.OQ' => 'Netflix', 'AMD.OQ' => 'AMD',
                    'DIS.OQ' => 'Walt Disney', 'BA.OQ' => 'Boeing', 'INTC.OQ' => 'Intel',
                    'JPM.OQ' => 'JP Morgan', 'V.OQ' => 'Visa', 'KO.OQ' => 'Coca-Cola',
                    'NKE.OQ' => 'Nike', 'PYPL.OQ' => 'PayPal', 'CRM.OQ' => 'Salesforce',
                    'UBER.OQ' => 'Uber', 'SQ.OQ' => 'Block (Square)', 'COIN.OQ' => 'Coinbase',
                    'CRWD.OQ' => 'CrowdStrike', 'SNOW.OQ' => 'Snowflake', 'CRSP.OQ' => 'CRISPR',
                ];
                foreach ($usStocks as $sym => $name): ?>
                    <div class="symbol-item"><span class="symbol-name"><?= $sym ?></span><span class="symbol-desc"><?= $name ?></span></div>
                <?php endforeach; ?>
            </div>

            <h4 style="color:var(--green);font-size:12px;font-weight:600;letter-spacing:0.08em;margin-bottom:12px;font-family:var(--font-heading)">EUROPEAN STOCKS</h4>
            <div class="symbol-grid">
                <?php $euStocks = [
                    'SAPG.DE' => 'SAP SE', 'ADSGn.DE' => 'Adidas', 'BMWG.DE' => 'BMW',
                    'DAIGn.DE' => 'Mercedes-Benz', 'ALVG.DE' => 'Allianz', 'BASFn.DE' => 'BASF',
                    'BAYGn.DE' => 'Bayer', 'DBKGn.DE' => 'Deutsche Bank', 'IFXGn.DE' => 'Infineon',
                    'LHAG.DE' => 'Lufthansa', 'PSHG_p.DE' => 'Porsche', 'PUMG.DE' => 'Puma',
                    'BOSS.DE' => 'Hugo Boss', 'RWEG.DE' => 'RWE', 'EONGn.DE' => 'E.ON',
                    'LVMH.PA' => 'LVMH', 'AIR.PA' => 'Airbus', 'OREP.PA' => "L'Oreal",
                    'BNPP.PA' => 'BNP Paribas', 'HRMS.PA' => 'Hermes', 'DANO.PA' => 'Danone',
                    'HEIN.AS' => 'Heineken',
                ];
                foreach ($euStocks as $sym => $name): ?>
                    <div class="symbol-item"><span class="symbol-name"><?= $sym ?></span><span class="symbol-desc"><?= $name ?></span></div>
                <?php endforeach; ?>
            </div>
            <p style="color:var(--text3);font-size:12px;margin-top:16px;text-align:center">Showing a selection of 800+ available stock CFDs. Full list available on your trading platform.</p>
        </div>

        <div style="height:32px"></div>
        <p style="text-align:center;color:var(--text3);font-size:13px">
            Instrument availability may vary by platform and account type. Spreads and conditions are subject to market liquidity.<br>
            Data based on GBE Prime Product Specifications v4.58. For the full symbol specification, refer to your trading platform.
        </p>
    </div>
</section>

<script>
function showSymbolCat(cat, btn) {
    document.querySelectorAll('.symbol-cat').forEach(c => c.classList.remove('active'));
    document.querySelectorAll('.symbol-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('cat-' + cat).classList.add('active');
    btn.classList.add('active');
}

function filterSymbols(query) {
    query = query.toLowerCase();
    document.querySelectorAll('.symbol-item').forEach(function(item) {
        var text = item.textContent.toLowerCase();
        item.style.display = text.includes(query) ? '' : 'none';
    });
}
</script>

<div class="section-divider"></div>

<?php include 'includes/community.php'; ?>
