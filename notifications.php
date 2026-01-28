<?php
require_once 'config/config.php';
requireStudent();

$conn = getDBConnection();
$student_id = $_SESSION['user_id'];

// Get all notifications
$stmt = $conn->prepare("
    SELECT * FROM notifications
    WHERE student_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$student_id]);
$notifications = $stmt->fetchAll();

// Mark all as read
$stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE student_id = ? AND is_read = 0");
$stmt->execute([$student_id]);

include 'includes/header.php';
?>

<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12" data-aos="fade-down">
            <h1 class="h3 fw-bold mb-1">Notifications</h1>
            <p class="text-muted mb-0">Stay updated with the latest activities</p>
        </div>
    </div>
    
    <div class="row">
        <?php if (empty($notifications)): ?>
            <div class="col-12" data-aos="fade-up">
                <div class="card border-0 shadow-sm text-center py-5">
                    <div class="card-body">
                        <div class="mb-3 text-muted" style="font-size: 3rem;">
                            <i class="far fa-bell-slash"></i>
                        </div>
                        <h5 class="fw-bold">No Notifications</h5>
                        <p class="text-muted">You're all caught up! No new notifications.</p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="col-12">
                <div class="list-group list-group-flush shadow-sm rounded-3 overflow-hidden border-0">
                <?php foreach ($notifications as $index => $notification): ?>
                    <div class="list-group-item p-4 border-start border-4 <?php echo $notification['is_read'] ? 'border-start-transparent bg-white' : 'border-start-primary bg-light'; ?>" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-1">
                                    <h6 class="fw-bold mb-0 me-2"><?php echo htmlspecialchars($notification['title']); ?></h6>
                                    <?php if (!$notification['is_read']): ?>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill small" style="font-size: 0.6rem;">NEW</span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-secondary mb-2 small"><?php echo htmlspecialchars($notification['message']); ?></p>
                                <small class="text-muted">
                                    <i class="far fa-clock me-1"></i> <?php echo formatDateTime($notification['created_at']); ?>
                                </small>
                            </div>
                            <?php if ($notification['link']): ?>
                            <div class="ms-3">
                                <a href="<?php echo htmlspecialchars($notification['link']); ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                                    View <i class="fas fa-chevron-right ms-1" style="font-size: 0.7rem;"></i>
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="assets/js/notifications.js"></script>
<?php include 'includes/footer.php'; ?>
