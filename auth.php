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

function getAdminPassword() {
    if (!isset($_ENV['ADMIN_PASSWORD'])) {
        loadEnv();
    }
    return $_ENV['ADMIN_PASSWORD'] ?? '';
}

function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}
