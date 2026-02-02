<?php
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');
ini_set('display_errors', 0);

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Debug logging
error_log("Action: " . $action);
error_log("POST data: " . json_encode($_POST));

try {
    $conn = getDBConnection();
    
    switch($action) {
        case 'create_group':
            requireStudent();
            $student_id = $_SESSION['user_id'];
            $group_name = sanitizeInput($_POST['group_name'] ?? '');
            $department = sanitizeInput($_POST['department'] ?? '');
            $description = sanitizeInput($_POST['description'] ?? '');
            
            if (empty($group_name) || empty($description)) {
                 echo json_encode(['success' => false, 'message' => 'Name and description are required']);
                 exit;
            }
            
            $conn->beginTransaction();
            try {
                // Create group
                $stmt = $conn->prepare("INSERT INTO groups (group_name, department, description, created_by) VALUES (?, ?, ?, ?)");
                $stmt->execute([$group_name, $department, $description, $student_id]);
                $group_id = $conn->lastInsertId();
                
                // Add creator as member
                $stmt = $conn->prepare("INSERT INTO group_members (group_id, student_id) VALUES (?, ?)");
                $stmt->execute([$group_id, $student_id]);
                
                $conn->commit();
                echo json_encode(['success' => true, 'message' => 'Group created successfully', 'group_id' => $group_id]);
            } catch (Exception $e) {
                $conn->rollBack();
                // If error is about column not found, we might need to adjust.
                echo json_encode(['success' => false, 'message' => 'Error creating group: ' . $e->getMessage()]);
            }
            break;

        case 'update_group':
            requireStudent();
            $student_id = $_SESSION['user_id'];
            $group_id = intval($_POST['group_id']);
            $group_name = sanitizeInput($_POST['group_name'] ?? '');
            $department = sanitizeInput($_POST['department'] ?? '');
            $description = sanitizeInput($_POST['description'] ?? '');
            
            // Verify ownership
            $stmt = $conn->prepare("SELECT id FROM groups WHERE id = ? AND created_by = ?");
            $stmt->execute([$group_id, $student_id]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'You do not have permission to edit this group']);
                exit;
            }
            
            if (empty($group_name) || empty($description)) {
                echo json_encode(['success' => false, 'message' => 'Name and description are required']);
                exit;
            }
            
            $stmt = $conn->prepare("UPDATE groups SET group_name = ?, department = ?, description = ? WHERE id = ?");
            $stmt->execute([$group_name, $department, $description, $group_id]);
            
            echo json_encode(['success' => true, 'message' => 'Group updated successfully']);
            break;

        case 'join_group':
            requireStudent();
            $group_id = intval($_POST['group_id']);
            $student_id = $_SESSION['user_id'];
            
            // Check if already a member or pending
            $stmt = $conn->prepare("SELECT id, status FROM group_members WHERE group_id = ? AND student_id = ?");
            $stmt->execute([$group_id, $student_id]);
            if ($row = $stmt->fetch()) {
                if ($row['status'] == 'pending') {
                    echo json_encode(['success' => false, 'message' => 'Request already sent. Please wait for admin approval.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Already a member of this group']);
                }
                exit;
            }
            
            // Join group as PENDING
            $stmt = $conn->prepare("INSERT INTO group_members (group_id, student_id, status) VALUES (?, ?, 'pending')");
            $stmt->execute([$group_id, $student_id]);
            
            echo json_encode(['success' => true, 'message' => 'Request sent to admin']);
            break;
            
        case 'add_member':
            requireStudent();
            $group_id = intval($_POST['group_id']);
            $enrollment_no = sanitizeInput($_POST['enrollment_no']);
            $current_student_id = $_SESSION['user_id'];
            
            // Verify current user is THE CREATOR of the group
            $stmt = $conn->prepare("SELECT id FROM groups WHERE id = ? AND created_by = ?");
            $stmt->execute([$group_id, $current_student_id]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Only the group admin can add members']);
                exit;
            }
            
            // Find student by enrollment number
            $stmt = $conn->prepare("SELECT id FROM students WHERE enrollment_no = ?");
            $stmt->execute([$enrollment_no]);
            $student = $stmt->fetch();
            
            if (!$student) {
                echo json_encode(['success' => false, 'message' => 'Student not found with this enrollment number']);
                exit;
            }
            
            $new_member_id = $student['id'];
            
            // Check if already a member
            $stmt = $conn->prepare("SELECT id, status FROM group_members WHERE group_id = ? AND student_id = ?");
            $stmt->execute([$group_id, $new_member_id]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                if ($existing['status'] == 'pending') {
                    // Update to active
                    $stmt = $conn->prepare("UPDATE group_members SET status = 'active' WHERE id = ?");
                    $stmt->execute([$existing['id']]);
                    echo json_encode(['success' => true, 'message' => 'Request approved (member added)']);
                    exit;
                } else {
                    echo json_encode(['success' => false, 'message' => 'Student is already a member of this group']);
                    exit;
                }
            }
            
            // Add member as ACTIVE
            $stmt = $conn->prepare("INSERT INTO group_members (group_id, student_id, status) VALUES (?, ?, 'active')");
            $stmt->execute([$group_id, $new_member_id]);
            
            echo json_encode(['success' => true, 'message' => 'Member added successfully']);
            break;

        case 'approve_request':
            requireStudent();
            $group_id = intval($_POST['group_id']);
            $member_id = intval($_POST['student_id']);
            $current_student_id = $_SESSION['user_id'];

            // Verify admin
            $stmt = $conn->prepare("SELECT id FROM groups WHERE id = ? AND created_by = ?");
            $stmt->execute([$group_id, $current_student_id]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }

            $stmt = $conn->prepare("UPDATE group_members SET status = 'active' WHERE group_id = ? AND student_id = ?");
            $stmt->execute([$group_id, $member_id]);
            
            echo json_encode(['success' => true, 'message' => 'Request approved']);
            break;

        case 'reject_request':
            requireStudent();
            $group_id = intval($_POST['group_id']);
            $member_id = intval($_POST['student_id']);
            $current_student_id = $_SESSION['user_id'];

            // Verify admin
            $stmt = $conn->prepare("SELECT id FROM groups WHERE id = ? AND created_by = ?");
            $stmt->execute([$group_id, $current_student_id]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }

            $stmt = $conn->prepare("DELETE FROM group_members WHERE group_id = ? AND student_id = ?");
            $stmt->execute([$group_id, $member_id]);

            echo json_encode(['success' => true, 'message' => 'Request rejected']);
            break;

        case 'remove_member':
            requireStudent();
            $group_id = intval($_POST['group_id']);
            $member_id = intval($_POST['student_id']);
            $current_student_id = $_SESSION['user_id'];

            // Verify current user is THE CREATOR of the group
            $stmt = $conn->prepare("SELECT id FROM groups WHERE id = ? AND created_by = ?");
            $stmt->execute([$group_id, $current_student_id]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Only the group admin can remove members']);
                exit;
            }

            // Cannot remove yourself (admin)
            if ($member_id == $current_student_id) {
                echo json_encode(['success' => false, 'message' => 'You cannot remove yourself from the group']);
                exit;
            }

            // Remove member
            $stmt = $conn->prepare("DELETE FROM group_members WHERE group_id = ? AND student_id = ?");
            $stmt->execute([$group_id, $member_id]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Member removed successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Member not found in this group']);
            }
            break;

        case 'get_members':
            requireStudent();
            $group_id = intval($_GET['group_id']);
            $student_id = $_SESSION['user_id'];

             // Verify membership (any member can view list)
            $stmt = $conn->prepare("SELECT id FROM group_members WHERE group_id = ? AND student_id = ?");
            $stmt->execute([$group_id, $student_id]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'You are not a member of this group']);
                exit;
            }

            // Check if admin
            $stmt = $conn->prepare("SELECT created_by FROM groups WHERE id = ?");
            $stmt->execute([$group_id]);
            $group = $stmt->fetch();
            $is_admin = ($group['created_by'] == $student_id);

            // Admin sees all (Active + Pending), pending first. Others see only Active
            $status_condition = $is_admin ? "" : "AND gm.status = 'active'";
            
            $stmt = $conn->prepare("
                SELECT s.id, s.full_name, s.enrollment_no, s.profile_photo, gm.status,
                       (CASE WHEN g.created_by = s.id THEN 1 ELSE 0 END) as is_admin
                FROM group_members gm
                JOIN students s ON gm.student_id = s.id
                JOIN groups g ON gm.group_id = g.id
                WHERE gm.group_id = ? $status_condition
                ORDER BY case when gm.status = 'pending' then 0 else 1 end, is_admin DESC, s.full_name ASC
            ");
            $stmt->execute([$group_id]);
            $members = $stmt->fetchAll();

            echo json_encode(['success' => true, 'members' => $members]);
            break;

        case 'send_message':
            requireStudent();
            $group_id = intval($_POST['group_id']);
            $student_id = $_SESSION['user_id'];
            $message = sanitizeInput($_POST['message'] ?? ''); // Message can be empty if sending only image
            
            // Verify membership AND active status
            $stmt = $conn->prepare("SELECT id FROM group_members WHERE group_id = ? AND student_id = ? AND status = 'active'");
            $stmt->execute([$group_id, $student_id]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'You are not an active member of this group']);
                exit;
            }
            
            $attachment_url = null;
            
            // Handle file upload
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../uploads/chat/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $file = $_FILES['attachment'];
                $fileName = time() . '_' . basename($file['name']);
                $targetPath = $uploadDir . $fileName;
                
                // Allow only images
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($file['type'], $allowedTypes)) {
                    echo json_encode(['success' => false, 'message' => 'Only image files are allowed']);
                    exit;
                }
                
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $attachment_url = 'uploads/chat/' . $fileName;
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
                    exit;
                }
            }
            
            if (empty($message) && empty($attachment_url)) {
                echo json_encode(['success' => false, 'message' => 'Message or image is required']);
                exit;
            }
            
            // Insert message
            $stmt = $conn->prepare("INSERT INTO messages (group_id, student_id, message, attachment_url) VALUES (?, ?, ?, ?)");
            $stmt->execute([$group_id, $student_id, $message, $attachment_url]);
            
            // Get message with student info
            $message_id = $conn->lastInsertId();
            $stmt = $conn->prepare("
                SELECT m.*, s.full_name, s.enrollment_no
                FROM messages m
                JOIN students s ON m.student_id = s.id
                WHERE m.id = ?
            ");
            $stmt->execute([$message_id]);
            $new_message = $stmt->fetch();
            
            echo json_encode(['success' => true, 'message' => $new_message]);
            break;
            
        case 'get_messages':
            requireStudent();
            $group_id = intval($_GET['group_id']);
            $last_message_id = intval($_GET['last_message_id'] ?? 0);
            $student_id = $_SESSION['user_id'];
            
            // Verify membership AND active status
            $stmt = $conn->prepare("SELECT id FROM group_members WHERE group_id = ? AND student_id = ? AND status = 'active'");
            $stmt->execute([$group_id, $student_id]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'You are not an active member of this group']);
                exit;
            }
            
            // Get new messages
            if ($last_message_id > 0) {
                $stmt = $conn->prepare("
                    SELECT m.*, s.full_name, s.enrollment_no
                    FROM messages m
                    JOIN students s ON m.student_id = s.id
                    WHERE m.group_id = ? AND m.id > ?
                    ORDER BY m.sent_at ASC
                ");
                $stmt->execute([$group_id, $last_message_id]);
            } else {
                $stmt = $conn->prepare("
                    SELECT m.*, s.full_name, s.enrollment_no
                    FROM messages m
                    JOIN students s ON m.student_id = s.id
                    WHERE m.group_id = ?
                    ORDER BY m.sent_at DESC
                    LIMIT 20
                ");
                $stmt->execute([$group_id]);
            }
            $messages = $stmt->fetchAll();
            
            echo json_encode(['success' => true, 'messages' => $messages]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
