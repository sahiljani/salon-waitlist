<?php
session_start();

function loadEnv() {
    $path = __DIR__ . '/.env';
    if (!file_exists($path)) {
        die('Missing .env file');
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

function envValue($key, $default = '') {
    if (!isset($_ENV[$key])) {
        loadEnv();
    }
    return $_ENV[$key] ?? $default;
}

function getAdminPassword() {
    return envValue('ADMIN_PASSWORD', '');
}

function getStaffPassword() {
    $staff = envValue('STAFF_PASSWORD', '');
    if ($staff === '') {
        return getAdminPassword();
    }
    return $staff;
}

function loginAsRole($role) {
    session_regenerate_id(true);
    $_SESSION['is_logged_in'] = true;
    $_SESSION['role'] = $role;
}

function isLoggedIn() {
    return isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;
}

function currentRole() {
    return $_SESSION['role'] ?? null;
}

function isAdmin() {
    return currentRole() === 'admin';
}

function isStaff() {
    return currentRole() === 'staff';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: staff.php');
        exit;
    }
}

function requireStaffOrAdmin() {
    requireLogin();
    if (!isAdmin() && !isStaff()) {
        header('Location: login.php');
        exit;
    }
}

function requireAdminApi() {
    if (!isLoggedIn() || !isAdmin()) {
        http_response_code(403);
        echo json_encode(['error' => 'Admin access required']);
        exit;
    }
}
