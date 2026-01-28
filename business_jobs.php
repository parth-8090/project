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
    WHERE j.business_id = ?
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
    
    <div class="row g-4">
        <?php if (empty($jobs)): ?>
            <div class="col-12" data-aos="fade-up">
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
                <!-- Success/Error Alerts -->
                <div id="successAlert" class="alert alert-success border-0 shadow-sm mb-4" style="display: none;"></div>
                <div id="errorAlert" class="alert alert-danger border-0 shadow-sm mb-4" style="display: none;"></div>

                <?php foreach ($jobs as $index => $job): ?>
                <div class="card border-0 shadow-sm mb-3 overflow-hidden transition-hover" data-aos="fade-up" data-aos-delay="<?php echo min($index * 50, 500); ?>">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <div class="d-flex align-items-center mb-2">
                                    <h4 class="fw-bold mb-0 me-3"><?php echo htmlspecialchars($job['title']); ?></h4>
                                    <?php 
                                    $statusClass = match($job['status']) {
                                        'active' => 'success',
                                        'closed' => 'secondary',
                                        default => 'warning'
                                    };
                                    ?>
                                    <span class="badge bg-<?php echo $statusClass; ?>-subtle text-<?php echo $statusClass; ?> rounded-pill border border-<?php echo $statusClass; ?>-subtle px-3">
                                        <?php echo ucfirst($job['status']); ?>
                                    </span>
                                </div>
                                
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <span class="badge bg-light text-muted border fw-normal">
                                        <i class="fas fa-briefcase me-1"></i> <?php echo htmlspecialchars($job['job_type']); ?>
                                    </span>
                                    <span class="badge bg-light text-muted border fw-normal">
                                        <i class="fas fa-clock me-1"></i> <?php echo htmlspecialchars($job['period']); ?>
                                    </span>
                                    <span class="badge bg-light text-muted border fw-normal">
                                        <i class="fas fa-calendar me-1"></i> Posted: <?php echo formatDate($job['posted_at']); ?>
                                    </span>
                                </div>
                                
                                <p class="text-muted mb-3"><?php echo htmlspecialchars(substr($job['description'], 0, 200)) . (strlen($job['description']) > 200 ? '...' : ''); ?></p>
                                
                                <div class="d-flex align-items-center text-muted small">
                                    <i class="fas fa-tools me-2 text-primary"></i>
                                    <span class="fw-medium">Required Skills:</span>
                                    <span class="ms-2"><?php echo htmlspecialchars($job['required_skills']); ?></span>
                                </div>
                            </div>
                            
                            <div class="col-lg-4 mt-4 mt-lg-0">
                                <div class="bg-light rounded-3 p-3 text-center mb-3 border">
                                    <h3 class="fw-bold text-primary mb-0"><?php echo $job['application_count']; ?></h3>
                                    <div class="text-muted small text-uppercase fw-bold">Applications</div>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <a href="business_applications.php?job_id=<?php echo $job['id']; ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-users me-2"></i> View Applications
                                    </a>
                                    
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-outline-<?php echo $job['status'] === 'active' ? 'secondary' : 'success'; ?> flex-grow-1" 
                                                onclick="toggleJobStatus(<?php echo $job['id']; ?>, '<?php echo $job['status']; ?>')">
                                            <i class="fas fa-<?php echo $job['status'] === 'active' ? 'lock' : 'lock-open'; ?> me-2"></i> 
                                            <?php echo $job['status'] === 'active' ? 'Close' : 'Reopen'; ?>
                                        </button>
                                        
                                        <button class="btn btn-outline-danger" onclick="deleteJob(<?php echo $job['id']; ?>)" title="Delete Job">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="assets/js/business.js"></script>
<?php include 'includes/footer.php'; ?>
