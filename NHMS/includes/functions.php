<?php
// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    // Set secure session cookie parameters
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => !empty($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
}

/**
 * Sanitize user input
 */
function sanitize_input($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate unique certificate number
 */
function generate_certificate_number($prefix) {
    return $prefix . '-' . strtoupper(bin2hex(random_bytes(8))) . '-' . date('Ym');
}

/**
 * Check if user is admin
 */
function is_admin() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Check if user is hospital
 */
function is_hospital() {
    return isset($_SESSION['hospital_id']) && !empty($_SESSION['hospital_id']);
}

/**
 * Redirect to URL
 */
function redirect($url) {
    header("Location: $url", true);
    exit;
}

/**
 * Set flash message
 */
function set_flash_message($type, $message) {
    $_SESSION['flash'] = [
        'type' => sanitize_input($type),
        'message' => sanitize_input($message)
    ];
}

/**
 * Display flash message
 */
function display_flash_message() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        $alert_class = $flash['type'] === 'success' ? 'success' : 'danger';
        return "<div class='alert alert-{$alert_class}' role='alert'>{$flash['message']}</div>";
    }
    return '';
}

/**
 * Generate CSRF token
 */
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Get CSRF token
 */
function get_csrf_token() {
    return $_SESSION['csrf_token'] ?? '';
}

/**
 * Verify CSRF token
 */
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }
    return true;
}

/**
 * Generate CSRF token field for forms
 */
function csrf_token_field() {
    $token = generate_csrf_token();
    return "<input type='hidden' name='csrf_token' value='" . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . "'>";
}

/**
 * Log activity for audit trail
 */
function log_activity($hospital_id, $action, $details) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO activity_logs (hospital_id, action, details, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$hospital_id, $action, $details]);
    } catch (Exception $e) {
        error_log('Activity logging failed: ' . $e->getMessage());
    }
}
