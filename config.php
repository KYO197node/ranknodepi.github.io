<?php
// Configuration file for Pi Node Ranking

// Database settings (if you want to use database later)
define('DB_HOST', 'localhost');
define('DB_NAME', 'pi_nodes');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');

// Application settings
define('ITEMS_PER_PAGE', 20);
define('CACHE_DURATION', 300); // 5 minutes in seconds

// File paths
define('DATA_FILE', 'data/nodes_ranking.json');
define('BACKUP_DATA_FILE', 'data/backup_nodes.json');

// API settings (for future use)
define('PI_API_URL', 'https://blockexplorer.minepi.com/mainnet/nodes');
define('API_TIMEOUT', 30);

// Security settings
define('ENABLE_RATE_LIMIT', false);
define('MAX_REQUESTS_PER_MINUTE', 60);

// Display settings
define('SITE_TITLE', 'Pi Node Ranking');
define('SITE_DESCRIPTION', 'Kiểm tra xếp hạng Pi Node');
define('DEFAULT_TIMEZONE', 'Asia/Ho_Chi_Minh');

// Set timezone
date_default_timezone_set(DEFAULT_TIMEZONE);

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Helper functions
function formatNumber($number) {
    return number_format($number);
}

function formatDate($dateString, $format = 'd/m/Y H:i') {
    return date($format, strtotime($dateString));
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function isValidPublicKey($key) {
    // Basic validation for Stellar public key format
    return preg_match('/^G[A-Z0-9]{55}$/', $key);
}
?>