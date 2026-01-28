<?php
require_once 'config/config.php';
requireStudent();

$conn = getDBConnection();
$student_id = $_SESSION['user_id'];
$department = $_SESSION['department'];

// Get all groups
$stmt = $conn->prepare("
    SELECT g.*, 
           (SELECT COUNT(*) FROM group_members WHERE group_id = g.id) as member_count,
           (SELECT COUNT(*) FROM group_members WHERE group_id = g.id AND student_id = ?) as is_member
    FROM groups g
    ORDER BY g.created_at DESC
");
$stmt->execute([$student_id]);
$groups = $stmt->fetchAll();

// Get student's groups
$stmt = $conn->prepare("
    SELECT g.*, 
           (SELECT COUNT(*) FROM group_members WHERE group_id = g.id) as member_count
    FROM groups g
    JOIN group_members gm ON g.id = gm.group_id
    WHERE gm.student_id = ?
    ORDER BY g.created_at DESC
");
$stmt->execute([$student_id]);

$my_groups = $stmt->fetchAll();

$page_title = "Groups";
require_once 'includes/header.php';
?>

<div class="container py-4">
    <div class="row mb-5 align-items-center" data-aos="fade-down">
        <div class="col-md-8">
            <h1 class="page-title h3 fw-bold mb-2">
                <span class="text-gradient">Community Groups</span>
            </h1>
            <p class="text-muted mb-0">Connect with peers, share knowledge, and grow together.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createGroupModal">
                <i class="fas fa-plus me-2"></i> Create Group
            </button>
        </div>
    </div>
    
    <!-- My Groups -->
    <?php if (!empty($my_groups)): ?>
    <div class="row mt-4 mb-3" data-aos="fade-right">
        <div class="col-12">
            <h2 class="section-title h4 fw-bold mb-4 border-start border-4 border-primary ps-3">My Groups</h2>
        </div>
    </div>
    
    <div class="row g-4 mb-5">
        <?php foreach ($my_groups as $index => $group): ?>
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
            <div class="card h-100 group-card border-0 shadow-sm">
                <div class="group-card-header"></div>
                <div class="group-card-avatar">
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-body pt-5 mt-3">
                    <h4 class="card-title h5 fw-bold mb-2 text-center"><?php echo htmlspecialchars($group['group_name']); ?></h4>
                    
                    <?php if ($group['department']): ?>
                    <p class="text-center text-primary small fw-semibold mb-3">
                        <i class="fas fa-building me-1"></i> <?php echo htmlspecialchars($group['department']); ?>
                    </p>
                    <?php endif; ?>
                    
                    <?php if ($group['description']): ?>
                    <p class="card-text text-muted small mb-4 text-center line-clamp-2">
                        <?php echo htmlspecialchars(substr($group['description'], 0, 100)); ?>...
                    </p>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top border-light">
                        <span class="small text-muted fw-medium">
                            <i class="fas fa-user-friends me-1 text-info"></i> <?php echo $group['member_count']; ?> Members
                        </span>
                        <div class="d-flex gap-2">
                            <?php if (isset($group['created_by']) && $group['created_by'] == $student_id): ?>
                            <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 edit-group-btn" 
                                data-id="<?php echo $group['id']; ?>"
                                data-name="<?php echo htmlspecialchars($group['group_name']); ?>"
                                data-dept="<?php echo htmlspecialchars($group['department']); ?>"
                                data-desc="<?php echo htmlspecialchars($group['description']); ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <?php endif; ?>
                            <a href="group_chat.php?id=<?php echo $group['id']; ?>" class="btn btn-primary btn-sm rounded-pill px-3">
                                <i class="fas fa-comments me-1"></i> Chat
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <!-- All Groups -->
    <div class="row mt-4 mb-3" data-aos="fade-right">
        <div class="col-12">
            <h2 class="section-title h4 fw-bold mb-4 border-start border-4 border-info ps-3">Explore Groups</h2>
        </div>
    </div>
    
    <div class="row g-4">
        <?php if (empty($groups)): ?>
            <div class="col-12">
                <div class="alert alert-info d-flex align-items-center p-4 rounded-3 shadow-sm border-0 bg-info-subtle text-info-emphasis" role="alert" data-aos="fade-up">
                    <i class="fas fa-info-circle fa-2x me-3"></i>
                    <div>
                        <h5 class="alert-heading fw-bold mb-1">No Groups Available</h5>
                        <p class="mb-0">There are currently no groups to join. Why not create one?</p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($groups as $index => $group): ?>
            <?php if ($group['is_member'] == 0): // Only show groups not joined in this section ?>
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                <div class="card h-100 group-card border-0 shadow-sm">
                    <div class="group-card-header" style="background: linear-gradient(135deg, #a5b4fc, #6366f1);"></div>
                    <div class="group-card-avatar">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div class="card-body pt-5 mt-3">
                        <h4 class="card-title h5 fw-bold mb-2 text-center"><?php echo htmlspecialchars($group['group_name']); ?></h4>
                        
                        <?php if ($group['department']): ?>
                        <p class="text-center text-primary small fw-semibold mb-3">
                            <i class="fas fa-building me-1"></i> <?php echo htmlspecialchars($group['department']); ?>
                        </p>
                        <?php endif; ?>
                        
                        <?php if ($group['description']): ?>
                        <p class="card-text text-muted small mb-4 text-center line-clamp-2">
                            <?php echo htmlspecialchars(substr($group['description'], 0, 100)); ?>...
                        </p>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top border-light">
                            <span class="small text-muted fw-medium">
                                <i class="fas fa-user-friends me-1 text-info"></i> <?php echo $group['member_count']; ?> Members
                            </span>
                            <button class="btn btn-outline-primary btn-sm rounded-pill px-3 join-group-btn" data-group-id="<?php echo $group['id']; ?>">
                                <i class="fas fa-plus me-1"></i> Join
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Create Group Modal (Placeholder) -->
<div class="modal fade" id="createGroupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Create New Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createGroupForm">
                    <div class="mb-3">
                        <label for="groupName" class="form-label">Group Name</label>
                        <input type="text" class="form-control" id="groupName" name="group_name" required placeholder="e.g. React Developers">
                    </div>
                    <div class="mb-3">
                        <label for="groupDepartment" class="form-label">Department (Optional)</label>
                        <input type="text" class="form-control" id="groupDepartment" name="department" placeholder="e.g. Computer Science">
                    </div>
                    <div class="mb-3">
                        <label for="groupDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="groupDescription" name="description" rows="4" required placeholder="What is this group about?"></textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary rounded-pill py-2">
                            <i class="fas fa-plus-circle me-2"></i>Create Group
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Group Modal -->
<div class="modal fade" id="editGroupModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editGroupForm">
                    <input type="hidden" name="group_id" id="edit_group_id">
                    <div class="mb-3">
                        <label class="form-label">Group Name</label>
                        <input type="text" class="form-control" name="group_name" id="edit_group_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department (Optional)</label>
                        <input type="text" class="form-control" name="department" id="edit_department" placeholder="e.g. Computer Science">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">Update Group</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
<script src="assets/js/groups.js"></script>
