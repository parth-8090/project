<?php
require_once 'config/config.php';
requireBusiness();

$page_title = 'Post New Job';
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-5" data-aos="fade-down">
                <a href="business_dashboard.php" class="btn btn-outline-secondary me-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="page-title h3 fw-bold mb-1">
                        <span class="text-gradient">Post a New Job</span>
                    </h1>
                    <p class="text-muted mb-0">Create a new opportunity for students</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm overflow-hidden rounded-4 bg-white" data-aos="fade-up">
                <div class="card-body p-4 p-md-5">
                    
                    <form id="postJobForm">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Job Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg bg-light" name="title" placeholder="e.g. Junior Web Developer" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Job Type <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-briefcase"></i></span>
                                <select class="form-select form-select-lg border-start-0 ps-0 bg-light" name="job_type" required>
                                    <option value="">Select Job Type</option>
                                    <option value="Full-time">Full-time</option>
                                    <option value="Part-time">Part-time</option>
                                    <option value="Internship">Internship</option>
                                    <option value="Contract">Contract</option>
                                    <option value="Freelance">Freelance</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Job Description <span class="text-danger">*</span></label>
                            <textarea class="form-control bg-light" name="description" rows="6" placeholder="Describe the role, responsibilities, and what you're looking for..." required></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Required Skills <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-tools"></i></span>
                                <input type="text" class="form-control form-control-lg border-start-0 ps-0 bg-light" name="required_skills" placeholder="e.g., PHP, MySQL, JavaScript" required>
                            </div>
                            <div class="form-text">Separate multiple skills with commas</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Duration <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-clock"></i></span>
                                    <select class="form-select border-start-0 ps-0 bg-light" name="period" required>
                                        <option value="">Select Duration</option>
                                        <option value="1 month">1 month</option>
                                        <option value="2 months">2 months</option>
                                        <option value="3 months">3 months</option>
                                        <option value="6 months">6 months</option>
                                        <option value="1 year">1 year</option>
                                        <option value="Ongoing">Ongoing</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Time Commitment</label>
                                <input type="text" class="form-control bg-light" name="time_required" placeholder="e.g., 20 hours/week">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Openings</label>
                                <input type="number" class="form-control bg-light" name="number_of_employees" min="1" placeholder="Number of positions">
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Application Deadline</label>
                                <input type="date" class="form-control bg-light" name="end_date">
                                <div class="form-text">Leave empty if ongoing recruitment</div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-end gap-3">
                            <a href="business_dashboard.php" class="btn btn-light px-4 rounded-pill">Cancel</a>
                            <button type="submit" class="btn btn-primary px-5 shadow-sm rounded-pill">
                                <i class="fas fa-paper-plane me-2"></i> Post Job
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/business.js?v=<?php echo time(); ?>"></script>
<?php include 'includes/footer.php'; ?>
