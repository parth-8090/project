<?php
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    $conn = getDBConnection();
    
    switch($action) {
        case 'upload_note':
            requireStudent();
            $student_id = $_SESSION['user_id'];
            $title = sanitizeInput($_POST['title']);
            $subject = sanitizeInput($_POST['subject']);
            $description = sanitizeInput($_POST['description'] ?? '');
            $department = $_SESSION['department'];
            
            // Handle file upload
            if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(['success' => false, 'message' => 'File upload error']);
                exit;
            }
            
            $file = $_FILES['file'];
            $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (!in_array($file['type'], $allowed_types) && !in_array($file_ext, ['pdf', 'doc', 'docx'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid file type. Only PDF and DOC files are allowed']);
                exit;
            }
            
            if ($file['size'] > MAX_FILE_SIZE) {
                echo json_encode(['success' => false, 'message' => 'File size exceeds maximum limit']);
                exit;
            }
            
            // Generate unique filename
            $filename = uniqid() . '_' . time() . '.' . $file_ext;
            $file_path = NOTES_DIR . $filename;
            
            if (!move_uploaded_file($file['tmp_name'], $file_path)) {
                echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
                exit;
            }
            
            // Save to database
            $relative_path = 'uploads/notes/' . $filename;
            $stmt = $conn->prepare("INSERT INTO notes (student_id, title, description, file_path, subject, department) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$student_id, $title, $description, $relative_path, $subject, $department]);
            
            // Award points
            $stmt = $conn->prepare("UPDATE students SET points = points + 5 WHERE id = ?");
            $stmt->execute([$student_id]);
            
            echo json_encode(['success' => true, 'message' => 'Note uploaded successfully']);
            break;
            
        case 'request_note':
            requireStudent();
            $student_id = $_SESSION['user_id'];
            $subject = sanitizeInput($_POST['subject']);
            $description = sanitizeInput($_POST['description'] ?? '');
            $request_type = sanitizeInput($_POST['request_type']);
            
            $stmt = $conn->prepare("INSERT INTO note_requests (student_id, subject, description, request_type) VALUES (?, ?, ?, ?)");
            $stmt->execute([$student_id, $subject, $description, $request_type]);
            
            echo json_encode(['success' => true, 'message' => 'Request submitted successfully']);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
