<?php
/**
 * Doji Funding — FAQ Data
 * 
 * All FAQ categories and Q&A pairs.
 * Used by both PHP rendering and JS interactivity.
 * Each Q&A generates FAQPage schema for Google rich results.
 */

$faqCategories = [
    [
        'title' => 'General Information',
        'icon'  => 'info',
        'questions' => [
            ['q' => 'What is Doji Funding®?', 'a' => 'Doji Funding® is a proprietary trading firm offering fully customizable funded accounts from $5K to $100K.'],
            ['q' => 'How does a prop firm work?', 'a' => 'We provide trading capital after you pass an evaluation. You keep up to 90% of profits with no personal risk.'],
            ['q' => 'What markets are available?', 'a' => 'Forex, indices, commodities, metals, and crypto — over 150 instruments across all major markets.'],
            ['q' => 'What trading platforms are supported?', 'a' => 'MetaTrader 5 and cTrader, both fully supported with all features.'],
        ]
    ],
    [
        'title' => 'Challenges & Evaluation',
        'icon'  => 'target',
        'questions' => [
            ['q' => 'What account sizes do you offer?', 'a' => '20 sizes from $5,000 to $100,000 in $5K increments. Each with fully customizable parameters.'],
            ['q' => 'How many challenge phases are there?', 'a' => '1 Step (single evaluation) or 2 Step (two-phase validation). Choose what suits your style.'],
            ['q' => 'What are the profit targets?', 'a' => '1 Step: 5-15% customizable. 2 Step: Phase 1 (5-12%) + Phase 2 (3-8%), all configurable.'],
            ['q' => 'Is there a time limit?', 'a' => 'No hard time limit. You need minimum trading days (3-20 depending on config) but no maximum deadline.'],
        ]
    ],
    [
        'title' => 'Trading Rules',
        'icon'  => 'rules',
        'questions' => [
            ['q' => 'What is the maximum daily loss?', 'a' => 'Customizable from 2-8% depending on your challenge type and configuration.'],
            ['q' => 'How are losses calculated?', 'a' => 'Three options: Intraday (real-time), End of Day (session close), or Static (start-of-day balance).'],
            ['q' => 'Can I trade during news events?', 'a' => 'Depends on your configuration. Generally allowed with buffer restrictions for high-risk setups.'],
            ['q' => 'Are EAs and bots allowed?', 'a' => 'Yes, Expert Advisors are allowed on both platforms. HFT strategies are restricted.'],
        ]
    ],
    [
        'title' => 'Payouts & Profits',
        'icon'  => 'wallet',
        'questions' => [
            ['q' => 'What is the profit split?', 'a' => 'Customizable: 50-90% for 1 Step, 70-90% for 2 Step. You choose your split at configuration.'],
            ['q' => 'When can I request a payout?', 'a' => 'After meeting minimum profit thresholds: 2% (1 Step) or 1% (2 Step) of account size.'],
            ['q' => 'What payout frequencies are available?', 'a' => 'Monthly (included), Bi-Weekly (+$29), or Weekly (+$59). Selected during challenge configuration.'],
            ['q' => 'What payment methods are accepted?', 'a' => 'Bank transfer, crypto, and major e-wallets. Processed within 24-48 business hours.'],
        ]
    ],
    [
        'title' => 'Scaling & Progression',
        'icon'  => 'chart',
        'questions' => [
            ['q' => 'How does the scaling plan work?', 'a' => '1 Step: up to 5× scaling. 2 Step: up to 10× scaling. Based on consistent profitability milestones.'],
            ['q' => 'Does the profit split change?', 'a' => 'Split can increase through the trader level system as you prove consistent performance.'],
        ]
    ],
    [
        'id'    => 'doji-coins',
        'title' => 'Doji Coins™ Loyalty Program',
        'icon'  => 'doji-logo',
        'questions' => [
            ['q' => 'What are Doji Coins™?', 'a' => 'Doji Coins™ are Doji Funding\'s loyalty rewards. Every trade you execute — win or lose — earns you Doji Coins that accumulate in your dashboard wallet. You can spend them on free challenges, boosted profit splits, reduced fees, and exclusive perks.'],
            ['q' => 'How do I earn Doji Coins™?', 'a' => 'You earn 10 Doji Coins per standard lot traded. Both evaluation and funded phases participate in the program. The more you trade, the more you earn — regardless of trade outcome.'],
            ['q' => 'Are there bonus multipliers?', 'a' => 'Yes! During the funded phase you earn at 2× rate (20 DC/lot). Achieving a streak of 5 consecutive profitable trading days activates a 3× bonus (30 DC/lot). Elite-level traders enjoy a permanent 5× multiplier (50 DC/lot).'],
            ['q' => 'What can I spend Doji Coins™ on?', 'a' => 'Free challenge accounts (redeem a full evaluation at no cost), profit split boosts (+5% or +10% on your next payout), fee discounts on future challenges, and exclusive merchandise from the Doji Funding store.'],
            ['q' => 'Do Doji Coins™ expire?', 'a' => 'No. Your Doji Coins™ balance never expires as long as your account remains active. They stay in your wallet until you choose to spend them.'],
            ['q' => 'Where can I see my Doji Coins™ balance?', 'a' => 'Your balance is displayed in real-time on your trader dashboard. You\'ll also see a detailed history of all earnings and redemptions.'],
            ['q' => 'How many Doji Coins™ do I need for a free challenge?', 'a' => 'Redemption costs vary by account size. For example, a $10K 1-Step challenge requires approximately 3,200 DC, while a $50K account requires around 12,000 DC. Check the rewards section in your dashboard for current rates.'],
        ]
    ],
    [
        'id'    => 'competitions',
        'title' => 'Competitions',
        'icon'  => 'trophy',
        'questions' => [
            ['q' => 'How do I join a competition?', 'a' => 'Create a free Doji Funding account, then navigate to the Competitions page. For Free Competitions, simply click "Join" — no payment required. For Paid Competitions, select your entry and complete the payment. You\'ll receive your competition trading credentials instantly.'],
            ['q' => 'Is the competition free to enter?', 'a' => 'We offer two types: the Free Competition has no entry fee at all, and the Paid Competition requires an entry fee starting from $49 with a significantly larger prize pool.'],
            ['q' => 'What are the competition trading rules?', 'a' => 'Both competitions have no daily loss limit, a 10% static overall drawdown, and allow news trading and overnight/overweek holding. EA and bots are not permitted. Free competitions require 5 minimum trading days, paid require 10.'],
            ['q' => 'How is the leaderboard ranked?', 'a' => 'The leaderboard is ranked by percentage return on the simulated account. It is live 24/7 and visible to all users. Top traders are reviewed for consistency and rule compliance before prizes are awarded.'],
            ['q' => 'Do I need KYC to participate?', 'a' => 'No KYC is required to join or participate. However, KYC verification is mandatory before any prize can be distributed. We recommend completing KYC early in your dashboard to avoid delays.'],
            ['q' => 'Can I re-enter a competition?', 'a' => 'Free Competition: unlimited re-entry. Paid Competition: unlimited re-entry at a 50% discounted fee. Only one active entry per person is allowed at any time.'],
            ['q' => 'When are winners announced?', 'a' => 'Winners are announced during the first week following the competition\'s end date. All top-ranked traders are reviewed for consistency and rule compliance. Results are final and binding.'],
        ]
    ],
    [
        'title' => 'Orders & Billing',
        'icon'  => 'card',
        'questions' => [
            ['q' => 'What payment methods do you accept?', 'a' => 'We accept Visa, Mastercard, Apple Pay, Google Pay, Skrill, and cryptocurrency payments including Bitcoin (BTC), Ethereum (ETH), Tether (USDT), USD Coin (USDC), and Litecoin (LTC). Available methods may vary by region.'],
            ['q' => 'Is my payment secure?', 'a' => 'Yes. All card transactions are processed through PCI DSS-compliant payment providers with 256-bit SSL encryption. Crypto payments are processed on-chain with confirmation verification.'],
            ['q' => 'Can I pay with cryptocurrency?', 'a' => 'Absolutely. We accept BTC, ETH, USDT (ERC-20 & TRC-20), USDC, and LTC. Crypto payments are typically confirmed within 10-30 minutes depending on the network.'],
            ['q' => 'Do you offer refunds?', 'a' => 'Challenge fees are non-refundable once the evaluation has started. If you haven\'t begun trading, please contact support within 24 hours of purchase for assistance.'],
            ['q' => 'Will I be charged recurring fees?', 'a' => 'No. Challenge fees are one-time payments. There are no monthly subscriptions, hidden fees, or recurring charges. You only pay once per challenge.'],
            ['q' => 'How long does payment processing take?', 'a' => 'Card and e-wallet payments are instant. Crypto payments require network confirmations (typically 10-30 minutes). Your account is activated immediately upon confirmed payment.'],
        ]
    ],
];

// Export as JSON for JS
function getFaqJson() {
    global $faqCategories;
    return json_encode($faqCategories);
}
