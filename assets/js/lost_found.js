// Lost & Found JavaScript with GSAP animations

document.addEventListener('DOMContentLoaded', function() {
    // Animate cards
    gsap.from('.note-card', {
        duration: 0.6,
        y: 30,
        opacity: 0,
        stagger: 0.1,
        ease: 'power3.out'
    });
    
    // Report form
    const reportForm = document.getElementById('reportItemForm');
    if (reportForm) {
        reportForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleReportItem();
        });
    }
});

function handleReportItem() {
    const form = document.getElementById('reportItemForm');
    const formData = new FormData(form);
    formData.append('action', 'report_lost_found');
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Reporting...';
    
    fetch('api/student.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            const modal = bootstrap.Modal.getInstance(document.getElementById('reportItemModal'));
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

function markResolved(itemId) {
    if (!confirm('Mark this item as resolved?')) return;
    
    const formData = new FormData();
    formData.append('action', 'mark_resolved');
    formData.append('item_id', itemId);
    
    fetch('api/student.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('An error occurred. Please try again.', 'error');
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

window.markResolved = markResolved;
