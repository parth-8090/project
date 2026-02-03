<?php
/**
 * Comprehensive Dummy Data Generator for Agora Campus
 * URL: http://localhost/project/database/populate_full_data.php
 */

require_once __DIR__ . '/../config/database.php';

// disable time limit for image downloading
set_time_limit(300);

$conn = getDBConnection();

function downloadImage($url, $savePath) {
    try {
        $content = @file_get_contents($url);
        if ($content !== false) {
            file_put_contents($savePath, $content);
            return true;
        }
    } catch (Exception $e) {
        // Ignore errors
    }
    return false;
}

echo "<!DOCTYPE html><html><head><title>Data Generation</title><style>body{font-family:sans-serif;line-height:1.6;padding:20px;max-width:800px;margin:0 auto;} .success{color:green;} .error{color:red;} h3{border-bottom:1px solid #ccc;padding-bottom:10px;margin-top:30px;}</style></head><body>";
echo "<h1>🚀 Agora Campus Data Generator</h1>";

try {
    // 1. CLEAR EXISTING DATA
    echo "<h3>1. Cleaning Database...</h3>";
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");
    $tables = [
        'users', // if exists
        'students', 'businesses', 'events', 'jobs', 'groups', 
        'complaints', 'lost_found', 'marketplace_items', 'mentorship', 
        'note_requests', 'notes', 'notifications', 'applications', 
        'business_reviews', 'group_members', 'messages', 'marketplace_inquiries'
    ];
    
    foreach ($tables as $table) {
        try {
            $conn->query("TRUNCATE TABLE $table");
        } catch (PDOException $e) {
            // Table might not exist, ignore
        }
    }
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");
    echo "<p class='success'>✅ Database cleared.</p>";

    $conn->beginTransaction();

    // 2. INSERT STUDENTS
    echo "<h3>2. Creating Students...</h3>";
    $password = password_hash('password123', PASSWORD_DEFAULT);
    
    $students = [
        ['John Doe', 'john.doe@campus.edu', 'Computer Science', 'EN2021001', 2021, '2003-05-15', 150, 'men/1.jpg'],
        ['Jane Smith', 'jane.smith@campus.edu', 'Electrical Engineering', 'EN2021002', 2021, '2003-08-20', 200, 'women/2.jpg'],
        ['Mike Johnson', 'mike.johnson@campus.edu', 'Mechanical Engineering', 'EN2020001', 2020, '2002-03-10', 300, 'men/3.jpg'],
        ['Sarah Williams', 'sarah.williams@campus.edu', 'Business Administration', 'EN2022001', 2022, '2004-11-25', 100, 'women/4.jpg'],
        ['David Brown', 'david.brown@campus.edu', 'Computer Science', 'EN2021003', 2021, '2003-01-30', 250, 'men/5.jpg'],
        ['Emily Davis', 'emily.davis@campus.edu', 'Civil Engineering', 'EN2020002', 2020, '2002-07-12', 180, 'women/6.jpg'],
        ['Chris Wilson', 'chris.wilson@campus.edu', 'Computer Science', 'EN2022002', 2022, '2004-04-18', 120, 'men/7.jpg'],
        ['Lisa Anderson', 'lisa.anderson@campus.edu', 'Electronics Engineering', 'EN2021004', 2021, '2003-09-05', 220, 'women/8.jpg'],
        ['Robert Taylor', 'robert.taylor@campus.edu', 'Mechanical Engineering', 'EN2020003', 2020, '2002-12-22', 280, 'men/9.jpg'],
        ['Amanda Martinez', 'amanda.martinez@campus.edu', 'Business Administration', 'EN2022003', 2022, '2004-06-08', 90, 'women/10.jpg']
    ];

    $stmt = $conn->prepare("INSERT INTO students (full_name, email, password, department, enrollment_no, year_of_admission, birthdate, age, points, profile_photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $studentIds = [];
    
    foreach ($students as $s) {
        $birthDate = new DateTime($s[5]);
        $today = new DateTime();
        $age = $today->diff($birthDate)->y;
        
        // Download Image
        $imageName = 'user_' . uniqid() . '.jpg';
        $imageUrl = "https://randomuser.me/api/portraits/" . $s[7];
        $targetPath = __DIR__ . '/../uploads/profiles/' . $imageName;
        
        $downloaded = downloadImage($imageUrl, $targetPath);
        if (!$downloaded) $imageName = null; // Fallback to default if download fails

        $stmt->execute([$s[0], $s[1], $password, $s[2], $s[3], $s[4], $s[5], $age, $s[6], $imageName]);
        $studentIds[] = $conn->lastInsertId();
    }
    echo "<p class='success'>✅ Inserted " . count($students) . " students with profile images.</p>";

    // 3. INSERT BUSINESSES
    echo "<h3>3. Creating Businesses...</h3>";
    $businesses = [
        ['Tech Solutions Inc', 'tech@business.com', 'IT'],
        ['Campus Bookstore', 'books@business.com', 'Retail'],
        ['Food Court', 'food@business.com', 'Food Service'],
        ['City Bank', 'bank@business.com', 'Finance'],
        ['Design Studio', 'design@business.com', 'Creative']
    ];
    
    $stmt = $conn->prepare("INSERT INTO businesses (business_name, email, password, business_type) VALUES (?, ?, ?, ?)");
    $businessIds = [];
    foreach ($businesses as $b) {
        $stmt->execute([$b[0], $b[1], $password, $b[2]]);
        $businessIds[] = $conn->lastInsertId();
    }
    echo "<p class='success'>✅ Inserted " . count($businesses) . " businesses.</p>";

    // 4. INSERT JOBS & APPLICATIONS
    echo "<h3>4. Creating Jobs & Applications...</h3>";
    $jobTitles = [
        ['Junior Web Developer', 'Full-time', 'IT'],
        ['Marketing Intern', 'Internship', 'Marketing'],
        ['Sales Associate', 'Part-time', 'Sales'],
        ['Graphic Designer', 'Freelance', 'Design'],
        ['Data Analyst', 'Full-time', 'Analytics']
    ];

    $jobStmt = $conn->prepare("INSERT INTO jobs (business_id, title, description, required_skills, period, job_type, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $appStmt = $conn->prepare("INSERT INTO applications (job_id, student_id, status) VALUES (?, ?, ?)");
    
    $jobIds = [];
    foreach ($businessIds as $bId) {
        // Each business posts 2-3 jobs
        $numJobs = rand(2, 3);
        for ($i = 0; $i < $numJobs; $i++) {
            $jobData = $jobTitles[array_rand($jobTitles)];
            $jobStmt->execute([
                $bId,
                $jobData[0],
                "This is a great opportunity for students to learn about " . $jobData[2] . ". Join our dynamic team!",
                "Communication, Teamwork, " . $jobData[2],
                rand(3, 12) . " Months",
                $jobData[1],
                'active'
            ]);
            $jobId = $conn->lastInsertId();
            $jobIds[] = $jobId;
            
            // Random students apply
            $applicants = array_rand(array_flip($studentIds), rand(3, 6));
            if (!is_array($applicants)) $applicants = [$applicants];
            
            foreach ($applicants as $sId) {
                $status = ['pending', 'approved', 'rejected'][rand(0, 2)];
                $appStmt->execute([$jobId, $sId, $status]);
            }
        }
    }
    echo "<p class='success'>✅ Inserted jobs and applications.</p>";

    // 5. MARKETPLACE ITEMS
    echo "<h3>5. Populating Marketplace...</h3>";
    $items = [
        ['Engineering Textbook', 'Books', 500, 'book'],
        ['Scientific Calculator', 'Electronics', 800, 'calculator'],
        ['Study Table', 'Furniture', 1500, 'table'],
        ['Laptop Stand', 'Electronics', 300, 'laptop'],
        ['Cycling Gear', 'Other', 1200, 'bicycle'],
        ['DBMS Textbook', 'Books', 400, 'book'],
        ['Drafter', 'Stationery', 250, 'pen'],
        ['Mattress', 'Furniture', 2000, 'bed'],
        ['Wireless Mouse', 'Electronics', 450, 'mouse'],
        ['Headphones', 'Electronics', 1000, 'headphones']
    ];

    $itemStmt = $conn->prepare("INSERT INTO marketplace_items (student_id, title, description, category, price, image_path, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($items as $item) {
        $sId = $studentIds[array_rand($studentIds)];
        
        // Download Image from loremflickr (more reliable for specific keywords)
        $imageName = 'item_' . uniqid() . '.jpg';
        $imageUrl = "https://loremflickr.com/400/300/" . $item[3] . "/all"; 
        $targetPath = __DIR__ . '/../uploads/marketplace/' . $imageName;
        
        $downloaded = downloadImage($imageUrl, $targetPath);
        if (!$downloaded) $imageName = null;

        $itemStmt->execute([
            $sId,
            $item[0],
            "Used but in good condition. Contact me for details.",
            $item[1],
            $item[2],
            $imageName,
            'available'
        ]);
        
        // Add inquiry
        $itemId = $conn->lastInsertId();
        $inquirerId = $studentIds[array_rand($studentIds)];
        if ($inquirerId != $sId) {
             $conn->query("INSERT INTO marketplace_inquiries (item_id, sender_id, receiver_id, message) VALUES ($itemId, $inquirerId, $sId, 'Is this still available?')");
        }
    }
    echo "<p class='success'>✅ Inserted marketplace items with images and inquiries.</p>";

    // 6. GROUPS & MESSAGES
    echo "<h3>6. creating Groups & Chats...</h3>";
    $groups = ['Coding Club', 'Music Society', 'Robotics Team', 'Debate Club', 'Sports Council'];
    $groupStmt = $conn->prepare("INSERT INTO groups (group_name, description, created_by) VALUES (?, ?, ?)");
    $memberStmt = $conn->prepare("INSERT INTO group_members (group_id, student_id) VALUES (?, ?)");
    $msgStmt = $conn->prepare("INSERT INTO messages (group_id, student_id, message) VALUES (?, ?, ?)");

    foreach ($groups as $gName) {
        $creator = $studentIds[array_rand($studentIds)];
        $groupStmt->execute([$gName, "Official group for $gName enthusiasts.", $creator]);
        $groupId = $conn->lastInsertId();
        
        // Add members
        $members = array_rand(array_flip($studentIds), rand(4, 8));
        if (!is_array($members)) $members = [$members];
        
        foreach ($members as $mId) {
            $memberStmt->execute([$groupId, $mId]);
            // Add random message
            $msgStmt->execute([$groupId, $mId, "Hello everyone! Excited to be here."]);
        }
    }
    echo "<p class='success'>✅ Inserted groups and chat messages.</p>";

    // 7. NOTIFICATIONS (CRITICAL REQUIREMENT)
    echo "<h3>7. Generating Notifications...</h3>";
    $notifStmt = $conn->prepare("INSERT INTO notifications (student_id, type, title, message, is_read) VALUES (?, ?, ?, ?, ?)");
    $types = ['system', 'application_update', 'new_message', 'event_reminder'];
    
    foreach ($studentIds as $sId) {
        // Generate 5-8 notifications per student
        $numNotifs = rand(5, 8);
        for ($i = 0; $i < $numNotifs; $i++) {
            $type = $types[array_rand($types)];
            $isRead = rand(0, 1);
            $titles = [
                'system' => 'System Maintenance',
                'application_update' => 'Application Status Changed',
                'new_message' => 'New Group Message',
                'event_reminder' => 'Upcoming Event'
            ];
            
            $notifStmt->execute([
                $sId,
                $type,
                $titles[$type],
                "This is a test notification content for $type. Please check details.",
                $isRead
            ]);
        }
    }
    echo "<p class='success'>✅ Inserted multiple notifications for each student.</p>";

    // 8. EVENTS
    echo "<h3>8. Creating Events...</h3>";
    $events = [
        ['Annual Tech Fest', 'campus', '2024-12-10'],
        ['Career Fair 2024', 'career', '2024-11-20'],
        ['Music Concert', 'cultural', '2024-10-15'],
        ['Hackathon', 'tech', '2024-09-05'],
        ['Alumni Meet', 'social', '2025-01-25']
    ];
    $eventStmt = $conn->prepare("INSERT INTO events (title, description, event_type, event_date, venue) VALUES (?, ?, ?, ?, ?)");
    
    foreach ($events as $ev) {
        $eventStmt->execute([
            $ev[0],
            "Join us for the " . $ev[0] . ". It will be amazing!",
            $ev[1],
            $ev[2],
            "Main Auditorium"
        ]);
    }
    echo "<p class='success'>✅ Inserted events.</p>";

    $conn->commit();
    echo "<h2>🎉 ALL DATA GENERATED SUCCESSFULLY!</h2>";
    echo "<p><strong>Students:</strong> Check README.md for login details (Password: password123)</p>";
    echo "<a href='../login.php'>Go to Login</a>";

} catch (Exception $e) {
    $conn->rollBack();
    echo "<h2 class='error'>❌ Error: " . $e->getMessage() . "</h2>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
