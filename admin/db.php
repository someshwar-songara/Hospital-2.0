<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'apex_hospital');
// If MySQL runs on a non-default port (e.g. 3307), change DB_PORT below
define('DB_PORT', 3306);

// Connect without selecting a database first, so we can show a helpful message
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, '', DB_PORT);

if ($conn->connect_error) {
    die('<div style="font-family:sans-serif;padding:40px;color:#c00;">
        <h2>❌ Database Server Unreachable</h2>
        <p>' . htmlspecialchars($conn->connect_error) . '</p>
        <p>Make sure <strong>MySQL is running</strong> in XAMPP Control Panel.</p>
    </div>');
}

$conn->set_charset('utf8mb4');

// Check if database exists; if not, redirect to setup
$dbCheck = $conn->query("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = '" . DB_NAME . "'");
if ($dbCheck->num_rows === 0) {
    // Not in CLI context — show setup link
    if (php_sapi_name() !== 'cli') {
        $setupUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
            . '://' . $_SERVER['HTTP_HOST']
            . rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/') . '/admin/setup.php';
        die('<div style="font-family:sans-serif;max-width:500px;margin:60px auto;padding:32px;border:1px solid #e2e8f0;border-radius:12px;text-align:center;">
            <h2 style="color:#0a1628;">⚙️ First-Time Setup Required</h2>
            <p style="color:#718096;margin:16px 0 24px;">The database <strong>apex_hospital</strong> does not exist yet.</p>
            <a href="' . htmlspecialchars($setupUrl) . '"
               style="display:inline-block;padding:12px 32px;background:#00c9a7;color:#0a1628;border-radius:50px;font-weight:700;text-decoration:none;font-family:Poppins,sans-serif;">
               Run Setup Now →
            </a>
        </div>');
    }
}

// Now select the database
$conn->select_db(DB_NAME);
