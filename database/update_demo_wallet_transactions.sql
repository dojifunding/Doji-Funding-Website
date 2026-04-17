-- ══════════════════════════════════════════════════════════════
--  Demo data — Wallet Transactions + Coins Log (user_id = 2)
-- ══════════════════════════════════════════════════════════════

-- Set wallet balance to match transactions: 1200 - 299 + 850 = 1751
UPDATE `users` SET `wallet_balance` = 1751.00 WHERE `id` = 2;

-- Wallet movements: 2 credits (payout transfers) + 1 debit (eval purchase)
INSERT INTO `wallet_transactions` (`user_id`, `type`, `amount`, `description`, `reference_id`, `created_at`) VALUES
(2, 'payout_transfer',    1200.00, 'Payout transfer — Funded #1042 ($100K)',  3,  NOW() - INTERVAL 12 DAY),
(2, 'challenge_purchase',  -299.00, 'Evaluation purchase — $25K Standard',  NULL, NOW() - INTERVAL 5 DAY),
(2, 'payout_transfer',     850.00, 'Payout transfer — Funded #1058 ($50K)',   4,  NOW() - INTERVAL 2 DAY);

-- Coins log: 3 days of earnings (today + 2 previous days)
INSERT INTO `coins_log` (`user_id`, `amount`, `source`, `description`, `created_at`) VALUES
(2, 14, 'volume', 'Volume reward',  NOW()),
(2,  3, 'bonus',  'Daily bonus',    NOW() - INTERVAL 2 HOUR),
(2, 22, 'volume', 'Volume reward',  NOW() - INTERVAL 1 DAY),
(2,  8, 'volume', 'Volume reward',  NOW() - INTERVAL 2 DAY);
