<?php
/**
 * Doji Funding — Database Configuration
 *
 * MySQL connection for InfinityFree hosting.
 * Supports .env file for credentials (no Composer required).
 * Falls back to hardcoded defaults if .env is not present.
 */

// ══════════════════════════════════════════
//  Load .env if it exists (simple parser, no Composer needed)
// ══════════════════════════════════════════
$__envFile = __DIR__ . '/../.env';
if (file_exists($__envFile)) {
    $__lines = file($__envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($__lines as $__line) {
        $__line = trim($__line);
        // Skip comments
        if ($__line === '' || $__line[0] === '#') {
            continue;
        }
        if (strpos($__line, '=') !== false) {
            list($__key, $__val) = array_map('trim', explode('=', $__line, 2));
            $_ENV[$__key] = $__val;
        }
    }
    unset($__lines, $__line, $__key, $__val);
}
unset($__envFile);

// ══════════════════════════════════════════
//  Database credentials (.env values override defaults)
// ══════════════════════════════════════════
define('DB_HOST', $_ENV['DB_HOST'] ?? 'sql104.infinityfree.com');   // Your MySQL host
define('DB_NAME', $_ENV['DB_NAME'] ?? 'if0_41197205_dojifunding');          // Your database name
define('DB_USER', $_ENV['DB_USER'] ?? 'if0_41197205');               // Your database username
define('DB_PASS', $_ENV['DB_PASS'] ?? '9ECz5NjmHOk');         // Your database password

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
