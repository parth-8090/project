<?php
require_once 'config/config.php';
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}
$page_title = "Register";
$hideNavbar = true;
$body_class = 'auth-layout';
require_once 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card" data-aos="zoom-in">
        <div class="auth-logo">
            <i class="fas fa-university me-2"></i>Agora Campus
        </div>
        
        <div class="text-center mb-4">
            <h4 class="fw-bold">Create Account</h4>
            <p class="text-muted">Join our vibrant campus community</p>
        </div>
        
        <div class="mb-4">
            <label class="form-label text-muted small fw-bold text-uppercase">Register as</label>
            <select class="form-select" id="registerType" name="register_type">
                <option value="student">Student</option>
                <option value="business">Business</option>
            </select>
        </div>
        
        <!-- Student Registration Form -->
        <form id="studentRegisterForm" style="display: block;">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">Full Name</label>
                    <input type="text" class="form-control" name="full_name" placeholder="John Doe" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">Email</label>
                    <input type="email" class="form-control" name="email" placeholder="student@example.com" required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">Department</label>
                    <input type="text" class="form-control" name="department" placeholder="e.g. Computer Science" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">Enrollment Number</label>
                    <input type="text" class="form-control" name="enrollment_no" required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">Year of Admission</label>
                    <input type="number" class="form-control" name="year_of_admission" min="2000" max="2030" value="<?php echo date('Y'); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">Birthdate</label>
                    <input type="date" class="form-control" name="birthdate" required>
                </div>
                
                <div class="col-12">
                    <label class="form-label small fw-bold text-muted">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-lock text-muted"></i></span>
                        <input type="password" class="form-control border-start-0 ps-0" name="password" placeholder="Create a strong password" required>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 mt-4 py-2 shadow-sm">
                Register as Student <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </form>
        
        <!-- Business Registration Form -->
        <form id="businessRegisterForm" style="display: none;">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label small fw-bold text-muted">Business Name</label>
                    <input type="text" class="form-control" name="business_name" placeholder="e.g. Campus Cafe" required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">Email</label>
                    <input type="email" class="form-control" name="email" placeholder="business@example.com" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">Business Type</label>
                    <input type="text" class="form-control" name="business_type" placeholder="e.g. Food & Beverage" required>
                </div>
                
                <div class="col-12">
                    <label class="form-label small fw-bold text-muted">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-lock text-muted"></i></span>
                        <input type="password" class="form-control border-start-0 ps-0" name="password" placeholder="Create a strong password" required>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 mt-4 py-2 shadow-sm">
                Register as Business <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </form>
        
        <div class="text-center mt-4">
            <p class="text-muted mb-0">Already have an account? <a href="login.php" class="fw-bold">Login here</a></p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
<script src="assets/js/auth.js"></script>
