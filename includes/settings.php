<?php
/**
 * Load all site_settings from DB into $site array.
 * Also loads departments (for booking form).
 * Include once at top of each public page that needs dynamic content.
 */
require_once __DIR__ . '/../admin/db.php';

$site = [];
$res  = $conn->query("SELECT `key`,`value` FROM site_settings");
if ($res) while ($row = $res->fetch_assoc()) $site[$row['key']] = $row['value'];

// Helper – get setting with fallback
function cfg(string $key, string $default = ''): string {
    global $site;
    return $site[$key] ?? $default;
}
