// Jobs JavaScript with GSAP animations

document.addEventListener('DOMContentLoaded', function() {
    // Animate job cards on load
    gsap.from('.job-card', {
        duration: 0.6,
        y: 30,
        opacity: 0,
        stagger: 0.1,
        ease: 'power3.out'
    });
    
    // Search functionality
    const searchInput = document.getElementById('jobSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const jobItems = document.querySelectorAll('.job-item');
            let hasResults = false;
            
            jobItems.forEach(item => {
                const title = item.querySelector('.card-title').textContent.toLowerCase();
                const business = item.querySelector('.text-muted.small').textContent.toLowerCase();
                const description = item.querySelector('.card-text').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || business.includes(searchTerm) || description.includes(searchTerm)) {
                    item.style.display = 'block';
                    hasResults = true;
                    // Re-animate appearance
                    gsap.to(item, { opacity: 1, scale: 1, duration: 0.3 });
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Handle no results
            const noResultsMsg = document.getElementById('no-results-msg');
            if (!hasResults && searchTerm !== '') {
                if (!noResultsMsg) {
                    const msg = document.createElement('div');
                    msg.id = 'no-results-msg';
                    msg.className = 'col-12 text-center py-5';
                    msg.innerHTML = `
                        <div class="text-muted">
                            <i class="fas fa-search fa-3x mb-3 opacity-50"></i>
                            <h5>No jobs found matching "${searchTerm}"</h5>
                            <p>Try adjusting your search terms</p>
                        </div>
                    `;
                    document.getElementById('jobsContainer').appendChild(msg);
                } else {
                    // Update search term in message if needed, or just show it
                    msg.querySelector('h5').innerText = `No jobs found matching "${searchTerm}"`;
                }
            } else if (noResultsMsg) {
                noResultsMsg.remove();
            }
        });
    }
    
    // Apply job button handlers
    const applyButtons = document.querySelectorAll('.apply-job-btn, #applyJobBtn');
    applyButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const jobId = this.getAttribute('data-job-id');
            applyForJob(jobId, this);
        });
    });
});

function applyForJob(jobId, button) {
    Swal.fire({
        title: 'Apply for this job?',
        text: "Are you sure you want to submit your application?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#6366f1',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, apply now!'
    }).then((result) => {
        if (result.isConfirmed) {
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Applying...';
            
            const formData = new FormData();
            formData.append('action', 'apply');
            formData.append('job_id', jobId);
            
            fetch('api/jobs.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Applied!',
                        text: 'Your application has been submitted successfully.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                    
                    button.innerHTML = '<i class="fas fa-check"></i> Applied';
                } else {
                    Swal.fire(
                        'Error!',
                        data.message,
                        'error'
                    );
                    button.disabled = false;
                    button.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire(
                    'Error!',
                    'An error occurred. Please try again.',
                    'error'
                );
                button.disabled = false;
                button.innerHTML = originalText;
            });
        }
    });
}
