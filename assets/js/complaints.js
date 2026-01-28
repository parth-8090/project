// Complaints JavaScript with GSAP animations

document.addEventListener('DOMContentLoaded', function() {
    // Animate complaint cards
    gsap.from('.note-card', {
        duration: 0.6,
        x: -30,
        opacity: 0,
        stagger: 0.1,
        ease: 'power3.out'
    });
    
    // Complaint form
    const complaintForm = document.getElementById('complaintForm');
    if (complaintForm) {
        complaintForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleSubmitComplaint();
        });
    }
});

function handleSubmitComplaint() {
    const form = document.getElementById('complaintForm');
    const formData = new FormData(form);
    formData.append('action', 'submit_complaint');
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    
    fetch('api/student.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            const modal = bootstrap.Modal.getInstance(document.getElementById('complaintModal'));
            modal.hide();
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showNotification(data.message, 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        showNotification('An error occurred. Please try again.', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

function showNotification(message, type = 'success') {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
    alert.style.zIndex = '9999';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    gsap.from(alert, {
        opacity: 0,
        y: -20,
        duration: 0.3
    });
    
    setTimeout(() => {
        gsap.to(alert, {
            opacity: 0,
            y: -20,
            duration: 0.3,
            onComplete: () => alert.remove()
        });
    }, 3000);
}
