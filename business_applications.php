<?php
require_once 'config/config.php';
requireBusiness();

$conn = getDBConnection();
$business_id = $_SESSION['user_id'];

// Get all applications for this business
$stmt = $conn->prepare("
    SELECT a.*, j.title as job_title, j.job_type, s.full_name, s.email, s.department, s.enrollment_no, s.points, s.linkedin_link, s.github_link, s.skills
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    JOIN students s ON a.student_id = s.id
    WHERE j.business_id = ?
    ORDER BY a.applied_at DESC
");
$stmt->execute([$business_id]);
$applications = $stmt->fetchAll();

$page_title = 'Job Applications';
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5" data-aos="fade-down">
        <div>
            <h1 class="page-title h3 fw-bold mb-1">
                <span class="text-gradient">Job Applications</span>
            </h1>
            <p class="text-muted mb-0">Review and manage student applications</p>
        </div>
        <div class="d-flex gap-2">
            <a href="business_jobs.php" class="btn btn-outline-primary rounded-pill">
                <i class="fas fa-briefcase me-2"></i>My Jobs
            </a>
            <a href="post_job.php" class="btn btn-primary shadow-sm rounded-pill">
                <i class="fas fa-plus me-2"></i>Post Job
            </a>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-12" data-aos="fade-up">
            <div class="card border-0 shadow-sm overflow-hidden rounded-4">
                <div class="card-body p-0">
                    <?php if (empty($applications)): ?>
                        <div class="text-center p-5">
                            <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                                <i class="fas fa-file-alt fa-2x"></i>
                            </div>
                            <h4 class="fw-bold">No Applications Yet</h4>
                            <p class="text-muted mb-4">You haven't received any applications for your jobs yet.</p>
                            <a href="post_job.php" class="btn btn-primary px-4 rounded-pill">Post a New Job</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3 px-4 border-0 text-muted small text-uppercase fw-bold">Student</th>
                                        <th class="py-3 px-4 border-0 text-muted small text-uppercase fw-bold">Job Title</th>
                                        <th class="py-3 px-4 border-0 text-muted small text-uppercase fw-bold">Department</th>
                                        <th class="py-3 px-4 border-0 text-muted small text-uppercase fw-bold">Points</th>
                                        <th class="py-3 px-4 border-0 text-muted small text-uppercase fw-bold">Status</th>
                                        <th class="py-3 px-4 border-0 text-muted small text-uppercase fw-bold">Applied On</th>
                                        <th class="py-3 px-4 border-0 text-end text-muted small text-uppercase fw-bold">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($applications as $app): ?>
                                    <tr>
                                        <td class="px-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle sm bg-primary-subtle text-primary me-3">
                                                    <span class="fw-bold"><?php echo strtoupper(substr($app['full_name'], 0, 1)); ?></span>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($app['full_name']); ?></div>
                                                    <small class="text-muted"><?php echo htmlspecialchars($app['enrollment_no']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4">
                                            <div class="fw-medium text-primary"><?php echo htmlspecialchars($app['job_title']); ?></div>
                                            <span class="badge bg-light text-muted border fw-normal small"><?php echo htmlspecialchars($app['job_type']); ?></span>
                                        </td>
                                        <td class="px-4 text-muted"><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($app['department']); ?></span></td>
                                        <td class="px-4">
                                            <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-3">
                                                <i class="fas fa-star me-1 small"></i> <?php echo $app['points']; ?>
                                            </span>
                                        </td>
                                        <td class="px-4">
                                            <?php 
                                            $statusClass = match($app['status']) {
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                default => 'warning'
                                            };
                                            ?>
                                            <span class="badge bg-<?php echo $statusClass; ?>-subtle text-<?php echo $statusClass; ?> rounded-pill text-capitalize px-3">
                                                <?php echo htmlspecialchars($app['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-4 text-muted small">
                                            <i class="far fa-calendar me-1"></i> <?php echo formatDate($app['applied_at']); ?>
                                        </td>
                                        <td class="px-4 text-end">
                                            <a href="view_student.php?student_id=<?php echo $app['student_id']; ?>&application_id=<?php echo $app['id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                                                <i class="fas fa-eye me-1"></i> View Profile
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/business.js?v=<?php echo time(); ?>"></script>
<?php include 'includes/footer.php'; ?>
