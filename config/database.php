<?php
// Database Configuration
// Check if running on localhost (XAMPP) or Live Server (InfinityFree)
// Check if running on localhost (XAMPP) or Live Server (InfinityFree)
// In CLI mode, HTTP_HOST is not set, so we assume localhost
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
if ($host === 'localhost' || str_contains($host, 'localhost')) {
    // Localhost (XAMPP) Credentials
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'p');
} else {
    // InfinityFree / Live Server Credentials
    // UPDATE THESE WITH YOUR INFINITYFREE DETAILS
    define('DB_HOST', 'sql123.infinityfree.com'); // Example: sql300.infinityfree.com
    define('DB_USER', 'if0_38383838');             // Example: if0_38383838
    define('DB_PASS', 'YourPassword');             // Your vPanel password
    define('DB_NAME', 'if0_38383838_agora');       // Example: if0_38383838_agora
}

// Create connection
function getDBConnection() {
    try {
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $conn;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
