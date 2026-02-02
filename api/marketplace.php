<?php
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');
ini_set('display_errors', 0);

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
            
            // Handle image upload (Required)
            if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(['success' => false, 'message' => 'Item image is required']);
                exit;
            }

            $file = $_FILES['image'];
            if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
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

        case 'send_inquiry':
            requireStudent();
            $sender_id = $_SESSION['user_id'];
            $item_id = intval($_POST['item_id']);
            $receiver_id = intval($_POST['receiver_id']);
            $message = sanitizeInput($_POST['message']); // 'message' combines subject + content for now or just content

            if (empty($message)) {
                echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
                exit;
            }

            // Prevent sending message to self
            if ($sender_id === $receiver_id) {
                 echo json_encode(['success' => false, 'message' => 'You cannot message yourself']);
                 exit;
            }

            // Get sender details
            $stmt = $conn->prepare("SELECT full_name FROM students WHERE id = ?");
            $stmt->execute([$sender_id]);
            $sender = $stmt->fetch();
            $sender_name = $sender['full_name'] ?? 'A Student';

            // Get item details
            $stmt = $conn->prepare("SELECT title FROM marketplace_items WHERE id = ?");
            $stmt->execute([$item_id]);
            $item_details = $stmt->fetch();
            $item_title = $item_details['title'] ?? 'Item';

            $stmt = $conn->prepare("INSERT INTO marketplace_inquiries (item_id, sender_id, receiver_id, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$item_id, $sender_id, $receiver_id, $message]);

            // Create notification for the seller
            $notif_title = "New Inquiry: " . $item_title;
            $notif_message = $sender_name . " sent you an inquiry.";
            $notif_link = "marketplace.php?highlight=" . $item_id;
            
            $stmt = $conn->prepare("INSERT INTO notifications (student_id, title, message, link, type) VALUES (?, ?, ?, ?, 'marketplace')");
            $stmt->execute([$receiver_id, $notif_title, $notif_message, $notif_link]);

            echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
            break;

        case 'mark_as_sold':
            requireStudent();
            $student_id = $_SESSION['user_id'];
            $item_id = intval($_POST['item_id']);

            // Verify ownership
            $stmt = $conn->prepare("SELECT student_id FROM marketplace_items WHERE id = ?");
            $stmt->execute([$item_id]);
            $item = $stmt->fetch();

            if (!$item || $item['student_id'] != $student_id) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized action']);
                exit;
            }

            // Update status
            $stmt = $conn->prepare("UPDATE marketplace_items SET status = 'sold' WHERE id = ?");
            $stmt->execute([$item_id]);

            echo json_encode(['success' => true, 'message' => 'Item marked as sold']);
            break;

        case 'get_inquiries':
            requireStudent();
            $user_id = $_SESSION['user_id'];
            
            // Get inquiries for items listed by this user
            $stmt = $conn->prepare("
                SELECT i.*, m.title as item_title, m.image_path, s.full_name as sender_name
                FROM marketplace_inquiries i
                JOIN marketplace_items m ON i.item_id = m.id
                JOIN students s ON i.sender_id = s.id
                WHERE i.receiver_id = ?
                ORDER BY i.created_at DESC
            ");
            $stmt->execute([$user_id]);
            $inquiries = $stmt->fetchAll();
            
            echo json_encode(['success' => true, 'inquiries' => $inquiries]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
