<?php
require_once 'config/config.php';
requireStudent();

$group_id = $_GET['id'] ?? 0;
$conn = getDBConnection();
$student_id = $_SESSION['user_id'];

// Verify student is member of group
$stmt = $conn->prepare("SELECT id FROM group_members WHERE group_id = ? AND student_id = ?");
$stmt->execute([$group_id, $student_id]);
if (!$stmt->fetch()) {
    header('Location: groups.php');
    exit;
}

// Get group details
$stmt = $conn->prepare("SELECT * FROM groups WHERE id = ?");
$stmt->execute([$group_id]);
$group = $stmt->fetch();

if (!$group) {
    header('Location: groups.php');
    exit;
}

// Get messages (last 50)
$stmt = $conn->prepare("
    SELECT m.*, s.full_name, s.enrollment_no
    FROM messages m
    JOIN students s ON m.student_id = s.id
    WHERE m.group_id = ?
    ORDER BY m.sent_at ASC
    LIMIT 50
");
$stmt->execute([$group_id]);
$messages = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container py-4 h-100">
    <div class="row h-100">
        <div class="col-12 h-100 d-flex flex-column">
            <div class="card border-0 shadow-sm flex-grow-1 d-flex flex-column overflow-hidden chat-container" style="height: calc(100vh - 140px);">
                <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                            <i class="fas fa-users fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($group['group_name']); ?></h5>
                            <p class="text-muted small mb-0"><?php echo htmlspecialchars($group['description'] ?? ''); ?></p>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                            <i class="fas fa-user-plus me-1"></i> Add Member
                        </button>
                        <a href="groups.php" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                
                <div class="card-body p-0 flex-grow-1 overflow-auto bg-light" id="chatMessages">
                    <div class="p-4 d-flex flex-column gap-3">
                        <?php foreach ($messages as $msg): 
                            $isMe = $msg['student_id'] == $student_id;
                        ?>
                            <div class="d-flex <?php echo $isMe ? 'justify-content-end' : 'justify-content-start'; ?> message-item" data-message-id="<?php echo $msg['id']; ?>">
                                <div class="card border-0 shadow-sm <?php echo $isMe ? 'bg-primary text-white' : 'bg-white'; ?>" style="max-width: 75%; border-radius: 1rem; <?php echo $isMe ? 'border-bottom-right-radius: 0.25rem;' : 'border-bottom-left-radius: 0.25rem;'; ?>">
                                    <div class="card-body p-3">
                                        <?php if (!$isMe): ?>
                                            <div class="fw-bold small mb-1 <?php echo $isMe ? 'text-white-50' : 'text-primary'; ?>">
                                                <?php echo htmlspecialchars($msg['full_name']); ?>
                                            </div>
                                        <?php endif; ?>
                                        <p class="mb-1"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                                        <div class="small <?php echo $isMe ? 'text-white-50' : 'text-muted'; ?> text-end" style="font-size: 0.7rem;">
                                            <?php echo formatDateTime($msg['sent_at']); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="card-footer bg-white p-3 border-top">
                    <form id="messageForm" enctype="multipart/form-data">
                        <div class="input-group">
                            <button type="button" class="btn btn-light border text-muted rounded-start-pill px-3" id="attachBtn" onclick="document.getElementById('attachmentInput').click()">
                                <i class="fas fa-paperclip"></i>
                            </button>
                            <input type="file" id="attachmentInput" name="attachment" accept="image/*" class="d-none">
                            <input type="text" class="form-control border-0 bg-light ps-3" id="messageInput" placeholder="Type your message..." autocomplete="off">
                            <button type="submit" class="btn btn-primary rounded-end-pill px-4" id="sendMessageBtn" data-group-id="<?php echo $group_id; ?>">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                        <div id="filePreview" class="small text-primary mt-2 d-none">
                            <i class="fas fa-image me-1"></i> <span id="fileName"></span> 
                            <button type="button" class="btn-close btn-close-sm ms-2" style="font-size: 0.5rem;" onclick="clearAttachment()"></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addMemberForm">
                    <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
                    <div class="mb-3">
                        <label class="form-label">Enrollment Number</label>
                        <input type="text" class="form-control" name="enrollment_no" placeholder="Enter student's enrollment no" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">Add Member</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/chat.js"></script>
<?php include 'includes/footer.php'; ?>
