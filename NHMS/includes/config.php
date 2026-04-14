<?php
/**
 * Application Configuration
 * 
 * This file contains configuration for the NHMS application.
 * Modify these settings for your deployment environment.
 */

// === Database Configuration ===
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: 3306);
define('DB_NAME', getenv('DB_NAME') ?: 'nigerian_hospital_registry');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

// === Application Configuration ===
define('APP_NAME', 'National Birth & Death Management System');
define('APP_VERSION', '1.0.0');
define('APP_ENV', getenv('APP_ENV') ?: 'production');

// === Security Configuration ===
define('ENABLE_HTTPS', !empty($_SERVER['HTTPS']));
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_ATTEMPT_TIMEOUT', 15); // 15 minutes in seconds

// === File Configuration ===
define('UPLOAD_DIR', dirname(__DIR__) . '/uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB in bytes

// === Email Configuration (optional) ===
define('ENABLE_EMAIL', false);
define('SMTP_HOST', getenv('SMTP_HOST') ?: '');
define('SMTP_PORT', getenv('SMTP_PORT') ?: 587);
define('SMTP_USER', getenv('SMTP_USER') ?: '');
define('SMTP_PASS', getenv('SMTP_PASS') ?: '');
define('FROM_EMAIL', getenv('FROM_EMAIL') ?: 'noreply@nhms.gov');

// === Logging Configuration ===
define('LOG_DIR', dirname(dirname(__DIR__)) . '/logs/');
define('LOG_LEVEL', APP_ENV === 'production' ? 'error' : 'debug');
define('ENABLE_ERROR_DISPLAY', APP_ENV !== 'production');

// === API Configuration ===
define('API_RATE_LIMIT', 100); // requests per hour
define('API_TIMEOUT', 30); // seconds

// === Ensure required directories exist ===
if (!is_dir(UPLOAD_DIR)) {
    @mkdir(UPLOAD_DIR, 0755, true);
}

if (!is_dir(LOG_DIR)) {
    @mkdir(LOG_DIR, 0755, true);
}

// === Set error reporting based on environment ===
if (ENABLE_ERROR_DISPLAY) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
}

// === Set log file ===
ini_set('error_log', LOG_DIR . 'php-error.log');
