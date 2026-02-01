<?php
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    $conn = getDBConnection();
    
    switch($action) {
        case 'update_profile':
            requireStudent();
            $student_id = $_SESSION['user_id'];
            $full_name = sanitizeInput($_POST['full_name']);
            $email = sanitizeInput($_POST['email']);
            $birthdate = sanitizeInput($_POST['birthdate']);
            
            $linkedin_link = sanitizeInput($_POST['linkedin_link'] ?? '');
            $github_link = sanitizeInput($_POST['github_link'] ?? '');
            $skills = sanitizeInput($_POST['skills'] ?? '');
            $interests = sanitizeInput($_POST['interests'] ?? '');
            
            // Handle Profile Photo Upload
            $profile_photo = null;
            if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['profile_photo'];
                $allowed = ['image/jpeg', 'image/png', 'image/gif'];
                
                if (in_array($file['type'], $allowed)) {
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename = 'profile_' . $student_id . '_' . time() . '.' . $ext;
                    $target = PROFILE_DIR . $filename;
                    
                    if (move_uploaded_file($file['tmp_name'], $target)) {
                        $profile_photo = $filename;
                    }
                }
            }
            
            // Calculate age
            $dob = new DateTime($birthdate);
            $now = new DateTime();
            $age = $now->diff($dob)->y;
            
            if ($profile_photo) {
                $stmt = $conn->prepare("UPDATE students SET full_name = ?, email = ?, birthdate = ?, age = ?, linkedin_link = ?, github_link = ?, skills = ?, interests = ?, profile_photo = ? WHERE id = ?");
                $stmt->execute([$full_name, $email, $birthdate, $age, $linkedin_link, $github_link, $skills, $interests, $profile_photo, $student_id]);
            } else {
                $stmt = $conn->prepare("UPDATE students SET full_name = ?, email = ?, birthdate = ?, age = ?, linkedin_link = ?, github_link = ?, skills = ?, interests = ? WHERE id = ?");
                $stmt->execute([$full_name, $email, $birthdate, $age, $linkedin_link, $github_link, $skills, $interests, $student_id]);
            }
            
            echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
            break;
            
        case 'report_lost_found':
            requireStudent();
            $student_id = $_SESSION['user_id'];
            $item_name = sanitizeInput($_POST['item_name']);
            $item_type = sanitizeInput($_POST['item_type']);
            $description = sanitizeInput($_POST['description'] ?? '');
            $location = sanitizeInput($_POST['location'] ?? '');
            
            $stmt = $conn->prepare("INSERT INTO lost_found (student_id, item_name, description, location, item_type) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$student_id, $item_name, $description, $location, $item_type]);
            
            echo json_encode(['success' => true, 'message' => 'Item reported successfully']);
            break;
            
        case 'mark_resolved':
            requireStudent();
            $item_id = intval($_POST['item_id']);
            $student_id = $_SESSION['user_id'];
            
            // Verify ownership
            $stmt = $conn->prepare("SELECT id FROM lost_found WHERE id = ? AND student_id = ?");
            $stmt->execute([$item_id, $student_id]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Item not found']);
                exit;
            }
            
            $stmt = $conn->prepare("UPDATE lost_found SET status = 'resolved' WHERE id = ?");
            $stmt->execute([$item_id]);
            
            echo json_encode(['success' => true, 'message' => 'Marked as resolved']);
            break;
            
        case 'submit_complaint':
            requireStudent();
            $student_id = $_SESSION['user_id'];
            $title = sanitizeInput($_POST['title']);
            $category = sanitizeInput($_POST['category'] ?? '');
            $description = sanitizeInput($_POST['description']);
            
            $stmt = $conn->prepare("INSERT INTO complaints (student_id, title, description, category) VALUES (?, ?, ?, ?)");
            $stmt->execute([$student_id, $title, $description, $category]);
            
            echo json_encode(['success' => true, 'message' => 'Complaint submitted successfully']);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
