<?php
require_once 'config/config.php';
requireStudent();

$conn = getDBConnection();
$student_id = $_SESSION['user_id'];

// Get all notes
$stmt = $conn->prepare("
    SELECT n.*, s.full_name, s.department
    FROM notes n
    JOIN students s ON n.student_id = s.id
    ORDER BY n.uploaded_at DESC
");
$stmt->execute();
$notes = $stmt->fetchAll();

// Get note requests
$stmt = $conn->prepare("
    SELECT nr.*, s.full_name, s.department
    FROM note_requests nr
    JOIN students s ON nr.student_id = s.id
    WHERE nr.status = 'open'
    ORDER BY nr.created_at DESC
");
$stmt->execute();
$requests = $stmt->fetchAll();

$page_title = "Notes & Learning";
require_once 'includes/header.php';
?>

<div class="container py-4">
    <div class="row mb-4" data-aos="fade-down">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title h3 fw-bold">Notes & Learning</h1>
                    <p class="text-muted mb-0">Share resources and help each other learn.</p>
                </div>
                <div>
                    <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#uploadNoteModal">
                        <i class="fas fa-upload me-2"></i> Upload Notes
                    </button>
                    <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#requestNoteModal">
                        <i class="fas fa-hand-paper me-2"></i> Request Notes
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Note Requests -->
    <?php if (!empty($requests)): ?>
    <div class="row mt-4" data-aos="fade-right">
        <div class="col-12">
            <h2 class="section-title h4 mb-4">Note Requests</h2>
        </div>
    </div>
    
    <div class="row">
        <?php foreach ($requests as $index => $request): ?>
        <div class="col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
            <div class="card h-100 border-start border-4 border-start-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title h6 fw-bold mb-0"><?php echo htmlspecialchars($request['subject']); ?></h5>
                        <span class="badge bg-info-subtle text-info"><?php echo ucfirst($request['request_type']); ?></span>
                    </div>
                    
                    <p class="text-muted small mb-3">
                        <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($request['full_name']); ?>
                        <span class="ms-2 border-start ps-2"><i class="fas fa-building me-1"></i> <?php echo htmlspecialchars($request['department']); ?></span>
                    </p>
                    
                    <p class="card-text small mb-3"><?php echo htmlspecialchars($request['description'] ?? 'No description'); ?></p>
                    
                    <div class="text-end">
                        <button class="btn btn-sm btn-outline-primary">Help this student</button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <!-- Available Notes -->
    <div class="row mt-5" data-aos="fade-right">
        <div class="col-12">
            <h2 class="section-title h4 mb-4">Available Notes</h2>
        </div>
    </div>
    
    <div class="row">
        <?php if (empty($notes)): ?>
            <div class="col-12">
                <div class="empty-state" data-aos="fade-up">
                    <div class="empty-state-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h4 class="fw-bold mb-2">No Notes Available</h4>
                    <p class="text-muted mb-0">Be the first to upload notes and help your peers!</p>
                    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#uploadNoteModal">
                        <i class="fas fa-upload me-2"></i> Upload Notes
                    </button>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($notes as $index => $note): ?>
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-start mb-3">
                            <div class="bg-primary-subtle text-primary rounded p-3 me-3">
                                <i class="fas fa-file-alt fa-lg"></i>
                            </div>
                            <div>
                                <h5 class="card-title h6 fw-bold mb-1"><?php echo htmlspecialchars($note['title']); ?></h5>
                                <p class="text-muted small mb-0"><i class="fas fa-book me-1"></i> <?php echo htmlspecialchars($note['subject']); ?></p>
                            </div>
                        </div>
                        
                        <?php if ($note['description']): ?>
                        <p class="card-text small text-muted mb-3 flex-grow-1"><?php echo htmlspecialchars(substr($note['description'], 0, 100)); ?>...</p>
                        <?php else: ?>
                        <p class="card-text small text-muted mb-3 flex-grow-1">No description provided.</p>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($note['full_name']); ?>
                            </small>
                            <a href="<?php echo htmlspecialchars($note['file_path']); ?>" class="btn btn-primary btn-sm" download>
                                <i class="fas fa-download me-1"></i> Download
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Upload Note Modal -->
<div class="modal fade" id="uploadNoteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Notes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="uploadNoteForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject *</label>
                        <input type="text" class="form-control" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File (PDF/DOC) *</label>
                        <input type="file" class="form-control" name="file" accept=".pdf,.doc,.docx" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Request Note Modal -->
<div class="modal fade" id="requestNoteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Request Notes or Tutoring</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="requestNoteForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Request Type *</label>
                        <select class="form-select" name="request_type" required>
                            <option value="notes">Notes</option>
                            <option value="tutoring">Tutoring Help</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject *</label>
                        <input type="text" class="form-control" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
<script src="assets/js/notes.js"></script>

