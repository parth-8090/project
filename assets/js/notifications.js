// Global Notification Utilities
window.showNotification = function(type, message, title = '') {
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 is not loaded');
        alert(message);
        return;
    }

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    Toast.fire({
        icon: type, // success, error, warning, info, question
        title: title || (type.charAt(0).toUpperCase() + type.slice(1)),
        text: message
    });
};

// Initialize any pending notifications from PHP session
document.addEventListener('DOMContentLoaded', function() {
    // Check for data-notification attribute on body (can be set by PHP)
    const notificationData = document.body.dataset.notification;
    if (notificationData) {
        try {
            const data = JSON.parse(notificationData);
            showNotification(data.type, data.message, data.title);
        } catch (e) {
            console.error('Error parsing notification data', e);
        }
    }
    
    // Animate notification cards if they exist (e.g. on a dedicated notifications page)
    const cards = document.querySelectorAll('.note-card');
    if (cards.length > 0 && window.gsap) {
        gsap.from(cards, {
            duration: 0.6,
            x: -50,
            autoAlpha: 0,
            stagger: 0.1,
            ease: 'power3.out'
        });
    }
});
