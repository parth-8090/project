<?php
require_once 'config/config.php';
requireStudent();

$conn = getDBConnection();
$student_id = $_SESSION['user_id'];

// Get all lost & found items
$stmt = $conn->prepare("
    SELECT lf.*, s.full_name, s.department
    FROM lost_found lf
    JOIN students s ON lf.student_id = s.id
    WHERE lf.status = 'open'
    ORDER BY lf.created_at DESC
");
$stmt->execute();
$items = $stmt->fetchAll();

$page_title = "Lost & Found";
require_once 'includes/header.php';
?>

<div class="container py-4">
    <div class="row mb-4" data-aos="fade-down">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title h3 fw-bold">Lost & Found</h1>
                    <p class="text-muted mb-0">Report and find lost items on campus.</p>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reportItemModal">
                    <i class="fas fa-plus me-2"></i> Report Item
                </button>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <?php if (empty($items)): ?>
            <div class="col-12">
                <div class="empty-state" data-aos="fade-up">
                    <div class="empty-state-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h4 class="fw-bold mb-2">No Items Reported</h4>
                    <p class="text-muted mb-0">Good news! No lost or found items have been reported recently.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($items as $index => $item): ?>
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge <?php echo $item['item_type'] === 'lost' ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success'; ?> rounded-pill px-3">
                                <?php echo ucfirst($item['item_type']); ?>
                            </span>
                            <small class="text-muted">
                                <i class="far fa-clock me-1"></i> <?php echo formatDate($item['created_at']); ?>
                            </small>
                        </div>
                        
                        <h5 class="card-title h5 fw-bold mb-2 text-truncate" title="<?php echo htmlspecialchars($item['item_name']); ?>">
                            <?php echo htmlspecialchars($item['item_name']); ?>
                        </h5>
                        
                        <p class="text-muted small mb-3">
                            <i class="fas fa-user me-1 text-primary"></i> <?php echo htmlspecialchars($item['full_name']); ?>
                            <?php if ($item['location']): ?>
                            <br>
                            <i class="fas fa-map-marker-alt me-1 text-danger mt-1"></i> <?php echo htmlspecialchars($item['location']); ?>
                            <?php endif; ?>
                        </p>
                        
                        <?php if ($item['description']): ?>
                        <p class="card-text small text-muted mb-4 flex-grow-1 line-clamp-3"><?php echo htmlspecialchars($item['description']); ?></p>
                        <?php endif; ?>
                        
                        <div class="mt-auto pt-3 border-top border-light">
                            <?php if ($item['student_id'] == $student_id): ?>
                            <button class="btn btn-success w-100 rounded-pill btn-sm" onclick="markResolved(<?php echo $item['id']; ?>)">
                                <i class="fas fa-check me-1"></i> Mark as Resolved
                            </button>
                            <?php else: ?>
                            <button class="btn btn-outline-primary w-100 rounded-pill btn-sm">
                                <i class="fas fa-envelope me-1"></i> Contact
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Report Item Modal -->
<div class="modal fade" id="reportItemModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Report Lost or Found Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="reportItemForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Item Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="item_type" required>
                            <option value="lost">Lost Item</option>
                            <option value="found">Found Item</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Item Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="item_name" required placeholder="e.g. Blue Backpack">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Provide details about the item..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" name="location" placeholder="Where was it lost/found?">
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Report Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
<script src="assets/js/lost_found.js"></script>

