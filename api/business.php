<?php
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    $conn = getDBConnection();
    
    switch($action) {
        case 'post_job':
            requireBusiness();
            $business_id = $_SESSION['user_id'];
            $title = sanitizeInput($_POST['title']);
            $job_type = sanitizeInput($_POST['job_type']);
            $description = sanitizeInput($_POST['description']);
            $required_skills = sanitizeInput($_POST['required_skills']);
            $period = sanitizeInput($_POST['period']);
            $time_required = sanitizeInput($_POST['time_required'] ?? '');
            $number_of_employees = intval($_POST['number_of_employees'] ?? 0);
            
            $stmt = $conn->prepare("INSERT INTO jobs (business_id, job_type, title, description, required_skills, period, time_required, number_of_employees) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$business_id, $job_type, $title, $description, $required_skills, $period, $time_required, $number_of_employees]);
            
            echo json_encode(['success' => true, 'message' => 'Job posted successfully']);
            break;
            
        case 'update_application':
            requireBusiness();
            $application_id = intval($_POST['application_id']);
            $status = sanitizeInput($_POST['status']);
            
            // Verify business owns this application
            $stmt = $conn->prepare("
                SELECT a.id, a.student_id, a.job_id, j.number_of_employees, j.status as job_status
                FROM applications a
                JOIN jobs j ON a.job_id = j.id
                WHERE a.id = ? AND j.business_id = ?
            ");
            $stmt->execute([$application_id, $_SESSION['user_id']]);
            $application = $stmt->fetch();
            
            if (!$application) {
                echo json_encode(['success' => false, 'message' => 'Application not found']);
                exit;
            }

            // CHECK OPENINGS if approving
            if ($status === 'approved') {
                if ($application['number_of_employees'] <= 0) {
                    echo json_encode(['success' => false, 'message' => 'Cannot approve: No openings left for this job.']);
                    exit;
                }
            }
            
            // Update application status
            $stmt = $conn->prepare("UPDATE applications SET status = ? WHERE id = ?");
            $stmt->execute([$status, $application_id]);
            
            // If approved, award points, REDUCE OPENINGS, and potentially AUTO-CLOSE
            if ($status === 'approved') {
                // Get student's department
                $stmt = $conn->prepare("SELECT department FROM students WHERE id = ?");
                $stmt->execute([$application['student_id']]);
                $student = $stmt->fetch();
                
                // Award points
                $points_to_award = 10;
                $stmt = $conn->prepare("UPDATE students SET points = points + ? WHERE id = ?");
                $stmt->execute([$points_to_award, $application['student_id']]);
                
                // Create notification
                $stmt = $conn->prepare("INSERT INTO notifications (student_id, type, title, message, link) VALUES (?, 'application', 'Application Approved', 'Your job application has been approved!', ?)");
                $stmt->execute([$application['student_id'], "job_details.php?id=" . $application['job_id']]);

                // REDUCE OPENINGS
                $stmt = $conn->prepare("UPDATE jobs SET number_of_employees = number_of_employees - 1 WHERE id = ?");
                $stmt->execute([$application['job_id']]);

                // AUTO-CLOSE if 0
                // Re-fetch to be sure or just assume previous - 1
                $stmt = $conn->prepare("UPDATE jobs SET status = 'completed' WHERE id = ? AND number_of_employees <= 0");
                $stmt->execute([$application['job_id']]);
            }
            
            echo json_encode(['success' => true, 'message' => 'Application updated successfully']);
            break;
            
        case 'submit_review':
            requireBusiness();
            $business_id = $_SESSION['user_id'];
            $student_id = intval($_POST['student_id']);
            $job_id = intval($_POST['job_id']);
            $rating = intval($_POST['rating']);
            $review_text = sanitizeInput($_POST['review_text'] ?? '');
            
            // Check if review already exists
            $stmt = $conn->prepare("SELECT id FROM business_reviews WHERE business_id = ? AND student_id = ? AND job_id = ?");
            $stmt->execute([$business_id, $student_id, $job_id]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Review already submitted']);
                exit;
            }
            
            // Insert review
            $stmt = $conn->prepare("INSERT INTO business_reviews (business_id, student_id, job_id, rating, review_text) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$business_id, $student_id, $job_id, $rating, $review_text]);
            
            // Award points based on rating
            $points = $rating * 2; // 2 points per star
            $stmt = $conn->prepare("UPDATE students SET points = points + ? WHERE id = ?");
            $stmt->execute([$points, $student_id]);
            
            echo json_encode(['success' => true, 'message' => 'Review submitted successfully']);
            break;
            
        case 'delete_job':
            requireBusiness();
            $job_id = intval($_POST['job_id']);
            $business_id = $_SESSION['user_id'];
            
            // Verify ownership
            $stmt = $conn->prepare("SELECT id FROM jobs WHERE id = ? AND business_id = ?");
            $stmt->execute([$job_id, $business_id]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Job not found']);
                exit;
            }
            
            // Update status to deleted
            $stmt = $conn->prepare("UPDATE jobs SET status = 'deleted' WHERE id = ?");
            $stmt->execute([$job_id]);
            
            echo json_encode(['success' => true, 'message' => 'Job deleted successfully']);
            break;

        case 'toggle_job_status':
            requireBusiness();
            $job_id = intval($_POST['job_id']);
            $business_id = $_SESSION['user_id'];
            $new_status = sanitizeInput($_POST['status']);
            
            // Verify ownership
            $stmt = $conn->prepare("SELECT id FROM jobs WHERE id = ? AND business_id = ?");
            $stmt->execute([$job_id, $business_id]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Job not found']);
                exit;
            }
            
            // Update status
            $stmt = $conn->prepare("UPDATE jobs SET status = ? WHERE id = ?");
            $stmt->execute([$new_status, $job_id]);
            
            echo json_encode(['success' => true, 'message' => 'Job status updated successfully']);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
