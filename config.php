<?php
// Configuration file for Pi Node Ranking - Developed by Pi2Team

// Application info
define('APP_NAME', 'Pi Node Ranking');
define('APP_VERSION', '1.0.0');
define('APP_AUTHOR', 'Pi2Team');
define('APP_DESCRIPTION', 'Kiểm tra xếp hạng Pi Node với giao diện đẹp và tính năng đầy đủ');

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
define('SITE_TITLE', 'Pi Node Ranking - Pi2Team');
define('SITE_DESCRIPTION', 'Kiểm tra xếp hạng Pi Node - Phát triển bởi Pi2Team');
define('DEFAULT_TIMEZONE', 'Asia/Ho_Chi_Minh');

// Pi2Team branding
define('TEAM_NAME', 'Pi2Team');
define('TEAM_WEBSITE', '#'); // Update when available
define('TEAM_EMAIL', '#'); // Update when available
define('TEAM_TELEGRAM', '#'); // Update when available

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

// Pi2Team branding functions
function getTeamInfo() {
    return [
        'name' => TEAM_NAME,
        'website' => TEAM_WEBSITE,
        'email' => TEAM_EMAIL,
        'telegram' => TEAM_TELEGRAM
    ];
}

function getAppInfo() {
    return [
        'name' => APP_NAME,
        'version' => APP_VERSION,
        'author' => APP_AUTHOR,
        'description' => APP_DESCRIPTION
    ];
}
?>