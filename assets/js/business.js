// Business JavaScript with GSAP animations

document.addEventListener('DOMContentLoaded', function() {
    // Initialize animations
    initializeAnimations();
    
    // Initialize counters
    initializeCounters();
    
    // Post job form handler
    const postJobForm = document.getElementById('postJobForm');
    if (postJobForm) {
        postJobForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handlePostJob();
        });
    }
    
    // Review form handler
    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleSubmitReview();
        });
    }
});

function initializeAnimations() {
    // Animate page load
    gsap.from('.stat-card', {
        duration: 0.8,
        y: 30,
        opacity: 0,
        stagger: 0.1,
        ease: 'power3.out'
    });
    
    gsap.from('.card', {
        duration: 0.8,
        y: 30,
        opacity: 0,
        stagger: 0.1,
        delay: 0.2,
        ease: 'power3.out'
    });
}

function initializeCounters() {
    const counters = document.querySelectorAll('.counter');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target')) || 0;
        
        if (target > 0) {
            let obj = { val: 0 };
            gsap.to(obj, {
                val: target,
                duration: 2,
                ease: 'power2.out',
                onUpdate: function() {
                    counter.textContent = Math.floor(obj.val);
                }
            });
        } else {
            counter.textContent = target;
        }
    });
}

function handlePostJob() {
    const form = document.getElementById('postJobForm');
    const formData = new FormData(form);
    formData.append('action', 'post_job');
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Posting...';
    
    fetch('api/business.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'business_dashboard.php';
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred. Please try again.'
        });
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

function updateApplication(applicationId, status) {
    const actionText = status === 'approved' ? 'approve' : 'reject';
    const confirmColor = status === 'approved' ? '#198754' : '#dc3545';
    
    Swal.fire({
        title: 'Are you sure?',
        text: `Do you want to ${actionText} this application?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: confirmColor,
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Yes, ${actionText} it!`
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('action', 'update_application');
            formData.append('application_id', applicationId);
            formData.append('status', status);
            
            fetch('api/business.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.'
                });
            });
        }
    });
}

function handleSubmitReview() {
    const form = document.getElementById('reviewForm');
    const formData = new FormData(form);
    formData.append('action', 'submit_review');
    formData.append('student_id', form.getAttribute('data-student-id'));
    formData.append('job_id', form.getAttribute('data-job-id'));
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
    
    fetch('api/business.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred. Please try again.'
        });
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

function toggleJobStatus(jobId, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'closed' : 'active';
    const actionText = newStatus === 'active' ? 'activate' : 'close';
    const confirmColor = newStatus === 'active' ? '#198754' : '#6c757d';
    
    Swal.fire({
        title: 'Are you sure?',
        text: `Do you want to ${actionText} this job?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: confirmColor,
        cancelButtonColor: '#d33',
        confirmButtonText: `Yes, ${actionText} it!`
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('action', 'toggle_job_status');
            formData.append('job_id', jobId);
            formData.append('status', newStatus);
            
            fetch('api/business.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.'
                });
            });
        }
    });
}

// Make functions available globally
window.updateApplication = updateApplication;
window.toggleJobStatus = toggleJobStatus;
