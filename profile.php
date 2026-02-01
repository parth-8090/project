<?php
require_once 'config/config.php';
requireStudent();

$conn = getDBConnection();
$student_id = $_SESSION['user_id'];

// Get student details
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

// Get student's job applications
$stmt = $conn->prepare("
    SELECT a.*, j.title as job_title, j.job_type, b.business_name
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    JOIN businesses b ON j.business_id = b.id
    WHERE a.student_id = ?
    ORDER BY a.applied_at DESC
    LIMIT 10
");
$stmt->execute([$student_id]);
$applications = $stmt->fetchAll();

// Get student's reviews
$stmt = $conn->prepare("
    SELECT br.*, b.business_name, j.title as job_title
    FROM business_reviews br
    JOIN businesses b ON br.business_id = b.id
    JOIN jobs j ON br.job_id = j.id
    WHERE br.student_id = ?
    ORDER BY br.created_at DESC
    LIMIT 5
");
$stmt->execute([$student_id]);
$reviews = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row g-4">
        <!-- Profile Sidebar -->
        <div class="col-lg-4" data-aos="fade-right">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body p-0">
                    <div class="bg-primary bg-opacity-10 p-4 text-center pb-5">
                        <div class="position-relative d-inline-block mb-3">
                            <div class="rounded-circle bg-white p-1 shadow-sm">
                                <?php if (!empty($student['profile_photo']) && file_exists(UPLOAD_DIR . 'profiles/' . $student['profile_photo'])): ?>
                                    <div class="rounded-circle overflow-hidden mx-auto shadow-sm" style="width: 100px; height: 100px;">
                                        <img src="uploads/profiles/<?php echo htmlspecialchars($student['profile_photo']); ?>" alt="Profile file" class="w-100 h-100 object-fit-cover">
                                    </div>
                                <?php else: ?>
                                    <div class="profile-badge mx-auto">
                                        <i class="fas fa-user"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="position-absolute bottom-0 end-0">
                                <button class="btn btn-sm btn-light rounded-circle shadow-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal" title="Edit Profile">
                                    <i class="fas fa-camera text-primary"></i>
                                </button>
                            </div>
                        </div>
                        <h3 class="h4 fw-bold mb-1"><?php echo htmlspecialchars($student['full_name']); ?></h3>
                        <p class="text-muted mb-2"><?php echo htmlspecialchars($student['enrollment_no']); ?></p>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill shadow-sm">
                            <i class="fas fa-star me-1"></i> <?php echo $student['points']; ?> Points
                        </span>
                    </div>
                    
                    <div class="p-4 mt-n4 bg-white rounded-top-4 position-relative">
                        <div class="d-flex justify-content-between text-center mb-4 border-bottom pb-4">
                            <div>
                                <h6 class="text-muted small text-uppercase mb-1">Applied</h6>
                                <span class="fw-bold fs-5 text-primary"><?php echo count($applications); ?></span>
                            </div>
                            <div class="border-end"></div>
                            <div>
                                <h6 class="text-muted small text-uppercase mb-1">Reviews</h6>
                                <span class="fw-bold fs-5 text-primary"><?php echo count($reviews); ?></span>
                            </div>
                            <div class="border-end"></div>
                            <div>
                                <h6 class="text-muted small text-uppercase mb-1">Year</h6>
                                <span class="fw-bold fs-5 text-primary"><?php echo $student['year_of_admission']; ?></span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold text-uppercase text-muted small mb-3">Contact Information</h6>
                            
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-light rounded-circle p-2 me-3 text-primary">
                                    <i class="fas fa-envelope fa-fw"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Email Address</small>
                                    <span class="fw-medium text-break"><?php echo htmlspecialchars($student['email']); ?></span>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-light rounded-circle p-2 me-3 text-primary">
                                    <i class="fas fa-graduation-cap fa-fw"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Department</small>
                                    <span class="fw-medium"><?php echo htmlspecialchars($student['department']); ?></span>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-2 me-3 text-primary">
                                    <i class="fas fa-birthday-cake fa-fw"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Age</small>
                                    <span class="fw-medium"><?php echo $student['age']; ?> years</span>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold text-uppercase text-muted small mb-3">Social Profiles</h6>
                        <div class="d-grid gap-2">
                            <?php if ($student['linkedin_link']): ?>
                            <a href="<?php echo htmlspecialchars($student['linkedin_link']); ?>" target="_blank" class="btn btn-outline-primary">
                                <i class="fab fa-linkedin me-2"></i> LinkedIn Profile
                            </a>
                            <?php endif; ?>
                            
                            <?php if ($student['github_link']): ?>
                            <a href="<?php echo htmlspecialchars($student['github_link']); ?>" target="_blank" class="btn btn-outline-dark">
                                <i class="fab fa-github me-2"></i> GitHub Profile
                            </a>
                            <?php endif; ?>
                            
                            <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                <i class="fas fa-edit me-2"></i> Edit Profile Details
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Profile Content -->
        <div class="col-lg-8">
            <!-- Skills Section -->
            <div class="card border-0 shadow-sm mb-4 overflow-hidden" data-aos="fade-up">
                <div class="card-header bg-white border-bottom p-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3 text-primary">
                            <i class="fas fa-tools"></i>
                        </div>
                        <h5 class="fw-bold mb-0">Skills & Expertise</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <?php if ($student['skills']): ?>
                        <div class="d-flex flex-wrap gap-2">
                            <?php 
                            $skills = explode(',', $student['skills']);
                            foreach($skills as $skill): 
                                $skill = trim($skill);
                                if(empty($skill)) continue;
                            ?>
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill border border-primary border-opacity-10">
                                <?php echo htmlspecialchars($skill); ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-code fa-2x mb-2 opacity-50"></i>
                            <p>No skills added yet. Update your profile to showcase your expertise.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Interests Section -->
            <div class="card border-0 shadow-sm mb-4 overflow-hidden" data-aos="fade-up" data-aos-delay="100">
                <div class="card-header bg-white border-bottom p-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 rounded-circle p-2 me-3 text-danger">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h5 class="fw-bold mb-0">Interests</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <?php if ($student['interests']): ?>
                        <p class="text-muted mb-0 lh-lg"><?php echo nl2br(htmlspecialchars($student['interests'])); ?></p>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="far fa-heart fa-2x mb-2 opacity-50"></i>
                            <p>No interests added yet. Tell us what you're passionate about.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Applications Section -->
            <?php if (!empty($applications)): ?>
            <div class="card border-0 shadow-sm mb-4 overflow-hidden" data-aos="fade-up" data-aos-delay="200">
                <div class="card-header bg-white border-bottom p-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3 text-success">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <h5 class="fw-bold mb-0">Recent Job Applications</h5>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3">Job Title</th>
                                    <th class="py-3">Company</th>
                                    <th class="py-3">Status</th>
                                    <th class="pe-4 py-3 text-end">Applied Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td class="ps-4 fw-medium text-primary">
                                        <?php echo htmlspecialchars($app['job_title']); ?>
                                        <span class="badge bg-light text-muted ms-2 fw-normal border"><?php echo ucfirst($app['job_type']); ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                                <i class="fas fa-building text-muted small"></i>
                                            </div>
                                            <?php echo htmlspecialchars($app['business_name']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php 
                                        $statusClass = match($app['status']) {
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            'pending' => 'warning',
                                            default => 'secondary'
                                        };
                                        ?>
                                        <span class="badge bg-<?php echo $statusClass; ?>-subtle text-<?php echo $statusClass; ?> rounded-pill border border-<?php echo $statusClass; ?>-subtle px-3">
                                            <?php echo ucfirst($app['status']); ?>
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end text-muted small"><?php echo formatDate($app['applied_at']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Reviews Section -->
            <?php if (!empty($reviews)): ?>
            <div class="card border-0 shadow-sm mb-4 overflow-hidden" data-aos="fade-up" data-aos-delay="300">
                <div class="card-header bg-white border-bottom p-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3 text-warning">
                            <i class="fas fa-star"></i>
                        </div>
                        <h5 class="fw-bold mb-0">Reviews Received</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <?php foreach ($reviews as $review): ?>
                        <div class="col-12">
                            <div class="p-3 bg-light rounded-3 border h-100 transition-hover">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-white rounded-circle p-2 shadow-sm me-3">
                                            <i class="fas fa-building text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($review['business_name']); ?></h6>
                                            <small class="text-muted"><?php echo htmlspecialchars($review['job_title']); ?></small>
                                        </div>
                                    </div>
                                    <span class="text-muted small bg-white px-2 py-1 rounded border">
                                        <?php echo formatDate($review['created_at']); ?>
                                    </span>
                                </div>
                                <div class="mb-2 text-warning">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= $review['rating'] ? '' : 'text-black-50 opacity-25'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <?php if ($review['review_text']): ?>
                                <p class="mb-0 text-secondary fst-italic">"<?php echo htmlspecialchars($review['review_text']); ?>"</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editProfileForm">
                <div class="modal-body">
                    <h6 class="fw-bold text-muted text-uppercase small mb-3">Personal Details</h6>
                    <div class="mb-3 text-center">
                        <div class="position-relative d-inline-block mb-2">
                             <?php if (!empty($student['profile_photo']) && file_exists(UPLOAD_DIR . 'profiles/' . $student['profile_photo'])): ?>
                                <img src="uploads/profiles/<?php echo htmlspecialchars($student['profile_photo']); ?>" class="rounded-circle shadow-sm object-fit-cover" style="width: 80px; height: 80px;" id="photoPreview">
                            <?php else: ?>
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 80px; height: 80px; font-size: 2rem;" id="photoPreviewDiv">
                                    <i class="fas fa-user text-secondary"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="small mb-3">
                            <input type="file" class="form-control form-control-sm" name="profile_photo" accept="image/jpeg,image/png,image/gif" id="profilePhotoInput">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">Full Name</label>
                        <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($student['full_name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Email Address</label>
                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Date of Birth</label>
                        <input type="date" class="form-control" name="birthdate" value="<?php echo htmlspecialchars($student['birthdate']); ?>" required>
                    </div>
                    
                    <h6 class="fw-bold text-muted text-uppercase small mb-3 mt-4">Social & Skills</h6>
                    <div class="mb-3">
                        <label class="form-label fw-medium">LinkedIn Profile</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fab fa-linkedin text-primary"></i></span>
                            <input type="url" class="form-control border-start-0" name="linkedin_link" placeholder="https://linkedin.com/in/..." value="<?php echo htmlspecialchars($student['linkedin_link'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">GitHub Profile</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fab fa-github"></i></span>
                            <input type="url" class="form-control border-start-0" name="github_link" placeholder="https://github.com/..." value="<?php echo htmlspecialchars($student['github_link'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Skills</label>
                        <textarea class="form-control" name="skills" rows="3" placeholder="e.g. PHP, JavaScript, React, SQL"><?php echo htmlspecialchars($student['skills'] ?? ''); ?></textarea>
                        <div class="form-text">Separate skills with commas.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Interests</label>
                        <textarea class="form-control" name="interests" rows="3" placeholder="What are you interested in?"><?php echo htmlspecialchars($student['interests'] ?? ''); ?></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="assets/js/profile.js?v=<?php echo time(); ?>"></script>
<?php include 'includes/footer.php'; ?>
