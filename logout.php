<?php
require_once __DIR__ . '/config/config.php';

// Robust logout endpoint (works even if JS logout fails)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear all session variables
$_SESSION = [];

// Expire the session cookie (try both the configured path and '/')
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    $cookieName = session_name();

    $path = $params['path'] ?: '/';
    $domain = $params['domain'] ?: '';
    $secure = (bool)($params['secure'] ?? false);
    $httponly = (bool)($params['httponly'] ?? true);

    // PHP 7.3+ supports options array, but keep compatibility with older setups.
    setcookie($cookieName, '', time() - 42000, $path, $domain, $secure, $httponly);
    setcookie($cookieName, '', time() - 42000, '/', $domain, $secure, $httponly);
}

session_destroy();

header('Location: login.php');
exit;

