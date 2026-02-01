<?php
require_once 'config/config.php';
requireBusiness();

$conn = getDBConnection();
$business_id = $_SESSION['user_id'];

// Get all jobs posted by this business
$stmt = $conn->prepare("
    SELECT j.*, 
           (SELECT COUNT(*) FROM applications WHERE job_id = j.id) as application_count
    FROM jobs j
    WHERE j.business_id = ? AND j.status = 'active'
    ORDER BY j.posted_at DESC
");
$stmt->execute([$business_id]);
$jobs = $stmt->fetchAll();

$page_title = 'My Jobs';
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-down">
        <div>
            <h1 class="fw-bold mb-1">My Job Postings</h1>
            <p class="text-muted mb-0">Manage your active and past job listings</p>
        </div>
        <a href="post_job.php" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-2"></i>Post New Job
        </a>
    </div>

    <!-- Success/Error Alerts -->
    <div id="successAlert" class="alert alert-success border-0 shadow-sm mb-4" style="display: none;"></div>
    <div id="errorAlert" class="alert alert-danger border-0 shadow-sm mb-4" style="display: none;"></div>

    <div class="row g-4">
        <?php if (empty($jobs)): ?>
            <div class="col-12">
                <div class="card border-0 shadow-sm text-center p-5">
                    <div class="card-body">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-briefcase fa-2x"></i>
                        </div>
                        <h4 class="fw-bold">No Jobs Posted Yet</h4>
                        <p class="text-muted mb-4">Get started by posting your first job opportunity.</p>
                        <a href="post_job.php" class="btn btn-primary px-4">Post Your First Job</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="col-12">
                <?php foreach ($jobs as $index => $job): ?>
                <article class="job-card horizontal mb-4">
                    <div class="job-card-body">
                        <!-- Main Content -->
                        <div class="job-main-content">
                            <header class="job-header mb-2 justify-content-start align-items-center">
                                <h2 class="job-title fs-4 mb-0 me-3">
                                    <?php echo htmlspecialchars($job['title']); ?>
                                </h2>
                                <?php 
                                $statusClass = match($job['status']) {
                                    'active' => 'success',
                                    'completed' => 'secondary',
                                    default => 'warning'
                                };
                                ?>
                                <span class="badge bg-<?php echo $statusClass; ?>-subtle text-<?php echo $statusClass; ?> rounded-pill border border-<?php echo $statusClass; ?>-subtle px-3">
                                    <?php echo ucfirst($job['status']); ?>
                                </span>
                            </header>
                            
                            <div class="job-meta-tags mb-3">
                                <span class="job-tag">
                                    <i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($job['job_type']); ?>
                                </span>
                                <span class="job-tag">
                                    <i class="fas fa-clock"></i> <?php echo htmlspecialchars($job['period']); ?>
                                </span>
                                <span class="job-tag">
                                    <i class="fas fa-calendar"></i> Posted: <?php echo formatDate($job['posted_at']); ?>
                                </span>
                                <?php if ($job['number_of_employees'] > 0): ?>
                                    <span class="job-tag tag-openings">
                                        <i class="fas fa-users"></i> Openings: <?php echo $job['number_of_employees']; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <p class="job-description mb-3">
                                <?php echo htmlspecialchars(substr($job['description'], 0, 200)) . (strlen($job['description']) > 200 ? '...' : ''); ?>
                            </p>
                            
                            <div class="d-flex align-items-center text-muted small">
                                <i class="fas fa-tools me-2 text-primary"></i>
                                <span class="fw-medium">Required Skills:</span>
                                <span class="ms-2"><?php echo htmlspecialchars($job['required_skills']); ?></span>
                            </div>
                        </div>
                        
                        <!-- Sidebar Actions -->
                        <div class="job-sidebar">
                            <div class="job-audit-box">
                                <div class="job-audit-number"><?php echo $job['application_count']; ?></div>
                                <div class="job-audit-label">Applications</div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <a href="business_applications.php?job_id=<?php echo $job['id']; ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-users me-2"></i> View Apps
                                </a>
                                
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-<?php echo $job['status'] === 'active' ? 'secondary' : 'success'; ?> btn-sm flex-grow-1" 
                                            onclick="toggleJobStatus(<?php echo $job['id']; ?>, '<?php echo $job['status']; ?>')">
                                        <i class="fas fa-<?php echo $job['status'] === 'active' ? 'lock' : 'lock-open'; ?> me-2"></i> 
                                        <?php echo $job['status'] === 'active' ? 'Close' : 'Reopen'; ?>
                                    </button>
                                    
                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteJob(<?php echo $job['id']; ?>)" title="Delete Job">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="assets/js/business.js?v=<?php echo time(); ?>"></script>
<?php include 'includes/footer.php'; ?>
