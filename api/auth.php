<?php
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    $conn = getDBConnection();
    
    switch($action) {
        case 'register_student':
            $full_name = sanitizeInput($_POST['full_name']);
            $email = sanitizeInput($_POST['email']);
            $password = $_POST['password'];
            $department = sanitizeInput($_POST['department']);
            $enrollment_no = sanitizeInput($_POST['enrollment_no']);
            $year_of_admission = intval($_POST['year_of_admission']);
            $birthdate = $_POST['birthdate'];
            
            // Calculate age
            $age = calculateAge($birthdate);
            
            // Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Invalid email address']);
                exit;
            }
            
            // Check if email or enrollment already exists
            $stmt = $conn->prepare("SELECT id FROM students WHERE email = ? OR enrollment_no = ?");
            $stmt->execute([$email, $enrollment_no]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Email or Enrollment Number already exists']);
                exit;
            }
            
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert student
            $stmt = $conn->prepare("INSERT INTO students (full_name, email, password, department, enrollment_no, year_of_admission, birthdate, age) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$full_name, $email, $hashed_password, $department, $enrollment_no, $year_of_admission, $birthdate, $age]);
            
            echo json_encode(['success' => true, 'message' => 'Registration successful']);
            break;
            
        case 'register_business':
            $business_name = sanitizeInput($_POST['business_name']);
            $email = sanitizeInput($_POST['email']);
            $password = $_POST['password'];
            $business_type = sanitizeInput($_POST['business_type']);
            
            // Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Invalid email address']);
                exit;
            }
            
            // Check if email already exists
            $stmt = $conn->prepare("SELECT id FROM businesses WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Email already exists']);
                exit;
            }
            
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert business
            $stmt = $conn->prepare("INSERT INTO businesses (business_name, email, password, business_type) VALUES (?, ?, ?, ?)");
            $stmt->execute([$business_name, $email, $hashed_password, $business_type]);
            
            echo json_encode(['success' => true, 'message' => 'Registration successful']);
            break;
            
        case 'login':
            $email = sanitizeInput($_POST['email']);
            $password = $_POST['password'];
            $user_type = $_POST['user_type']; // 'student' or 'business'
            
            if ($user_type === 'student') {
                $stmt = $conn->prepare("SELECT id, full_name, email, password, department, points FROM students WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_type'] = 'student';
                    $_SESSION['user_name'] = $user['full_name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['department'] = $user['department'];
                    $_SESSION['points'] = $user['points'];
                    
                    echo json_encode(['success' => true, 'message' => 'Login successful', 'redirect' => 'dashboard.php']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
                }
            } else if ($user_type === 'business') {
                $stmt = $conn->prepare("SELECT id, business_name, email, password FROM businesses WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_type'] = 'business';
                    $_SESSION['user_name'] = $user['business_name'];
                    $_SESSION['user_email'] = $user['email'];
                    
                    echo json_encode(['success' => true, 'message' => 'Login successful', 'redirect' => 'business_dashboard.php']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid user type']);
            }
            break;
            
        case 'logout':
            // Clear session data
            $_SESSION = [];
            // Destroy the session
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                $cookieName = session_name();
                $path = $params["path"] ?: '/';
                $domain = $params["domain"] ?: '';
                $secure = (bool)($params["secure"] ?? false);
                $httponly = (bool)($params["httponly"] ?? true);

                // Expire using both the configured path and '/' (covers common misconfigs)
                setcookie($cookieName, '', time() - 42000, $path, $domain, $secure, $httponly);
                setcookie($cookieName, '', time() - 42000, '/', $domain, $secure, $httponly);
            }
            session_destroy();
            echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
