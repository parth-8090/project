<?php
require_once 'config/config.php';
requireBusiness();

$conn = getDBConnection();
$business_id = $_SESSION['user_id'];

// Get business stats
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM jobs WHERE business_id = ? AND status = 'active'");
$stmt->execute([$business_id]);
$active_jobs = $stmt->fetch()['count'];

$stmt = $conn->prepare("SELECT COUNT(*) as count FROM applications a JOIN jobs j ON a.job_id = j.id WHERE j.business_id = ? AND a.status = 'pending'");
$stmt->execute([$business_id]);
$pending_applications = $stmt->fetch()['count'];

$stmt = $conn->prepare("SELECT COUNT(*) as count FROM applications a JOIN jobs j ON a.job_id = j.id WHERE j.business_id = ? AND a.status = 'approved'");
$stmt->execute([$business_id]);
$approved_applications = $stmt->fetch()['count'];

// Get recent applications
$stmt = $conn->prepare("
    SELECT a.*, j.title as job_title, s.full_name, s.email, s.department, s.enrollment_no, s.points
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    JOIN students s ON a.student_id = s.id
    WHERE j.business_id = ?
    ORDER BY a.applied_at DESC
    LIMIT 10
");
$stmt->execute([$business_id]);
$recent_applications = $stmt->fetchAll();

$page_title = 'Business Dashboard';
include 'includes/header.php';
?>

<div class="container py-5">
    <!-- Welcome Section -->
    <div class="row mb-5 align-items-center" data-aos="fade-down">
        <div class="col-12">
            <h1 class="page-title h3 fw-bold mb-2">
                <span class="text-gradient">Business Dashboard</span>
            </h1>
            <p class="text-muted mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>! Manage your job postings and applications efficiently.</p>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
            <div class="stat-card h-100 border-0 shadow-sm overflow-hidden transition-hover bg-white rounded-4">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle bg-primary-subtle p-3 me-3 text-primary">
                        <i class="fas fa-briefcase fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-1">Active Jobs</h6>
                        <h2 class="mb-0 fw-bold display-6 counter text-dark" data-target="<?php echo $active_jobs; ?>"><?php echo $active_jobs; ?></h2>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-2 px-4">
                    <a href="business_jobs.php" class="text-decoration-none small fw-medium text-primary stretched-link">
                        View Details <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
            <div class="stat-card h-100 border-0 shadow-sm overflow-hidden transition-hover bg-white rounded-4">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle bg-warning-subtle p-3 me-3 text-warning">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-1">Pending Applications</h6>
                        <h2 class="mb-0 fw-bold display-6 counter text-dark" data-target="<?php echo $pending_applications; ?>"><?php echo $pending_applications; ?></h2>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-2 px-4">
                    <a href="business_applications.php" class="text-decoration-none small fw-medium text-primary stretched-link">
                        View Applications <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
            <div class="stat-card h-100 border-0 shadow-sm overflow-hidden transition-hover bg-white rounded-4">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle bg-success-subtle p-3 me-3 text-success">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-1">Approved Applications</h6>
                        <h2 class="mb-0 fw-bold display-6 counter text-dark" data-target="<?php echo $approved_applications; ?>"><?php echo $approved_applications; ?></h2>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-2 px-4">
                    <a href="business_applications.php?status=approved" class="text-decoration-none small fw-medium text-primary stretched-link">
                        View Approved <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row mb-5">
        <div class="col-12 mb-3">
            <h4 class="section-title h4 fw-bold mb-4 border-start border-4 border-primary ps-3" data-aos="fade-right">Quick Actions</h4>
        </div>
        
        <div class="col-md-4 mb-3" data-aos="zoom-in" data-aos-delay="100">
            <a href="post_job.php" class="card border-0 shadow-sm h-100 text-decoration-none transition-hover text-center p-4 hover-lift">
                <div class="card-body">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 60px; height: 60px;">
                        <i class="fas fa-plus fa-lg"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Post New Job</h5>
                    <p class="text-muted small mb-0">Create a new job posting to find talent</p>
                </div>
            </a>
        </div>
        
        <div class="col-md-4 mb-3" data-aos="zoom-in" data-aos-delay="200">
            <a href="business_applications.php" class="card border-0 shadow-sm h-100 text-decoration-none transition-hover text-center p-4 hover-lift">
                <div class="card-body">
                    <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 60px; height: 60px;">
                        <i class="fas fa-list fa-lg"></i>
                    </div>
                    <h5 class="fw-bold text-dark">View Applications</h5>
                    <p class="text-muted small mb-0">Manage incoming job applications</p>
                </div>
            </a>
        </div>
        
        <div class="col-md-4 mb-3" data-aos="zoom-in" data-aos-delay="300">
            <a href="business_jobs.php" class="card border-0 shadow-sm h-100 text-decoration-none transition-hover text-center p-4 hover-lift">
                <div class="card-body">
                    <div class="bg-purple text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 60px; height: 60px; background-color: #6f42c1;">
                        <i class="fas fa-tasks fa-lg"></i>
                    </div>
                    <h5 class="fw-bold text-dark">My Jobs</h5>
                    <p class="text-muted small mb-0">View and edit your existing job postings</p>
                </div>
            </a>
        </div>
    </div>
    
    <!-- Recent Applications -->
    <?php if (!empty($recent_applications)): ?>
    <div class="card border-0 shadow-sm overflow-hidden rounded-4" data-aos="fade-up">
        <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Recent Applications</h5>
            <a href="business_applications.php" class="btn btn-sm btn-outline-primary rounded-pill px-3">View All</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted small text-uppercase fw-bold">Student</th>
                            <th class="py-3 text-muted small text-uppercase fw-bold">Job Title</th>
                            <th class="py-3 text-muted small text-uppercase fw-bold">Department</th>
                            <th class="py-3 text-muted small text-uppercase fw-bold">Points</th>
                            <th class="py-3 text-muted small text-uppercase fw-bold">Status</th>
                            <th class="py-3 text-muted small text-uppercase fw-bold">Applied Date</th>
                            <th class="pe-4 py-3 text-end text-muted small text-uppercase fw-bold">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_applications as $app): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle sm bg-primary-subtle text-primary me-3">
                                        <?php echo strtoupper(substr($app['full_name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($app['full_name']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($app['enrollment_no']); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="fw-medium text-primary"><?php echo htmlspecialchars($app['job_title']); ?></td>
                            <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($app['department']); ?></span></td>
                            <td>
                                <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill">
                                    <i class="fas fa-star me-1"></i> <?php echo $app['points']; ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                $statusClass = match($app['status']) {
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    'pending' => 'warning',
                                    default => 'secondary'
                                };
                                ?>
                                <span class="badge bg-<?php echo $statusClass; ?>-subtle text-<?php echo $statusClass; ?> rounded-pill text-capitalize">
                                    <?php echo htmlspecialchars($app['status']); ?>
                                </span>
                            </td>
                            <td class="text-muted small"><?php echo formatDate($app['applied_at']); ?></td>
                            <td class="pe-4 text-end">
                                <a href="view_student.php?student_id=<?php echo $app['student_id']; ?>&application_id=<?php echo $app['id']; ?>" class="btn btn-sm btn-light text-primary border shadow-sm">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script src="assets/js/dashboard.js"></script>
<?php include 'includes/footer.php'; ?>
