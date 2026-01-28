<?php
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    $conn = getDBConnection();
    
    switch($action) {
        case 'sell_item':
            requireStudent();
            $student_id = $_SESSION['user_id'];
            $title = sanitizeInput($_POST['title']);
            $category = sanitizeInput($_POST['category'] ?? 'Other');
            $description = sanitizeInput($_POST['description'] ?? '');
            $price = floatval($_POST['price']);
            
            $image_path = null;
            
            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['image'];
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                
                if (in_array($file['type'], $allowed_types)) {
                    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $filename = uniqid() . '_' . time() . '.' . $file_ext;
                    $file_path = MARKETPLACE_DIR . $filename;
                    
                    if (move_uploaded_file($file['tmp_name'], $file_path)) {
                        $image_path = 'uploads/marketplace/' . $filename;
                    }
                }
            }
            
            $stmt = $conn->prepare("INSERT INTO marketplace_items (student_id, title, description, category, price, image_path) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$student_id, $title, $description, $category, $price, $image_path]);
            
            echo json_encode(['success' => true, 'message' => 'Item posted successfully']);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
