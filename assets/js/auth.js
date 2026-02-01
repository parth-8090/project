// Authentication JavaScript - Modern version with SweetAlert2

document.addEventListener('DOMContentLoaded', function() {
    // Ensure elements are visible
    const authCards = document.querySelectorAll('.auth-card');
    authCards.forEach(card => {
        if (card) {
            card.style.opacity = '1';
            card.style.visibility = 'visible';
            card.style.display = 'block';
        }
    });
    
    // Toggle between student and business registration
    const registerType = document.getElementById('registerType');
    const studentForm = document.getElementById('studentRegisterForm');
    const businessForm = document.getElementById('businessRegisterForm');
    
    if (registerType && studentForm && businessForm) {
        registerType.addEventListener('change', function() {
            if (this.value === 'student') {
                businessForm.style.display = 'none';
                studentForm.style.display = 'block';
                studentForm.style.opacity = '1';
            } else {
                studentForm.style.display = 'none';
                businessForm.style.display = 'block';
                businessForm.style.opacity = '1';
            }
        });
    }
    
    // Login form handler
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleLogin();
        });
    }
    
    // Student registration form handler
    const studentRegisterForm = document.getElementById('studentRegisterForm');
    if (studentRegisterForm) {
        studentRegisterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleStudentRegister();
        });
    }
    
    // Business registration form handler
    const businessRegisterForm = document.getElementById('businessRegisterForm');
    if (businessRegisterForm) {
        businessRegisterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleBusinessRegister();
        });
    }
});

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

function showAlert(message, type = 'error') {
    Toast.fire({
        icon: type,
        title: message
    });
}

function handleLogin() {
    const form = document.getElementById('loginForm');
    const formData = new FormData(form);
    formData.append('action', 'login');
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
    
    fetch('api/auth.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Server response:', text);
                throw new Error('Invalid JSON response from server');
            }
        });
    })
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            setTimeout(() => {
                window.location.href = data.redirect || 'dashboard.php';
            }, 1000);
        } else {
            showAlert(data.message, 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Login Error:', error);
        showAlert(error.message || 'An error occurred. Please try again.', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

function handleStudentRegister() {
    const form = document.getElementById('studentRegisterForm');
    const formData = new FormData(form);
    formData.append('action', 'register_student');
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registering...';
    
    fetch('api/auth.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Server response:', text);
                throw new Error('Invalid JSON response from server');
            }
        });
    })
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 2000);
        } else {
            showAlert(data.message, 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Registration Error:', error);
        showAlert(error.message || 'An error occurred. Please try again.', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

function handleBusinessRegister() {
    const form = document.getElementById('businessRegisterForm');
    const formData = new FormData(form);
    formData.append('action', 'register_business');
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registering...';
    
    fetch('api/auth.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Server response:', text);
                throw new Error('Invalid JSON response from server');
            }
        });
    })
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 2000);
        } else {
            showAlert(data.message, 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Registration Error:', error);
        showAlert(error.message || 'An error occurred. Please try again.', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

function handleLogout() {
    Swal.fire({
        title: 'Are you sure?',
        text: "You will be logged out of your session.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, logout!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('api/auth.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'action=logout',
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'login.php';
                } else {
                    console.error('Logout failed:', data.message);
                    window.location.href = 'logout.php';
                }
            })
            .catch(error => {
                console.error('Logout error:', error);
                window.location.href = 'logout.php';
            });
        }
    });
}

// Attach logout button handler using event delegation
document.addEventListener('click', function(e) {
    const logoutBtn = e.target.closest('#logoutBtn');
    if (logoutBtn) {
        e.preventDefault();
        handleLogout();
    }
});
