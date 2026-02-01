<?php
require_once 'config/config.php';
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}
$page_title = "Welcome";
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <h1 class="hero-title">Your Complete Campus Community Platform</h1>
                <p class="hero-subtitle">Connect, collaborate, and thrive with Agora Campus. The all-in-one solution for students to manage academic and social life.</p>
                <div class="d-flex gap-3 justify-content-center justify-content-lg-start">
                    <a href="login.php" class="btn btn-primary btn-lg px-4">
                        <i class="fas fa-sign-in-alt me-2"></i> Login
                    </a>
                    <a href="register.php" class="btn btn-outline-primary btn-lg px-4">
                        <i class="fas fa-user-plus me-2"></i> Register
                    </a>
                </div>
            </div>
            <div class="col-lg-6 mt-5 mt-lg-0 text-center" data-aos="fade-left" data-aos-delay="200">
                <!-- Using a composition of icons as a hero graphic since we don't have an image asset -->
                <div class="position-relative p-5">
                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-primary opacity-10 rounded-circle filter-blur-3xl"></div>
                    <i class="fas fa-university text-primary" style="font-size: 15rem; opacity: 0.8;"></i>
                    
                    <!-- Floating Elements -->
                    <div class="card p-3 position-absolute top-0 end-0 shadow-lg" style="width: 200px; transform: rotate(5deg);">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-success rounded-circle p-2 me-2"><i class="fas fa-check text-white"></i></div>
                            <small class="fw-bold">Assignment Done</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: 100%"></div>
                        </div>
                    </div>
                    
                    <div class="card p-3 position-absolute bottom-0 start-0 shadow-lg" style="width: 180px; transform: rotate(-5deg);">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning rounded-circle p-2 me-2"><i class="fas fa-users text-white"></i></div>
                            <div>
                                <small class="d-block text-muted">Study Group</small>
                                <span class="fw-bold">Joined!</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-light-subtle">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="fw-bold mb-3">Everything You Need</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">
                Agora Campus brings together all the tools you need to succeed in your academic journey.
            </p>
        </div>
        
        <div class="row g-4">
            <!-- Jobs -->
            <div class="col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-box h-100">
                    <div class="feature-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h4>Job Opportunities</h4>
                    <p class="text-muted">Find and apply for campus jobs, internships, and part-time roles tailored for students.</p>
                </div>
            </div>
            
            <!-- Community -->
            <div class="col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-box h-100">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4>Groups & Community</h4>
                    <p class="text-muted">Connect with peers, join study groups, and build your network within the campus.</p>
                </div>
            </div>
            
            <!-- Marketplace -->
            <div class="col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-box h-100">
                    <div class="feature-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <h4>Marketplace</h4>
                    <p class="text-muted">Buy and sell textbooks, electronics, and furniture safely within the community.</p>
                </div>
            </div>
            
            <!-- Notes -->
            <div class="col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-box h-100">
                    <div class="feature-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <h4>Study Notes</h4>
                    <p class="text-muted">Access shared study materials, lecture notes, and resources from other students.</p>
                </div>
            </div>
            
            <!-- Events -->
            <div class="col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="500">
                <div class="feature-box h-100">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h4>Events</h4>
                    <p class="text-muted">Never miss out on campus activities, workshops, and social gatherings.</p>
                </div>
            </div>
            
            <!-- Lost & Found -->
            <div class="col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="600">
                <div class="feature-box h-100">
                    <div class="feature-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h4>Lost & Found</h4>
                    <p class="text-muted">Report lost items or help return found belongings to their rightful owners.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
