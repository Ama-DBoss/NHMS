<?php
function sanitize_input($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function generate_certificate_number($prefix) {
    return uniqid($prefix . '-');
}

function is_admin() {
    return isset($_SESSION['admin_id']);
}

function is_hospital() {
    return isset($_SESSION['hospital_id']);
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function set_flash_message($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function display_flash_message() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return "<div class='alert alert-{$flash['type']}'>{$flash['message']}</div>";
    }
    return '';
}