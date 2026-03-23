<?php
/**
 * Doji Funding — Challenge Presets
 * 
 * Competitor configs and affiliate custom presets.
 * Used on the Challenges page only (not homepage configurator).
 *
 * Loss type values: 'intraday' (Intraday/Trailing), 'eod' (End of Day), 'static' (Static)
 */

$challengePresets = [

    [
        'group' => 'Popular Prop Firms',
        'presets' => [
            [
                'id'    => 'ftmo-1step',
                'name'  => 'FTMO — 1 Step',
                'tab'   => 'onestep',
                'config' => [
                    'target' => 10,
                    'daily' => 3, 'max' => 10,
                    'split' => 80, 'days' => 4,
                    'consistency' => 50,
                    'dailyType' => 'static', 'maxType' => 'intraday',
                ],
                'note' => '10% target, 3% daily, 10% max, 50% consistency, 80% split',
            ],
            [
                'id'    => 'ftmo-2step',
                'name'  => 'FTMO — 2 Step',
                'tab'   => 'twostep',
                'config' => [
                    'target1' => 10, 'target2' => 5,
                    'daily' => 5, 'max' => 10,
                    'split' => 80, 'days' => 8,
                    'consistency' => 50,
                    'dailyType' => 'static', 'maxType' => 'static',
                ],
                'note' => '10% + 5% targets, 5% daily, 10% max, 50% consistency, 80% split',
            ],
            [
                'id'    => 'fundingpips-1step',
                'name'  => 'FundingPips — 1 Step',
                'tab'   => 'onestep',
                'config' => [
                    'target' => 10,
                    'daily' => 3, 'max' => 6,
                    'split' => 80, 'days' => 3,
                    'consistency' => 30,
                    'dailyType' => 'eod', 'maxType' => 'static',
                ],
                'note' => '10% target, 3% daily, 6% max, 30% consistency, 80% split',
            ],
            [
                'id'    => 'fundingpips-2step',
                'name'  => 'FundingPips — 2 Step',
                'tab'   => 'twostep',
                'config' => [
                    'target1' => 10, 'target2' => 5,
                    'daily' => 5, 'max' => 10,
                    'split' => 80, 'days' => 6,
                    'consistency' => 30,
                    'dailyType' => 'eod', 'maxType' => 'static',
                ],
                'note' => '10% + 5% targets, 5% daily, 10% max, 30% consistency, 80% split',
            ],
            [
                'id'    => 'fundednext-1step',
                'name'  => 'FundedNext — 1 Step',
                'tab'   => 'onestep',
                'config' => [
                    'target' => 10,
                    'daily' => 3, 'max' => 6,
                    'split' => 80, 'days' => 5,
                    'consistency' => 40,
                    'dailyType' => 'static', 'maxType' => 'static',
                ],
                'note' => '10% target, 3% daily, 6% max, 40% consistency, 80% split',
            ],
            [
                'id'    => 'fundednext-2step',
                'name'  => 'FundedNext — 2 Step',
                'tab'   => 'twostep',
                'config' => [
                    'target1' => 8, 'target2' => 5,
                    'daily' => 5, 'max' => 10,
                    'split' => 80, 'days' => 10,
                    'consistency' => 40,
                    'dailyType' => 'static', 'maxType' => 'static',
                ],
                'note' => '8% + 5% targets, 5% daily, 10% max, 40% consistency, 80% split',
            ],
            [
                'id'    => 'brightfunded-2step',
                'name'  => 'BrightFunded — 2 Step',
                'tab'   => 'twostep',
                'config' => [
                    'target1' => 8, 'target2' => 5,
                    'daily' => 5, 'max' => 10,
                    'split' => 80, 'days' => 10,
                    'consistency' => 40,
                    'dailyType' => 'eod', 'maxType' => 'static',
                ],
                'note' => '8% + 5% targets, 5% daily, 10% max, 40% consistency, 80% split',
            ],
            [
                'id'    => 'e8-1step',
                'name'  => 'E8 Markets — 1 Step',
                'tab'   => 'onestep',
                'config' => [
                    'target' => 9,
                    'daily' => 4, 'max' => 6,
                    'split' => 80, 'days' => 5,
                    'consistency' => 40,
                    'dailyType' => 'static', 'maxType' => 'intraday',
                ],
                'note' => '9% target, 4% daily, 6% max, 40% consistency, 80% split',
            ],
            [
                'id'    => 'alphacapital-1step',
                'name'  => 'Alpha Capital — 1 Step',
                'tab'   => 'onestep',
                'config' => [
                    'target' => 10,
                    'daily' => 4, 'max' => 6,
                    'split' => 80, 'days' => 5,
                    'consistency' => 40,
                    'dailyType' => 'eod', 'maxType' => 'intraday',
                ],
                'note' => '10% target, 4% daily, 6% max, 40% consistency, 80% split',
            ],
            [
                'id'    => 'alphacapital-2step',
                'name'  => 'Alpha Capital — 2 Step',
                'tab'   => 'twostep',
                'config' => [
                    'target1' => 8, 'target2' => 5,
                    'daily' => 4, 'max' => 8,
                    'split' => 80, 'days' => 10,
                    'consistency' => 40,
                    'dailyType' => 'static', 'maxType' => 'static',
                ],
                'note' => '8% + 5% targets, 4% daily, 8% max, 40% consistency, 80% split',
            ],
            [
                'id'    => 'maven-1step',
                'name'  => 'Maven — 1 Step',
                'tab'   => 'onestep',
                'config' => [
                    'target' => 10,
                    'daily' => 3, 'max' => 6,
                    'split' => 70, 'days' => 5,
                    'consistency' => 40,
                    'dailyType' => 'eod', 'maxType' => 'intraday',
                ],
                'note' => '10% target, 3% daily, 6% max, 40% consistency, 70% split',
            ],
            [
                'id'    => 'maven-2step',
                'name'  => 'Maven — 2 Step',
                'tab'   => 'twostep',
                'config' => [
                    'target1' => 8, 'target2' => 5,
                    'daily' => 5, 'max' => 10,
                    'split' => 80, 'days' => 10,
                    'consistency' => 40,
                    'dailyType' => 'eod', 'maxType' => 'static',
                ],
                'note' => '8% + 5% targets, 5% daily, 10% max, 40% consistency, 80% split',
            ],
        ],
    ],

    // ══════════════════════════
    //  AFFILIATE PRESETS
    // ══════════════════════════
    [
        'group' => 'Creator Picks',
        'presets' => [
            // Add affiliate configs here
        ],
    ],
];

function getPresetsJson() {
    global $challengePresets;
    $filtered = array_filter($challengePresets, fn($g) => !empty($g['presets']));
    return json_encode(array_values($filtered));
}
