<?php
require_once 'config/config.php';

try {
    $conn = getDBConnection();
    
    // Add created_by to groups table
    $sql = "ALTER TABLE groups ADD COLUMN created_by INT DEFAULT NULL";
    $conn->exec($sql);
    echo "Successfully added created_by to groups table.\n";
    
    // Update existing groups to set created_by from the first member
    $sql = "UPDATE groups g 
            JOIN (
                SELECT group_id, student_id 
                FROM group_members 
                GROUP BY group_id 
                ORDER BY id ASC
            ) gm ON g.id = gm.group_id 
            SET g.created_by = gm.student_id 
            WHERE g.created_by IS NULL";
    $conn->exec($sql);
    echo "Updated existing groups with creator info.\n";
    
    // Add Foreign Key
    $sql = "ALTER TABLE groups ADD FOREIGN KEY (created_by) REFERENCES students(id) ON DELETE SET NULL";
    $conn->exec($sql);
    echo "Added foreign key constraint.\n";
    
} catch (PDOException $e) {
    echo "Database error (Column might already exist): " . $e->getMessage() . "\n";
}
?>