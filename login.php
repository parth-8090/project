<?php
require_once 'config/config.php';
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}
$page_title = "Login";
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
            <h4 class="fw-bold">Welcome Back!</h4>
            <p class="text-muted">Please sign in to continue</p>
        </div>
        
        <form id="loginForm">
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold text-uppercase">I am a</label>
                <select class="form-select" id="userType" name="user_type" required>
                    <option value="student">Student</option>
                    <option value="business">Business</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold text-uppercase">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                    <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" placeholder="name@example.com" required>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label text-muted small fw-bold text-uppercase">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-lock text-muted"></i></span>
                    <input type="password" class="form-control border-start-0 ps-0" id="password" name="password" placeholder="Enter your password" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 py-2 mb-3 shadow-sm">
                Sign In <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </form>
        
        <div class="text-center mt-4">
            <p class="text-muted mb-0">Don't have an account? <a href="register.php" class="fw-bold">Create Account</a></p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
<script src="assets/js/auth.js"></script>
