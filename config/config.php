<?php
// Application Configuration
require_once __DIR__ . '/database.php';

// Site Configuration
define('SITE_NAME', 'Agora Campus');
define('SITE_URL', 'http://localhost');

// File Upload Configuration
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('NOTES_DIR', UPLOAD_DIR . 'notes/');
define('MARKETPLACE_DIR', UPLOAD_DIR . 'marketplace/');
define('CHAT_DIR', UPLOAD_DIR . 'chat/');
define('MAX_FILE_SIZE', 10485760); // 10MB

// Create upload directories if they don't exist
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}
if (!file_exists(NOTES_DIR)) {
    mkdir(NOTES_DIR, 0777, true);
}
if (!file_exists(MARKETPLACE_DIR)) {
    mkdir(MARKETPLACE_DIR, 0777, true);
}
if (!file_exists(CHAT_DIR)) {
    mkdir(CHAT_DIR, 0777, true);
}

// Helper Functions
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
}

function isStudent() {
    return isLoggedIn() && $_SESSION['user_type'] === 'student';
}

function isBusiness() {
    return isLoggedIn() && $_SESSION['user_type'] === 'business';
}

function requireLogin() {
    if (!isLoggedIn()) {
        // Use relative paths so this works when hosted under /project
        header('Location: login.php');
        exit;
    }
}

function requireStudent() {
    requireLogin();
    if (!isStudent()) {
        // Business users shouldn't loop on student-only dashboard
        header('Location: business_dashboard.php');
        exit;
    }
}

function requireBusiness() {
    requireLogin();
    if (!isBusiness()) {
        header('Location: dashboard.php');
        exit;
    }
}

function calculateAge($birthdate) {
    $birth = new DateTime($birthdate);
    $today = new DateTime();
    return $today->diff($birth)->y;
}

function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

function formatDateTime($datetime) {
    return date('M d, Y h:i A', strtotime($datetime));
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
