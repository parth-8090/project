<?php
// Test file to debug group creation
require_once 'config/config.php';
requireStudent();

echo "<h2>Debug Information</h2>";

// Check if user is logged in
echo "<p><strong>User ID:</strong> " . $_SESSION['user_id'] . "</p>";
echo "<p><strong>User Type:</strong> " . $_SESSION['user_type'] . "</p>";

// Check database connection
try {
    $conn = getDBConnection();
    echo "<p style='color: green;'><strong>✓ Database Connected</strong></p>";
    
    // Check if students table has the user
    $stmt = $conn->prepare("SELECT id, full_name FROM students WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $student = $stmt->fetch();
    
    if ($student) {
        echo "<p style='color: green;'><strong>✓ Student Found:</strong> " . $student['full_name'] . "</p>";
    } else {
        echo "<p style='color: red;'><strong>✗ Student Not Found in Database</strong></p>";
    }
    
    // Check groups table structure
    echo "<h3>Groups Table Structure:</h3>";
    $result = $conn->query("SHOW COLUMNS FROM groups");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    foreach ($result as $row) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check group_members table structure
    echo "<h3>Group Members Table Structure:</h3>";
    $result = $conn->query("SHOW COLUMNS FROM group_members");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    foreach ($result as $row) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // List existing groups
    echo "<h3>Existing Groups:</h3>";
    $stmt = $conn->prepare("SELECT * FROM groups LIMIT 5");
    $stmt->execute();
    $groups = $stmt->fetchAll();
    
    if (!empty($groups)) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Name</th><th>Description</th><th>Created By</th><th>Created At</th></tr>";
        foreach ($groups as $group) {
            echo "<tr>";
            echo "<td>" . $group['id'] . "</td>";
            echo "<td>" . $group['group_name'] . "</td>";
            echo "<td>" . substr($group['description'], 0, 30) . "...</td>";
            echo "<td>" . $group['created_by'] . "</td>";
            echo "<td>" . $group['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No groups yet</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>✗ Error:</strong> " . $e->getMessage() . "</p>";
}
?>

<h3>Test Group Creation</h3>
<p>Use this form to test creating a group:</p>
<form method="POST" action="api/groups.php">
    <input type="hidden" name="action" value="create_group">
    <div>
        <label>Group Name:</label><br>
        <input type="text" name="group_name" value="Test Group <?php echo time(); ?>" required>
    </div>
    <div>
        <label>Department:</label><br>
        <input type="text" name="department" value="Computer Science">
    </div>
    <div>
        <label>Description:</label><br>
        <textarea name="description" required>This is a test group</textarea>
    </div>
    <button type="submit">Create Group</button>
</form>

<hr>
<p><a href="groups.php">← Back to Groups</a></p>
