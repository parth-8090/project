// Groups JavaScript with GSAP animations

document.addEventListener('DOMContentLoaded', function() {
    // Animate group cards
    // Animate group cards if they exist
    if (document.querySelectorAll('.group-card').length > 0) {
        gsap.from('.group-card', {
            duration: 0.8,
            y: 50,
            opacity: 0,
            stagger: 0.1,
            ease: 'power3.out'
        });
    }
    
    // Join group button handlers
    const joinButtons = document.querySelectorAll('.join-group-btn');
    joinButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const groupId = this.getAttribute('data-group-id');
            joinGroup(groupId, this);
        });
    });

    // Edit group button handlers
    document.querySelectorAll('.edit-group-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const dept = this.getAttribute('data-dept');
            const desc = this.getAttribute('data-desc');
            
            document.getElementById('edit_group_id').value = id;
            document.getElementById('edit_group_name').value = name;
            document.getElementById('edit_department').value = dept;
            document.getElementById('edit_description').value = desc;
            
            const modal = new bootstrap.Modal(document.getElementById('editGroupModal'));
            modal.show();
        });
    });

    // Edit group form handler
    const editGroupForm = document.getElementById('editGroupForm');
    if (editGroupForm) {
        editGroupForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
            
            const formData = new FormData(this);
            formData.append('action', 'update_group');
            
            fetch('api/groups.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Server output:', text);
                    throw new Error('Server returned invalid JSON. Check console for details.');
                }
            }))
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Group updated successfully',
                        timer: 1500,
                        showConfirmButton: false
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
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.'
                });
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }

    // Create group form handler
    const createGroupForm = document.getElementById('createGroupForm');
    if (createGroupForm) {
        createGroupForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';
            
            const formData = new FormData(this);
            formData.append('action', 'create_group');
            
            fetch('api/groups.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Server output:', text);
                    throw new Error('Server returned invalid JSON. Check console for details.');
                }
            }))
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Group created successfully',
                        timer: 1500,
                        showConfirmButton: false
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
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.'
                });
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }
});

function joinGroup(groupId, button) {
    Swal.fire({
        title: 'Join this group?',
        text: "You will become a member of this community.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#6366f1',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, join now!'
    }).then((result) => {
        if (result.isConfirmed) {
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Joining...';
            
            const formData = new FormData();
            formData.append('action', 'join_group');
            formData.append('group_id', groupId);
            
            fetch('api/groups.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Server output:', text);
                    throw new Error('Server returned invalid JSON. Check console for details.');
                }
            }))
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Joined!',
                        text: 'You are now a member of this group.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                    
                    button.innerHTML = '<i class="fas fa-check"></i> Joined';
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
