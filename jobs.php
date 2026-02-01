<?php
require_once 'config/config.php';
requireStudent();

$conn = getDBConnection();
$department = $_SESSION['department'];

// Get available jobs (matching student's department or general)
$stmt = $conn->prepare("
    SELECT j.*, b.business_name, b.business_type,
           (SELECT COUNT(*) FROM applications WHERE job_id = j.id AND student_id = ?) as has_applied
    FROM jobs j
    JOIN businesses b ON j.business_id = b.id
    WHERE j.status = 'active'
    ORDER BY j.posted_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$jobs = $stmt->fetchAll();

$student_dept = $_SESSION['department'];

$page_title = "Jobs";
require_once 'includes/header.php';
?>

<div class="container py-4">
    <div class="row mb-5 align-items-center" data-aos="fade-down">
        <div class="col-md-8">
            <h1 class="page-title h3 fw-bold mb-2">
                <span class="text-gradient">Available Jobs</span>
            </h1>
            <p class="text-muted mb-0">
                Find the perfect opportunity in <span class="badge bg-primary-subtle text-primary rounded-pill"><?php echo htmlspecialchars($department); ?></span>
            </p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white border-end-0">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input type="text" id="jobSearch" class="form-control border-start-0 ps-0" placeholder="Search jobs...">
            </div>
        </div>
    </div>
    
    <div class="row g-4" id="jobsContainer">
        <?php if (empty($jobs)): ?>
            <div class="col-12">
                <div class="empty-state" data-aos="fade-up">
                    <div class="empty-state-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h4 class="fw-bold mb-2">No Jobs Available</h4>
                    <p class="text-muted mb-0">There are currently no job openings for your department. Please check back later!</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($jobs as $index => $job): ?>
                <div class="col-md-6 col-lg-4 job-item" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                    <article class="job-card">
                        <div class="job-card-body">
                            <header class="job-header">
                                <div class="job-company-info">
                                    <div class="job-logo">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div class="job-title-group">
                                        <h2 class="job-title" title="<?php echo htmlspecialchars($job['title']); ?>">
                                            <?php echo htmlspecialchars($job['title']); ?>
                                        </h2>
                                        <div class="job-company">
                                            <?php echo htmlspecialchars($job['business_name']); ?>
                                        </div>
                                    </div>
                                </div>
                                <span class="job-type-badge">
                                    <?php echo ucfirst(htmlspecialchars($job['job_type'])); ?>
                                </span>
                            </header>
                            
                            <div class="job-meta-tags">
                                <span class="job-tag">
                                    <i class="fas fa-map-marker-alt"></i><?php echo htmlspecialchars($job['location'] ?? 'On Campus'); ?>
                                </span>
                                <span class="job-tag tag-salary">
                                    <i class="fas fa-money-bill-wave"></i><?php echo htmlspecialchars($job['salary'] ?? 'Negotiable'); ?>
                                </span>
                                <?php if ($job['number_of_employees'] > 0): ?>
                                    <span class="job-tag tag-openings">
                                        <i class="fas fa-users"></i>Openings: <?php echo $job['number_of_employees']; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <p class="job-description">
                                <?php echo substr(htmlspecialchars($job['description']), 0, 150) . (strlen($job['description']) > 150 ? '...' : ''); ?>
                            </p>
                            
                            <footer class="job-footer">
                                <small class="job-posted-date">
                                    <i class="far fa-clock me-1"></i> <?php echo date('M d', strtotime($job['posted_at'])); ?>
                                </small>
                                <a href="job_details.php?id=<?php echo $job['id']; ?>" class="btn btn-primary job-action-btn">
                                    View Details
                                </a>
                            </footer>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
<script src="assets/js/jobs.js?v=<?php echo time(); ?>"></script>
