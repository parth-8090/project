<?php
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    $conn = getDBConnection();
    
    switch($action) {
        case 'apply':
            requireStudent();
            $job_id = intval($_POST['job_id']);
            $student_id = $_SESSION['user_id'];
            
            // Check if already applied
            $stmt = $conn->prepare("SELECT id FROM applications WHERE job_id = ? AND student_id = ?");
            $stmt->execute([$job_id, $student_id]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'You have already applied for this job']);
                exit;
            }
            
            // Check if job exists and is active
            $stmt = $conn->prepare("SELECT id FROM jobs WHERE id = ? AND status = 'active'");
            $stmt->execute([$job_id]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Job not found or no longer available']);
                exit;
            }
            
            // Insert application
            $stmt = $conn->prepare("INSERT INTO applications (job_id, student_id, status) VALUES (?, ?, 'pending')");
            $stmt->execute([$job_id, $student_id]);
            
            // Create notification
            $stmt = $conn->prepare("INSERT INTO notifications (student_id, type, title, message, link) VALUES (?, 'application', 'Job Application Submitted', 'Your application has been submitted successfully', ?)");
            $stmt->execute([$student_id, "job_details.php?id=$job_id"]);
            
            echo json_encode(['success' => true, 'message' => 'Application submitted successfully']);
            break;
            
        case 'get_jobs':
            requireStudent();
            $department = $_SESSION['department'] ?? '';
            
            $stmt = $conn->prepare("
                SELECT j.*, b.business_name, b.business_type,
                       (SELECT COUNT(*) FROM applications WHERE job_id = j.id AND student_id = ?) as has_applied
                FROM jobs j
                JOIN businesses b ON j.business_id = b.id
                WHERE j.status = 'active'
                ORDER BY j.posted_at DESC
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $jobs = $stmt->fetchAll();
            
            echo json_encode(['success' => true, 'jobs' => $jobs]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
