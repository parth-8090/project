<?php
require_once 'config/config.php';
requireStudent();

$conn = getDBConnection();

// Get all events
$stmt = $conn->prepare("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC");
$stmt->execute();
$upcoming_events = $stmt->fetchAll();

// Get past events
$stmt = $conn->prepare("SELECT * FROM events WHERE event_date < CURDATE() ORDER BY event_date DESC LIMIT 10");
$stmt->execute();
$past_events = $stmt->fetchAll();

$page_title = "Events";
$page_description = "Discover upcoming college events, workshops, seminars, and social gatherings. Never miss out on campus activities.";
$page_keywords = "college events, student workshops, campus activities, seminars, social gatherings";
require_once 'includes/header.php';
?>

<div class="container py-4">
    <div class="row mb-5 align-items-center" data-aos="fade-down">
        <div class="col-md-8">
            <h1 class="page-title h3 fw-bold mb-2">
                <span class="text-gradient">College Events</span>
            </h1>
            <p class="text-muted mb-0">Stay updated with campus activities, workshops, and seminars.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white border-end-0">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input type="text" id="eventSearch" class="form-control border-start-0 ps-0" placeholder="Search events...">
            </div>
        </div>
    </div>
    
    <!-- Upcoming Events -->
    <div class="row mb-4" data-aos="fade-right">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h2 class="section-title h4 mb-0 fw-bold">Upcoming Events</h2>
            <span class="badge bg-primary-subtle text-primary rounded-pill"><?php echo count($upcoming_events); ?> Upcoming</span>
        </div>
    </div>
    
    <div class="row g-4" id="eventsContainer">
        <?php if (empty($upcoming_events)): ?>
            <div class="col-12">
                <div class="empty-state" data-aos="fade-up">
                    <div class="empty-state-icon">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <h4 class="fw-bold mb-2">No Upcoming Events</h4>
                    <p class="text-muted mb-0">There are no events scheduled at the moment. Check back later!</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($upcoming_events as $index => $event): ?>
            <div class="col-6 col-md-6 col-lg-4 event-item" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                <div class="card h-100 event-card border-0 shadow-sm hover-lift overflow-hidden">
                    <div class="card-body p-4 d-flex flex-column h-100">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-primary-subtle text-primary rounded-pill">
                                <?php echo htmlspecialchars($event['event_type'] ?? 'General'); ?>
                            </span>
                            <small class="text-muted fw-semibold">
                                <i class="far fa-clock me-1"></i>
                                <?php echo $event['event_time'] ? date('h:i A', strtotime($event['event_time'])) : 'TBA'; ?>
                            </small>
                        </div>
                        
                        <h5 class="card-title h5 fw-bold mb-3 text-truncate-2" title="<?php echo htmlspecialchars($event['title']); ?>">
                            <?php echo htmlspecialchars($event['title']); ?>
                        </h5>
                        
                        <div class="d-flex align-items-center mb-3 text-muted small">
                            <div class="me-3">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>
                                <span class="fw-medium text-dark"><?php echo formatDate($event['event_date']); ?></span>
                            </div>
                            <?php if ($event['venue']): ?>
                            <div class="text-truncate">
                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                <span><?php echo htmlspecialchars($event['venue']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <p class="card-text text-muted small mb-4 flex-grow-1 line-clamp-3">
                            <?php echo htmlspecialchars(substr($event['description'] ?? '', 0, 120)); ?>...
                        </p>
                        
                        <button class="btn btn-outline-primary w-100 rounded-pill mt-auto register-btn" data-event-id="<?php echo $event['id']; ?>" data-event-title="<?php echo htmlspecialchars($event['title']); ?>">
                            <i class="fas fa-ticket-alt me-2"></i> Register Now
                        </button>
                    </div>
                    <div class="card-footer bg-light border-top-0 py-2 px-4">
                        <small class="text-muted">
                            <i class="fas fa-users me-1"></i> 12 interested
                        </small>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- Past Events -->
    <?php if (!empty($past_events)): ?>
    <div class="row mt-5 mb-4" data-aos="fade-right">
        <div class="col-12">
            <h2 class="section-title h4 mb-0 fw-bold text-muted">Past Events</h2>
        </div>
    </div>
    
    <div class="row g-4 opacity-75">
        <?php foreach ($past_events as $index => $event): ?>
        <div class="col-6 col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
            <div class="card h-100 border-0 shadow-sm bg-light">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title h6 fw-bold mb-0 text-muted"><?php echo htmlspecialchars($event['title']); ?></h5>
                        <small class="text-muted"><?php echo formatDate($event['event_date']); ?></small>
                    </div>
                    <p class="card-text small text-muted mb-0"><?php echo htmlspecialchars(substr($event['description'] ?? '', 0, 80)); ?>...</p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
<script src="assets/js/events.js"></script>

