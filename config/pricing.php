<?php
/**
 * Doji Funding — Pricing Configuration
 * 
 * All pricing tables, adjustments, and promo codes.
 * This data is output as JSON for the JS configurator.
 */

// $5K–$50K : paliers de $5K | $50K–$100K : paliers de $10K | $100K–$200K : paliers de $25K
$accountSizes = [
    5000, 10000, 15000, 20000, 25000, 30000, 35000, 40000, 45000, 50000,  // $5K steps
    60000, 70000, 80000, 90000, 100000,                                    // $10K steps
    125000, 150000, 175000, 200000,                                        // $25K steps
];

// Ratio prix/taille cible : décroissant de ~0.78% ($5K) → ~0.42% ($200K)
// $100K corrigé à $469 (ratio 0.469%) — était $499 (ratio plat 0.50% = anomalie)
$basePrices = [
    'onestep' => [
        5000=>39,   10000=>59,  15000=>79,  20000=>99,  25000=>119,
        30000=>139, 35000=>159, 40000=>179, 45000=>199, 50000=>249,
        60000=>309, 70000=>369, 80000=>429, 90000=>479, 100000=>469,
        125000=>569, 150000=>669, 175000=>759, 200000=>839,
    ],
    'twostep' => [
        5000=>69,   10000=>89,  15000=>109, 20000=>129, 25000=>149,
        30000=>169, 35000=>189, 40000=>209, 45000=>229, 50000=>249,
        60000=>309, 70000=>349, 80000=>379, 90000=>394, 100000=>399,
        125000=>479, 150000=>559, 175000=>629, 200000=>689,
    ],
];

$promoCodes = [
    'DOJI10'    => ['type' => 'percent', 'value' => 10, 'label' => '10% off'],
    'DOJI20'    => ['type' => 'percent', 'value' => 20, 'label' => '20% off'],
    'LAUNCH25'  => ['type' => 'percent', 'value' => 25, 'label' => '25% Launch Discount'],
    'WELCOME'   => ['type' => 'percent', 'value' => 20, 'label' => '20% Welcome Discount'],
    'TRADER50'  => ['type' => 'fixed',   'value' => 50, 'label' => '$50 off'],
];

/**
 * Pricing adjustment rules (reference for JS logic)
 * 
 * MODIFICATEURS — exprimés en % du base_price (RATES dans configurator.js)
 * Référence de calibration : $50K one-step (bp=$249) → mêmes prix qu'avant à taille égale.
 * Impact proportionnel garanti sur toute la gamme $5K–$200K.
 *
 * ONE STEP (rates × bp) :
 *   - Profit Target < 10%: +8.03% × bp per % below   (≈$20  @$249)
 *   - Profit Target > 12%: −6.02% × bp per % above   (≈$15  @$249)
 *   - Daily Loss 6–7%:     +24.1% × bp per %          (≈$60  @$249) — normal tier
 *   - Daily Loss ≥ 8%:     +40.2% × bp per % above 7 (≈$100 @$249) — deterrent tier
 *   - Daily Loss < 4%:     −12.0% × bp per % below   (≈$30  @$249)
 *   - Max Loss > 10%:      +20.1% × bp per % above   (≈$50  @$249)
 *   - Max Loss < 10%:      −3.35% × bp per % below   (≈$8   @$249) — anchored at industry default 10%
 *   - Min Days < 5:        +6.02% × bp per day below (≈$15  @$249)
 *   - Consistency:         ±0.80% × bp per % vs 40   (≈$2   @$249)
 *   - Split:               ±1.61% × bp per 1% vs 70  (≈$4   @$249)
 *   - Overnight:           +7.63% × bp               (≈$19  @$249)
 *   - Overweek:            +11.6% × bp               (≈$29  @$249)
 *   - Loss type Intraday:  −6.02% × bp per type      (≈$15  @$249)
 *   - Loss type Static:    +10.0% × bp per type      (≈$25  @$249)
 *
 * TWO STEP (rates × bp) :
 *   - Total targets < 10%: +6.02% × bp per % below   (≈$15  @$249)
 *   - Total targets > 12%: −4.02% × bp per % above   (≈$10  @$249)
 *   - Daily Loss 6–7%:     +20.1% × bp per %          (≈$50  @$249) — normal tier
 *   - Daily Loss ≥ 8%:     +32.1% × bp per % above 7 (≈$80  @$249) — deterrent tier
 *   - Daily Loss < 5%:     −10.0% × bp per % below   (≈$25  @$249)
 *   - Max Loss > 10%:      +18.1% × bp per % above   (≈$45  @$249)
 *   - Max Loss < 10%:      −4.02% × bp per % below   (≈$10  @$249) — gentler slope
 *   - Min Days < 10:       +4.02% × bp per day below (≈$10  @$249)
 *   - Consistency:         ±1.20% × bp per % vs 45   (≈$3   @$249)
 *   - Split:               ±2.41% × bp per 1% vs 80  (≈$6   @$249)
 *   - Overnight:           +10.0% × bp               (≈$25  @$249)
 *   - Overweek:            +15.7% × bp               (≈$39  @$249)
 *   - Loss type Intraday:  −7.63% × bp per type      (≈$19  @$249)
 *   - Loss type Static:    +11.6% × bp per type      (≈$29  @$249)
 *
 * COMMON :
 *   - Equal Loss surcharge: +40.2% × bp              (≈$100 @$249)
 *   - Payout Bi-Weekly:     inclus dans le prix de base (décision produit — add-on supprimé)
 *   - Payout Weekly:        +23.7% × bp              (≈$59  @$249)
 *   - Floor: max(calculated, base × 0.5)
 *   - Promo floor: max(discounted, base × 0.3)
 *
 * PALIERS (19 tailles) :
 *   $5K–$50K  : paliers de $5K  (10 tailles)
 *   $50K–$100K: paliers de $10K (5 tailles — $55K/$65K/$75K/$85K/$95K supprimés)
 *   $100K–$200K: paliers de $25K (4 tailles — $125K/$150K/$175K/$200K)
 *
 * ANOMALIES CORRIGÉES :
 *   - $100K one-step : $499 → $469 (ratio 0.469% vs 0.50% — recréation économie d'échelle)
 *   - Modificateurs fixes $ : convertis en % du base_price via RATES dans configurator.js
 *   - Max Loss ancre : 8% → 10% (alignement médiane sectorielle — FTMO, BrightFunded, The 5%ers)
 */

// Export as JSON for JS
function getPricingJson() {
    global $accountSizes, $basePrices, $promoCodes;
    return json_encode([
        'accountSizes' => $accountSizes,
        'basePrices'   => $basePrices,
        'promoCodes'   => $promoCodes,
    ]);
}
