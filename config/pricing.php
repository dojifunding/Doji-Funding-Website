<?php
/**
 * Doji Funding — Pricing Configuration
 * 
 * All pricing tables, adjustments, and promo codes.
 * This data is output as JSON for the JS configurator.
 */

$accountSizes = [5000,10000,15000,20000,25000,30000,35000,40000,45000,50000,
                 55000,60000,65000,70000,75000,80000,85000,90000,95000,100000];

$basePrices = [
    'onestep' => [
        5000=>39, 10000=>59, 15000=>79, 20000=>99, 25000=>119,
        30000=>139, 35000=>159, 40000=>179, 45000=>199, 50000=>249,
        55000=>279, 60000=>309, 65000=>339, 70000=>369, 75000=>399,
        80000=>429, 85000=>459, 90000=>479, 95000=>489, 100000=>499
    ],
    'twostep' => [
        5000=>69, 10000=>89, 15000=>109, 20000=>129, 25000=>149,
        30000=>169, 35000=>189, 40000=>209, 45000=>229, 50000=>249,
        55000=>279, 60000=>309, 65000=>329, 70000=>349, 75000=>369,
        80000=>379, 85000=>389, 90000=>394, 95000=>397, 100000=>399
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
 * ONE STEP:
 *   - Profit Target < 10%: +$20 per % below
 *   - Profit Target > 12%: -$15 per % above
 *   - Daily Loss > 5%: +$60 per % above
 *   - Daily Loss < 4%: -$30 per % below
 *   - Max Loss > 8%: +$50 per % above
 *   - Max Loss < 6%: -$25 per % below
 *   - Min Days < 5: +$15 per day below
 *   - Split adjustment: (split - 70) * $4
 *   - Overnight: +$19 | Overweek: +$29
 *   - Loss type Intraday: -$15 | Static: +$25
 * 
 * TWO STEP:
 *   - Total targets < 10%: +$15 per % below
 *   - Total targets > 12%: -$10 per % above
 *   - Daily Loss > 5%: +$50 per % above
 *   - Daily Loss < 5%: -$25 per % below
 *   - Max Loss > 10%: +$45 per % above
 *   - Max Loss < 10%: -$20 per % below
 *   - Min Days < 10: +$10 per day below
 *   - Split adjustment: (split - 80) * $6
 *   - Overnight: +$25 | Overweek: +$39
 *   - Loss type Intraday: -$19 | Static: +$29
 * 
 * COMMON:
 *   - Equal Loss surcharge (daily == max): +$100
 *   - Payout Bi-Weekly: +$29 | Weekly: +$59
 *   - Floor: max(calculated, base * 0.5)
 *   - Promo floor: max(discounted, base * 0.3)
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
