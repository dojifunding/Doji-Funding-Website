<?php
/**
 * Doji Funding — Logout API
 * POST /api/logout.php
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/auth.php';

logoutUser();
jsonResponse(['success' => true]);
