<?php
require_once 'config/config.php';
requireStudent();

$conn = getDBConnection();

// Get all marketplace items
$stmt = $conn->prepare("
    SELECT m.*, s.full_name, s.department
    FROM marketplace_items m
    JOIN students s ON m.student_id = s.id
    WHERE m.status = 'available'
    ORDER BY m.posted_at DESC
");
$stmt->execute();
$items = $stmt->fetchAll();

$page_title = "Marketplace";
require_once 'includes/header.php';
?>

<div class="container py-4">
    <div class="row mb-5 align-items-center" data-aos="fade-down">
        <div class="col-md-8">
            <h1 class="page-title h3 fw-bold mb-2">
                <span class="text-gradient">Campus Marketplace</span>
            </h1>
            <p class="text-muted mb-0">Buy and sell items within the campus community.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#sellItemModal">
                <i class="fas fa-plus me-2"></i> Sell Item
            </button>
        </div>
    </div>
    
    <div class="row g-4">
        <?php if (empty($items)): ?>
            <div class="col-12">
                <div class="empty-state" data-aos="fade-up">
                    <div class="empty-state-icon">
                        <i class="fas fa-store-slash"></i>
                    </div>
                    <h4 class="fw-bold mb-2">Marketplace Empty</h4>
                    <p class="text-muted mb-0">No items available for sale right now. Be the first to list something!</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($items as $index => $item): ?>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                <div class="card h-100 marketplace-card border-0 shadow-sm hover-lift overflow-hidden">
                    <div class="position-relative">
                        <?php if ($item['image_path']): ?>
                        <img src="<?php echo htmlspecialchars($item['image_path']); ?>" class="card-img-top marketplace-img" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <?php else: ?>
                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                            <i class="fas fa-shopping-bag fa-3x text-secondary opacity-25"></i>
                        </div>
                        <?php endif; ?>
                        <span class="position-absolute top-0 end-0 m-3 badge bg-white text-dark shadow-sm rounded-pill fw-bold">
                            <?php echo htmlspecialchars($item['category'] ?? 'General'); ?>
                        </span>
                    </div>
                    
                    <div class="card-body d-flex flex-column p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title h6 fw-bold mb-0 text-truncate" title="<?php echo htmlspecialchars($item['title']); ?>">
                                <?php echo htmlspecialchars($item['title']); ?>
                            </h5>
                        </div>
                        
                        <h5 class="text-primary fw-bold mb-3">₹<?php echo number_format($item['price'], 2); ?></h5>
                        
                        <p class="card-text small text-muted mb-4 flex-grow-1 line-clamp-3">
                            <?php echo htmlspecialchars(substr($item['description'] ?? '', 0, 80)); ?>...
                        </p>
                        
                        <div class="d-flex align-items-center mb-3 pt-3 border-top border-light">
                            <div class="avatar-circle sm bg-secondary-subtle text-secondary me-2">
                                <?php echo strtoupper(substr($item['full_name'], 0, 1)); ?>
                            </div>
                            <div class="small text-truncate">
                                <div class="fw-semibold text-dark"><?php echo htmlspecialchars($item['full_name']); ?></div>
                                <div class="text-muted" style="font-size: 0.75rem;"><?php echo htmlspecialchars($item['department']); ?></div>
                            </div>
                        </div>
                        
                        <button class="btn btn-outline-primary btn-sm w-100 rounded-pill contact-seller-btn" data-item-id="<?php echo $item['id']; ?>" data-seller-name="<?php echo htmlspecialchars($item['full_name']); ?>">
                            <i class="fas fa-envelope me-1"></i> Contact Seller
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Sell Item Modal -->
<div class="modal fade" id="sellItemModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Sell Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="sellItemForm" enctype="multipart/form-data">
                <div class="modal-body pt-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Item Title *</label>
                        <input type="text" class="form-control bg-light border-0" name="title" required placeholder="What are you selling?">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Category</label>
                            <select class="form-select bg-light border-0" name="category">
                                <option value="Books">Books</option>
                                <option value="Electronics">Electronics</option>
                                <option value="Clothing">Clothing</option>
                                <option value="Furniture">Furniture</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Price (₹) *</label>
                            <input type="number" class="form-control bg-light border-0" name="price" step="0.01" min="0" required placeholder="0.00">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Description</label>
                        <textarea class="form-control bg-light border-0" name="description" rows="3" placeholder="Describe your item..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Image (Optional)</label>
                        <input type="file" class="form-control bg-light border-0" name="image" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Post Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
<script src="assets/js/marketplace.js"></script>
