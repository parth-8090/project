<?php
require_once 'config/config.php';
requireBusiness();

$student_id = $_GET['student_id'] ?? 0;
$application_id = $_GET['application_id'] ?? 0;

$conn = getDBConnection();

// Get student details
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    header('Location: business_applications.php');
    exit;
}

// Get application details
$stmt = $conn->prepare("
    SELECT a.*, j.title as job_title, j.id as job_id
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    WHERE a.id = ? AND j.business_id = ?
");
$stmt->execute([$application_id, $_SESSION['user_id']]);
$application = $stmt->fetch();

if (!$application) {
    header('Location: business_applications.php');
    exit;
}

// Get student's job history (approved jobs)
$stmt = $conn->prepare("
    SELECT COUNT(*) as count, AVG(br.rating) as avg_rating
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    LEFT JOIN business_reviews br ON br.student_id = a.student_id AND br.job_id = j.id
    WHERE a.student_id = ? AND a.status = 'approved'
");
$stmt->execute([$student_id]);
$job_stats = $stmt->fetch();

// Get reviews for this student
$stmt = $conn->prepare("
    SELECT br.*, b.business_name, j.title as job_title
    FROM business_reviews br
    JOIN businesses b ON br.business_id = b.id
    JOIN jobs j ON br.job_id = j.id
    WHERE br.student_id = ?
    ORDER BY br.created_at DESC
    LIMIT 5
");
$stmt->execute([$student_id]);
$reviews = $stmt->fetchAll();

$page_title = 'Student Profile';
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="mb-4" data-aos="fade-down">
        <a href="business_applications.php" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i>Back to Applications
        </a>
    </div>

    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-lg-4" data-aos="fade-right">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body p-0">
                    <div class="bg-primary bg-opacity-10 p-4 text-center pb-5">
                        <div class="position-relative d-inline-block mb-3">
                            <div class="rounded-circle bg-white p-1 shadow-sm">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 120px; height: 120px; font-size: 3rem;">
                                    <?php echo strtoupper(substr($student['full_name'], 0, 1)); ?>
                                </div>
                            </div>
                        </div>
                        <h3 class="h4 fw-bold mb-1"><?php echo htmlspecialchars($student['full_name']); ?></h3>
                        <p class="text-muted mb-2"><?php echo htmlspecialchars($student['enrollment_no']); ?></p>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill shadow-sm">
                            <i class="fas fa-star me-1"></i> <?php echo $student['points']; ?> Points
                        </span>
                    </div>
                    
                    <div class="p-4">
                        <h5 class="fw-bold border-bottom pb-2 mb-3">Contact Info</h5>
                        <ul class="list-unstyled mb-4">
                            <li class="mb-3 d-flex align-items-center text-muted">
                                <i class="fas fa-envelope me-3 text-primary"></i>
                                <?php echo htmlspecialchars($student['email']); ?>
                            </li>
                            <li class="mb-3 d-flex align-items-center text-muted">
                                <i class="fas fa-building me-3 text-primary"></i>
                                <?php echo htmlspecialchars($student['department']); ?>
                            </li>
                            <li class="mb-3 d-flex align-items-center text-muted">
                                <i class="fas fa-calendar-alt me-3 text-primary"></i>
                                Joined: <?php echo $student['year_of_admission']; ?>
                            </li>
                        </ul>

                        <?php if ($student['linkedin_link'] || $student['github_link']): ?>
                            <h5 class="fw-bold border-bottom pb-2 mb-3">Social Profiles</h5>
                            <div class="d-grid gap-2">
                                <?php if ($student['linkedin_link']): ?>
                                <a href="<?php echo htmlspecialchars($student['linkedin_link']); ?>" target="_blank" class="btn btn-outline-primary">
                                    <i class="fab fa-linkedin me-2"></i> LinkedIn
                                </a>
                                <?php endif; ?>
                                
                                <?php if ($student['github_link']): ?>
                                <a href="<?php echo htmlspecialchars($student['github_link']); ?>" target="_blank" class="btn btn-outline-dark">
                                    <i class="fab fa-github me-2"></i> GitHub
                                </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Application Status Card -->
            <div class="card border-0 shadow-sm mb-4 overflow-hidden" data-aos="fade-up">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold mb-0 text-primary">Application Details</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <p class="mb-1 text-muted">Applying for position:</p>
                            <h4 class="fw-bold mb-3"><?php echo htmlspecialchars($application['job_title']); ?></h4>
                            <div class="d-flex gap-3 mb-3 mb-md-0">
                                <div>
                                    <small class="text-muted d-block">Applied On</small>
                                    <span class="fw-medium"><?php echo formatDateTime($application['applied_at']); ?></span>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Current Status</small>
                                    <?php 
                                    $statusClass = match($application['status']) {
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        default => 'warning'
                                    };
                                    ?>
                                    <span class="badge bg-<?php echo $statusClass; ?>-subtle text-<?php echo $statusClass; ?> border border-<?php echo $statusClass; ?>-subtle">
                                        <?php echo ucfirst($application['status']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <?php if ($application['status'] === 'pending'): ?>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-success" onclick="updateApplication(<?php echo $application_id; ?>, 'approved')">
                                        <i class="fas fa-check me-2"></i>Approve
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="updateApplication(<?php echo $application_id; ?>, 'rejected')">
                                        <i class="fas fa-times me-2"></i>Reject
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Skills & Interests -->
            <div class="row g-4 mb-4">
                <?php if ($student['skills']): ?>
                <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3"><i class="fas fa-tools text-primary me-2"></i>Skills</h5>
                            <div class="d-flex flex-wrap gap-2">
                                <?php 
                                $skills = explode(',', $student['skills']);
                                foreach($skills as $skill): 
                                ?>
                                    <span class="badge bg-light text-dark border fw-normal py-2 px-3"><?php echo trim(htmlspecialchars($skill)); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($student['interests']): ?>
                <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3"><i class="fas fa-heart text-danger me-2"></i>Interests</h5>
                            <p class="text-muted mb-0"><?php echo nl2br(htmlspecialchars($student['interests'])); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Job History & Stats -->
            <div class="card border-0 shadow-sm mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Work History</h5>
                    <div class="row g-4 text-center">
                        <div class="col-6">
                            <div class="bg-light rounded-3 p-3">
                                <h3 class="fw-bold text-primary mb-1"><?php echo $job_stats['count'] ?? 0; ?></h3>
                                <div class="text-muted small">Completed Jobs</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded-3 p-3">
                                <h3 class="fw-bold text-warning mb-1">
                                    <?php echo $job_stats['avg_rating'] ? round($job_stats['avg_rating'], 1) : '-'; ?>
                                </h3>
                                <div class="text-muted small">Average Rating</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($reviews)): ?>
            <div class="card border-0 shadow-sm mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold mb-0">Recent Reviews</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach ($reviews as $review): ?>
                        <div class="list-group-item p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($review['business_name']); ?></h6>
                                    <small class="text-muted"><?php echo htmlspecialchars($review['job_title']); ?></small>
                                </div>
                                <div class="text-warning">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= $review['rating'] ? '' : 'text-muted opacity-25'; ?> small"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <?php if ($review['review_text']): ?>
                                <p class="text-muted mb-0 small fst-italic">"<?php echo htmlspecialchars($review['review_text']); ?>"</p>
                            <?php endif; ?>
                            <div class="text-end mt-2">
                                <small class="text-muted" style="font-size: 0.75rem;"><?php echo formatDate($review['created_at']); ?></small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($application['status'] === 'approved'): ?>
            <div class="card border-0 shadow-sm bg-primary text-white" data-aos="fade-up" data-aos-delay="500">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-pen-nib me-2"></i>Leave a Review</h5>
                    <form id="reviewForm" data-job-id="<?php echo $application['job_id']; ?>" data-student-id="<?php echo $student_id; ?>">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label text-white-50 small text-uppercase fw-bold">Rating</label>
                                <select class="form-select border-0" name="rating" required>
                                    <option value="">Select...</option>
                                    <option value="5">5 - Excellent</option>
                                    <option value="4">4 - Very Good</option>
                                    <option value="3">3 - Good</option>
                                    <option value="2">2 - Fair</option>
                                    <option value="1">1 - Poor</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label text-white-50 small text-uppercase fw-bold">Feedback</label>
                                <textarea class="form-control border-0" name="review_text" rows="3" placeholder="Share your experience working with this student..."></textarea>
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-light text-primary fw-bold px-4 shadow-sm">Submit Review</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="assets/js/business.js"></script>
<?php include 'includes/footer.php'; ?>
