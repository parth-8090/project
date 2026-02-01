<?php
require_once 'config/config.php';
requireStudent();

$job_id = $_GET['id'] ?? 0;
$conn = getDBConnection();

// Get job details
$stmt = $conn->prepare("
    SELECT j.*, b.business_name, b.business_type, b.email as business_email
    FROM jobs j
    JOIN businesses b ON j.business_id = b.id
    WHERE j.id = ? AND j.status = 'active'
");
$stmt->execute([$job_id]);
$job = $stmt->fetch();

if (!$job) {
    header('Location: jobs.php');
    exit;
}

// Check if already applied
$stmt = $conn->prepare("SELECT * FROM applications WHERE job_id = ? AND student_id = ?");
$stmt->execute([$job_id, $_SESSION['user_id']]);
$application = $stmt->fetch();

// Get student info for matching
$stmt = $conn->prepare("SELECT department FROM students WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$student = $stmt->fetch();

include 'includes/header.php';
?>

<div class="container py-4">
    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm" data-aos="fade-up">
                <div class="card-body p-4">
                    <div class="mb-4">
                        <a href="jobs.php" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                            <i class="fas fa-arrow-left me-1"></i> Back to Jobs
                        </a>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                        <div>
                            <h2 class="h3 fw-bold mb-2 text-gradient"><?php echo htmlspecialchars($job['title']); ?></h2>
                            <p class="text-muted mb-0">
                                <i class="fas fa-building me-1"></i> <?php echo htmlspecialchars($job['business_name']); ?>
                                <span class="mx-2">•</span>
                                <i class="fas fa-tag me-1"></i> <?php echo htmlspecialchars($job['business_type']); ?>
                            </p>
                        </div>
                        <div class="text-end">
                             <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill mb-1"><?php echo htmlspecialchars($job['job_type']); ?></span>
                             <br>
                             <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill"><?php echo htmlspecialchars($job['period']); ?></span>
                        </div>
                    </div>
                    
                    <hr class="opacity-10 my-4">
                    
                    <div class="mb-5">
                        <h5 class="fw-bold mb-3"><i class="fas fa-align-left text-primary me-2"></i>Job Description</h5>
                        <div class="text-secondary lh-lg">
                            <?php echo nl2br(htmlspecialchars($job['description'])); ?>
                        </div>
                    </div>
                    
                    <div class="mb-5">
                        <h5 class="fw-bold mb-3"><i class="fas fa-tools text-primary me-2"></i>Required Skills</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <?php 
                            $skills = explode(',', $job['required_skills']);
                            foreach($skills as $skill): 
                                $skill = trim($skill);
                                if(empty($skill)) continue;
                            ?>
                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><?php echo htmlspecialchars($skill); ?></span>
                            <?php endforeach; ?>
                            <?php if(empty($skills) || (count($skills) == 1 && empty($skills[0]))): ?>
                                <p class="text-muted"><?php echo htmlspecialchars($job['required_skills']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mb-5">
                        <h5 class="fw-bold mb-3"><i class="fas fa-info-circle text-primary me-2"></i>Job Details</h5>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <div class="small text-muted text-uppercase fw-bold mb-1">Period</div>
                                    <div class="fw-medium"><i class="fas fa-clock text-secondary me-2"></i><?php echo htmlspecialchars($job['period']); ?></div>
                                </div>
                            </div>
                            <?php if ($job['time_required']): ?>
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <div class="small text-muted text-uppercase fw-bold mb-1">Time Required</div>
                                    <div class="fw-medium"><i class="fas fa-hourglass-half text-secondary me-2"></i><?php echo htmlspecialchars($job['time_required']); ?></div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if ($job['number_of_employees']): ?>
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <div class="small text-muted text-uppercase fw-bold mb-1">Employees Needed</div>
                                    <div class="fw-medium"><i class="fas fa-users text-secondary me-2"></i><?php echo $job['number_of_employees']; ?></div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <div class="small text-muted text-uppercase fw-bold mb-1">Posted On</div>
                                    <div class="fw-medium"><i class="fas fa-calendar text-secondary me-2"></i><?php echo formatDate($job['posted_at']); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($application): ?>
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            <i class="fas fa-check-circle fs-4 me-3"></i>
                            <div>
                                <div class="fw-bold">Application Submitted</div>
                                <div>Status: <span class="badge bg-success"><?php echo ucfirst($application['status']); ?></span></div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="d-grid">
                            <button class="btn btn-primary btn-lg" id="applyJobBtn" data-job-id="<?php echo $job['id']; ?>">
                                <i class="fas fa-paper-plane me-2"></i> Apply for this Job
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4" data-aos="fade-left">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Quick Summary</h5>
                    <hr class="opacity-10 mb-4">
                    
                    <div class="mb-3">
                        <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Business</small>
                        <div class="fw-medium"><?php echo htmlspecialchars($job['business_name']); ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Industry</small>
                        <div class="fw-medium"><?php echo htmlspecialchars($job['business_type']); ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Job Type</small>
                        <div class="fw-medium"><?php echo htmlspecialchars($job['job_type']); ?></div>
                    </div>
                    
                    <div class="mb-0">
                        <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Duration</small>
                        <div class="fw-medium"><?php echo htmlspecialchars($job['period']); ?></div>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-lightbulb me-2"></i>Tips</h5>
                    <p class="small opacity-75 mb-0">
                        Make sure your profile is up to date before applying. Businesses often check your skills and interests to see if you're a good fit.
                    </p>
                    <a href="profile.php" class="btn btn-sm btn-light mt-3 w-100 text-primary fw-bold">Update Profile</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/jobs.js?v=<?php echo time(); ?>"></script>
<?php include 'includes/footer.php'; ?>
