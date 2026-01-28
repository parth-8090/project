<?php
/**
 * Dummy Data Insertion Script
 * Run this file once to populate the database with sample data
 * Access via: http://localhost/project/database/insert_dummy_data.php
 */

require_once __DIR__ . '/../config/database.php';

$conn = getDBConnection();

try {
    // Check if dummy data already exists
    $checkStudents = $conn->query("SELECT COUNT(*) as count FROM students WHERE email LIKE '%@campus.edu'")->fetch();
    $checkBusinesses = $conn->query("SELECT COUNT(*) as count FROM businesses WHERE email LIKE '%@business.com'")->fetch();
    
    if ($checkStudents['count'] > 0 || $checkBusinesses['count'] > 0) {
        echo "<h2>⚠️ Dummy data already exists!</h2>";
        echo "<p>Found {$checkStudents['count']} students and {$checkBusinesses['count']} businesses with dummy emails.</p>";
        echo "<p>To re-insert, please delete existing dummy data first or use the SQL file directly.</p>";
        echo "<p><a href='../login.php'>Go to Login Page</a></p>";
        exit;
    }
    
    $conn->beginTransaction();
    
    // Default password for all dummy accounts: "password123"
    $defaultPassword = password_hash('password123', PASSWORD_DEFAULT);
    
    // Insert 10 Students
    $students = [
        ['John Doe', 'john.doe@campus.edu', 'Computer Science', 'EN2021001', 2021, '2003-05-15', 150, 'https://linkedin.com/in/johndoe', 'https://github.com/johndoe', 'PHP, JavaScript, Python', 'Web Development, AI'],
        ['Jane Smith', 'jane.smith@campus.edu', 'Electrical Engineering', 'EN2021002', 2021, '2003-08-20', 200, 'https://linkedin.com/in/janesmith', 'https://github.com/janesmith', 'C++, Embedded Systems', 'IoT, Robotics'],
        ['Mike Johnson', 'mike.johnson@campus.edu', 'Mechanical Engineering', 'EN2020001', 2020, '2002-03-10', 300, 'https://linkedin.com/in/mikejohnson', 'https://github.com/mikejohnson', 'CAD, SolidWorks', 'Automotive, Design'],
        ['Sarah Williams', 'sarah.williams@campus.edu', 'Business Administration', 'EN2022001', 2022, '2004-11-25', 100, 'https://linkedin.com/in/sarahwilliams', 'https://github.com/sarahwilliams', 'Marketing, Finance', 'Entrepreneurship'],
        ['David Brown', 'david.brown@campus.edu', 'Computer Science', 'EN2021003', 2021, '2003-01-30', 250, 'https://linkedin.com/in/davidbrown', 'https://github.com/davidbrown', 'Java, Spring Boot, React', 'Full Stack Development'],
        ['Emily Davis', 'emily.davis@campus.edu', 'Civil Engineering', 'EN2020002', 2020, '2002-07-12', 180, 'https://linkedin.com/in/emilydavis', 'https://github.com/emilydavis', 'AutoCAD, Project Management', 'Infrastructure'],
        ['Chris Wilson', 'chris.wilson@campus.edu', 'Computer Science', 'EN2022002', 2022, '2004-04-18', 120, 'https://linkedin.com/in/chriswilson', 'https://github.com/chriswilson', 'Python, Machine Learning', 'Data Science, AI'],
        ['Lisa Anderson', 'lisa.anderson@campus.edu', 'Electronics Engineering', 'EN2021004', 2021, '2003-09-05', 220, 'https://linkedin.com/in/lisaanderson', 'https://github.com/lisaanderson', 'VHDL, Verilog', 'Digital Design'],
        ['Robert Taylor', 'robert.taylor@campus.edu', 'Mechanical Engineering', 'EN2020003', 2020, '2002-12-22', 280, 'https://linkedin.com/in/roberttaylor', 'https://github.com/roberttaylor', 'MATLAB, ANSYS', 'Thermodynamics'],
        ['Amanda Martinez', 'amanda.martinez@campus.edu', 'Business Administration', 'EN2022003', 2022, '2004-06-08', 90, 'https://linkedin.com/in/amandamartinez', 'https://github.com/amandamartinez', 'Excel, Analytics', 'Business Strategy']
    ];
    
    $studentStmt = $conn->prepare("
        INSERT INTO students (full_name, email, password, department, enrollment_no, year_of_admission, birthdate, points, linkedin_link, github_link, skills, interests) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($students as $student) {
        $age = calculateAge($student[5]);
        $studentStmt->execute([
            $student[0], // full_name
            $student[1], // email
            $defaultPassword, // password
            $student[2], // department
            $student[3], // enrollment_no
            $student[4], // year_of_admission
            $student[5], // birthdate
            $student[6], // points
            $student[7], // linkedin_link
            $student[8], // github_link
            $student[9], // skills
            $student[10] // interests
        ]);
    }
    
    // Insert 5 Businesses
    $businesses = [
        ['Tech Solutions Inc', 'tech.solutions@business.com', 'Information Technology'],
        ['Campus Bookstore', 'campus.bookstore@business.com', 'Retail'],
        ['Food Court Management', 'food.court@business.com', 'Food Service'],
        ['Student Services Co', 'student.services@business.com', 'Services'],
        ['Digital Marketing Agency', 'digital.marketing@business.com', 'Marketing']
    ];
    
    $businessStmt = $conn->prepare("
        INSERT INTO businesses (business_name, email, password, business_type) 
        VALUES (?, ?, ?, ?)
    ");
    
    foreach ($businesses as $business) {
        $businessStmt->execute([
            $business[0], // business_name
            $business[1], // email
            $defaultPassword, // password
            $business[2] // business_type
        ]);
    }
    
    $conn->commit();
    
    echo "<h2>✅ Dummy Data Inserted Successfully!</h2>";
    echo "<h3>Students (10):</h3>";
    echo "<ul>";
    foreach ($students as $student) {
        echo "<li>{$student[0]} - {$student[1]} - Points: {$student[6]}</li>";
    }
    echo "</ul>";
    
    echo "<h3>Businesses (5):</h3>";
    echo "<ul>";
    foreach ($businesses as $business) {
        echo "<li>{$business[0]} - {$business[1]}</li>";
    }
    echo "</ul>";
    
    echo "<p><strong>Default Password for all accounts: password123</strong></p>";
    echo "<p><a href='../login.php'>Go to Login Page</a></p>";
    
} catch (Exception $e) {
    $conn->rollBack();
    echo "<h2>❌ Error: " . $e->getMessage() . "</h2>";
    echo "<p>Please check if the database tables exist and try again.</p>";
}

function calculateAge($birthdate) {
    $birth = new DateTime($birthdate);
    $today = new DateTime();
    return $today->diff($birth)->y;
}
