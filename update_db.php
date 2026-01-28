<?php
require_once 'config/config.php';

try {
    $conn = getDBConnection();
    
    // Add attachment_url to messages table
    $sql = "ALTER TABLE messages ADD COLUMN attachment_url VARCHAR(255) DEFAULT NULL";
    $conn->exec($sql);
    echo "Successfully added attachment_url to messages table.\n";
    
} catch (PDOException $e) {
    echo "Database error (Column might already exist): " . $e->getMessage() . "\n";
}
?>