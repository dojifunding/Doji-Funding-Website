<?php
/**
 * Doji Funding — Database Configuration
 * 
 * MySQL connection for InfinityFree hosting.
 * Update credentials from your InfinityFree Control Panel → MySQL Databases.
 */

// ══════════════════════════════════════════
//  ⚠️  UPDATE THESE WITH YOUR INFINITYFREE CREDENTIALS
// ══════════════════════════════════════════
define('DB_HOST', 'sql123.infinityfree.com');   // Your MySQL host (from control panel)
define('DB_NAME', 'if0_XXXXXXX_doji');          // Your database name
define('DB_USER', 'if0_XXXXXXX');               // Your database username
define('DB_PASS', 'your_password_here');         // Your database password

// ══════════════════════════════════════════

/**
 * Get PDO database connection
 * Uses singleton pattern to avoid multiple connections
 */
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE  => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES    => false,
                ]
            );
        } catch (PDOException $e) {
            // In production, log this instead of displaying
            error_log('Database connection failed: ' . $e->getMessage());
            return null;
        }
    }
    return $pdo;
}
