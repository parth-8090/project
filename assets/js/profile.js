// Profile JavaScript with GSAP animations

document.addEventListener('DOMContentLoaded', function() {
    // Animate profile elements
    gsap.from('.profile-header', {
        duration: 0.8,
        scale: 0.9,
        opacity: 0,
        ease: 'back.out(1.7)'
    });
    
    gsap.from('.business-card', {
        duration: 0.6,
        y: 30,
        opacity: 0,
        stagger: 0.1,
        ease: 'power3.out'
    });
    
    // Edit profile form
    const editForm = document.getElementById('editProfileForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleUpdateProfile();
        });
    }
});

function handleUpdateProfile() {
    const form = document.getElementById('editProfileForm');
    const formData = new FormData(form);
    formData.append('action', 'update_profile');
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    
    fetch('api/student.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (typeof window.showNotification === 'function') {
                window.showNotification('success', data.message);
            } else {
                alert(data.message);
            }
            const modal = bootstrap.Modal.getInstance(document.getElementById('editProfileModal'));
            modal.hide();
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            if (typeof window.showNotification === 'function') {
                window.showNotification('error', data.message);
            } else {
                alert(data.message);
            }
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        if (typeof window.showNotification === 'function') {
            window.showNotification('error', 'An error occurred. Please try again.');
        } else {
            console.error(error);
            alert('An error occurred. Please try again.');
        }
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}
