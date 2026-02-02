<?php
require_once 'config/config.php';
requireStudent();

$conn = getDBConnection();

// Get student stats
$stmt = $conn->prepare("SELECT points FROM students WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$student = $stmt->fetch();

// Get available jobs count
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM jobs WHERE status = 'active'");
$stmt->execute();
$jobs_count = $stmt->fetch()['count'];

// Get groups count
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM group_members WHERE student_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$groups_count = $stmt->fetch()['count'];

// Get unread notifications
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE student_id = ? AND is_read = 0");
$stmt->execute([$_SESSION['user_id']]);
$notifications_count = $stmt->fetch()['count'];

// Get recent events
$stmt = $conn->prepare("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 5");
$stmt->execute();
$events = $stmt->fetchAll();

$page_title = "Dashboard";
require_once 'includes/header.php';
?>

<div class="container py-4">
    <div class="row mb-5 align-items-center" data-aos="fade-down">
        <div class="col-md-8">
            <h1 class="page-title h3 fw-bold mb-2">
                <span class="text-gradient">Student Dashboard</span>
            </h1>
            <p class="text-muted mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>! Here's what's happening today.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 shadow-sm border border-primary-subtle">
                <i class="fas fa-star me-1"></i> <?php echo $student['points'] ?? 0; ?> Points
            </span>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="100">
            <div class="stat-card h-100">
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number" data-target="<?php echo $jobs_count; ?>">0</h3>
                    <p class="stat-label">Available Jobs</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="200">
            <div class="stat-card h-100">
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number"><?php echo $groups_count; ?></h3>
                    <p class="stat-label">My Groups</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="300">
            <div class="stat-card h-100">
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number"><?php echo $student['points'] ?? 0; ?></h3>
                    <p class="stat-label">Total Points</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="400">
            <div class="stat-card h-100">
                <div class="stat-icon bg-danger-subtle text-danger">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number" data-target="<?php echo $notifications_count; ?>">0</h3>
                    <p class="stat-label">Notifications</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <!-- Quick Access -->
        <div class="col-lg-8">
            <h4 class="mb-4 fw-bold" data-aos="fade-right">Quick Access</h4>
            <div class="row g-4">
                <div class="col-md-4 col-6" data-aos="zoom-in" data-aos-delay="100">
                    <a href="jobs.php" class="card h-100 text-center p-4 text-decoration-none hover-lift">
                        <div class="mb-3">
                            <i class="fas fa-briefcase fa-2x text-primary"></i>
                        </div>
                        <h6 class="fw-bold text-body">Jobs</h6>
                    </a>
                </div>
                <div class="col-md-4 col-6" data-aos="zoom-in" data-aos-delay="150">
                    <a href="groups.php" class="card h-100 text-center p-4 text-decoration-none hover-lift">
                        <div class="mb-3">
                            <i class="fas fa-users fa-2x text-success"></i>
                        </div>
                        <h6 class="fw-bold text-body">Groups</h6>
                    </a>
                </div>
                <div class="col-md-4 col-6" data-aos="zoom-in" data-aos-delay="200">
                    <a href="marketplace.php" class="card h-100 text-center p-4 text-decoration-none hover-lift">
                        <div class="mb-3">
                            <i class="fas fa-store fa-2x text-info"></i>
                        </div>
                        <h6 class="fw-bold text-body">Marketplace</h6>
                    </a>
                </div>
                <div class="col-md-4 col-6" data-aos="zoom-in" data-aos-delay="250">
                    <a href="events.php" class="card h-100 text-center p-4 text-decoration-none hover-lift">
                        <div class="mb-3">
                            <i class="fas fa-calendar-alt fa-2x text-warning"></i>
                        </div>
                        <h6 class="fw-bold text-body">Events</h6>
                    </a>
                </div>
                <div class="col-md-4 col-6" data-aos="zoom-in" data-aos-delay="300">
                    <a href="notes.php" class="card h-100 text-center p-4 text-decoration-none hover-lift">
                        <div class="mb-3">
                            <i class="fas fa-book fa-2x text-danger"></i>
                        </div>
                        <h6 class="fw-bold text-body">Notes</h6>
                    </a>
                </div>
                <div class="col-md-4 col-6" data-aos="zoom-in" data-aos-delay="350">
                    <a href="lost_found.php" class="card h-100 text-center p-4 text-decoration-none hover-lift">
                        <div class="mb-3">
                            <i class="fas fa-search fa-2x text-secondary"></i>
                        </div>
                        <h6 class="fw-bold text-body">Lost & Found</h6>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Recent Events Sidebar -->
        <div class="col-lg-4">
            <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-left">
                <h4 class="fw-bold mb-0">Upcoming Events</h4>
                <a href="events.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            
            <div class="card border-0 shadow-sm" data-aos="fade-left" data-aos-delay="200">
                <div class="list-group list-group-flush rounded-3">
                    <?php if (empty($events)): ?>
                        <div class="list-group-item p-4 text-center text-muted">
                            <i class="fas fa-calendar-times fa-2x mb-2"></i>
                            <p class="mb-0">No upcoming events</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($events as $event): ?>
                            <div class="list-group-item p-3 border-bottom-0">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($event['title']); ?></h6>
                                        <small class="text-muted">
                                            <i class="far fa-clock me-1"></i>
                                            <?php echo date('M d, H:i', strtotime($event['event_date'])); ?>
                                        </small>
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill">
                                        <?php echo htmlspecialchars($event['location']); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
<script src="assets/js/dashboard.js"></script>
