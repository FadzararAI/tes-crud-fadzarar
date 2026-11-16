<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
require_once __DIR__ . '/koneksi.php';
// Escape output before placing a database or form value in HTML.
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
// Ignore array-shaped input: each form field should contain one string.
function input($source, $key, $default = '') {
    if (isset($source[$key]) && is_string($source[$key])) {
        return trim($source[$key]);
    }
    return $default;
}
function positive_id($value)
{
    if (preg_match('/^[1-9][0-9]{0,8}$/D', (string)$value)) {
        return (int)$value;
    }

    return 0;
}

function valid_date($value) {
    $date = DateTime::createFromFormat('!Y-m-d', $value);
    return $date && $date->format('Y-m-d') === $value;
}

function redirect($url, $message = '') {
    if ($message !== '') {
        $_SESSION['flash'] = $message;
    }
    header('Location: ' . $url);
    exit;
}

function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(openssl_random_pseudo_bytes(32));
    }

    return $_SESSION['csrf'];
}

function csrf_field() {
    echo '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf() {
    if (!hash_equals(csrf_token(), input($_POST, 'csrf'))) {
        http_response_code(403);
        exit('Your form expired. Return to the previous page, reload, and try again.');
    }
}

function money($value) {
    return 'Rp ' . number_format((float)$value, 2, '.', ',');
}

function errors_html($errors) {
    if (!$errors) return;

    echo '<div class="notice error" role="alert"><strong>Please check the form</strong><ul>';

    foreach ($errors as $error) {
        echo '<li>' . e($error) . '</li>';
    }

    echo '</ul></div>';
}
// Keep shared page markup separate from request-processing code.
function page_start($title, $active = 'inventory') {
    $flash = isset($_SESSION['flash']) ? $_SESSION['flash'] : '';
    unset($_SESSION['flash']);
    require __DIR__ . '/includes/header.php';
}
function page_end() {
    require __DIR__ . '/includes/footer.php';
}
