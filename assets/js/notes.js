// Notes JavaScript with GSAP animations

document.addEventListener('DOMContentLoaded', function() {
    // Animate note cards
    gsap.from('.note-card', {
        duration: 0.6,
        y: 30,
        opacity: 0,
        stagger: 0.1,
        ease: 'power3.out'
    });
    
    // Upload note form
    const uploadForm = document.getElementById('uploadNoteForm');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleUploadNote();
        });
    }
    
    // Request note form
    const requestForm = document.getElementById('requestNoteForm');
    if (requestForm) {
        requestForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleRequestNote();
        });
    }
    
    // File upload progress animation
    const fileInput = document.querySelector('input[name="file"]');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                const file = this.files[0];
                const sizeMB = (file.size / 1048576).toFixed(2);
                console.log(`Selected file: ${file.name} (${sizeMB} MB)`);
            }
        });
    }
});

function handleUploadNote() {
    const form = document.getElementById('uploadNoteForm');
    const formData = new FormData(form);
    formData.append('action', 'upload_note');
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
    
    // Animate progress
    const progressBar = document.createElement('div');
    progressBar.className = 'progress mt-2';
    progressBar.innerHTML = '<div class="progress-bar" role="progressbar" style="width: 0%"></div>';
    form.appendChild(progressBar);
    
    fetch('api/notes.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Animate progress to 100%
            gsap.to(progressBar.querySelector('.progress-bar'), {
                width: '100%',
                duration: 0.5,
                onComplete: () => {
                    showNotification(data.message, 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
            });
        } else {
            showNotification(data.message, 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            progressBar.remove();
        }
    })
    .catch(error => {
        showNotification('An error occurred. Please try again.', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        progressBar.remove();
    });
}

function handleRequestNote() {
    const form = document.getElementById('requestNoteForm');
    const formData = new FormData(form);
    formData.append('action', 'request_note');
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    
    fetch('api/notes.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            const modal = bootstrap.Modal.getInstance(document.getElementById('requestNoteModal'));
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
