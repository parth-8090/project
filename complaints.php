<?php
require_once 'config/config.php';
requireStudent();

$conn = getDBConnection();
$student_id = $_SESSION['user_id'];

// Get student's complaints
$stmt = $conn->prepare("
    SELECT * FROM complaints
    WHERE student_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$student_id]);
$complaints = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12" data-aos="fade-down">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="h3 fw-bold mb-1">Complaints</h1>
                    <p class="text-muted mb-0">Track and manage your complaints</p>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#complaintModal">
                    <i class="fas fa-plus me-2"></i> Submit Complaint
                </button>
            </div>
        </div>
    </div>
    
    <div class="row">
        <?php if (empty($complaints)): ?>
            <div class="col-12">
                <div class="empty-state" data-aos="fade-up">
                    <div class="empty-state-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <h4 class="fw-bold mb-2">No Complaints</h4>
                    <p class="text-muted mb-0">You haven't submitted any complaints yet.</p>
                    <button class="btn btn-outline-primary mt-3" data-bs-toggle="modal" data-bs-target="#complaintModal">
                        Submit a Complaint
                    </button>
                </div>
            </div>
        <?php else: ?>
            <div class="col-12">
                <?php foreach ($complaints as $index => $complaint): ?>
                <div class="card border-0 shadow-sm mb-3" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <h5 class="fw-bold mb-0 me-3"><?php echo htmlspecialchars($complaint['title']); ?></h5>
                                    <?php if ($complaint['category']): ?>
                                        <span class="badge bg-info-subtle text-info rounded-pill small"><?php echo htmlspecialchars($complaint['category']); ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <p class="text-secondary mb-3"><?php echo nl2br(htmlspecialchars($complaint['description'])); ?></p>
                                
                                <div class="d-flex align-items-center text-muted small">
                                    <i class="far fa-clock me-1"></i>
                                    Submitted: <?php echo formatDateTime($complaint['created_at']); ?>
                                </div>
                            </div>
                            <div class="ms-3 text-end">
                                <span class="badge bg-<?php 
                                    echo $complaint['status'] === 'resolved' ? 'success' : 
                                        ($complaint['status'] === 'in_progress' ? 'warning' : 
                                        ($complaint['status'] === 'rejected' ? 'danger' : 'secondary')); 
                                ?>-subtle text-<?php 
                                    echo $complaint['status'] === 'resolved' ? 'success' : 
                                        ($complaint['status'] === 'in_progress' ? 'warning' : 
                                        ($complaint['status'] === 'rejected' ? 'danger' : 'secondary')); 
                                ?> rounded-pill px-3 py-2">
                                    <?php echo ucfirst(str_replace('_', ' ', $complaint['status'])); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Complaint Modal -->
<div class="modal fade" id="complaintModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Submit Complaint</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="complaintForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" required placeholder="Brief title of your complaint">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Category</label>
                        <select class="form-select" name="category">
                            <option value="">Select Category</option>
                            <option value="Infrastructure">Infrastructure</option>
                            <option value="Academic">Academic</option>
                            <option value="Administrative">Administrative</option>
                            <option value="Hostel">Hostel</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="description" rows="5" required placeholder="Detailed description of the issue..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Submit Complaint</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="assets/js/complaints.js"></script>
<?php include 'includes/footer.php'; ?>
