// Events JavaScript with GSAP animations

document.addEventListener('DOMContentLoaded', function() {
    // Animate event cards
    gsap.from('.event-card', {
        duration: 0.6,
        y: 50,
        opacity: 0,
        stagger: 0.1,
        ease: 'power3.out'
    });
    
    // Add hover effects
    const eventCards = document.querySelectorAll('.event-card');
    eventCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            gsap.to(this, {
                y: -10,
                duration: 0.3,
                ease: 'power2.out'
            });
        });
        
        card.addEventListener('mouseleave', function() {
            gsap.to(this, {
                y: 0,
                duration: 0.3,
                ease: 'power2.out'
            });
        });
    });

    // Search Functionality
    const searchInput = document.getElementById('eventSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const eventItems = document.querySelectorAll('.event-item');
            
            eventItems.forEach(item => {
                const title = item.querySelector('.card-title').textContent.toLowerCase();
                const description = item.querySelector('.card-text').textContent.toLowerCase();
                const type = item.querySelector('.badge').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || description.includes(searchTerm) || type.includes(searchTerm)) {
                    item.style.display = '';
                    gsap.to(item, { opacity: 1, scale: 1, duration: 0.3 });
                } else {
                    gsap.to(item, { 
                        opacity: 0, 
                        scale: 0.8, 
                        duration: 0.3, 
                        onComplete: () => { item.style.display = 'none'; } 
                    });
                }
            });
            
            // Show "No results" message if needed
            // (Implementation optional for now)
        });
    }

    // Event Delegation for Registration
    document.addEventListener('click', function(e) {
        if (e.target.closest('.register-btn')) {
            const btn = e.target.closest('.register-btn');
            const eventId = btn.dataset.eventId;
            const eventTitle = btn.dataset.eventTitle;
            handleEventRegistration(eventId, eventTitle, btn);
        }
    });
});

function handleEventRegistration(eventId, eventTitle, btn) {
    Swal.fire({
        title: 'Register for Event?',
        text: `Do you want to register for "${eventTitle}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#6366f1',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, register me!',
        cancelButtonText: 'Maybe later'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;

            // Simulate API call
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Registered!',
                    text: `You have successfully registered for ${eventTitle}. Check your email for details.`,
                    timer: 3000,
                    showConfirmButton: false
                });

                // Update button state
                btn.innerHTML = '<i class="fas fa-check me-2"></i> Registered';
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-success');
                // btn.disabled = true; // Optional: keep it disabled or allow un-registering
            }, 1000);
        }
    });
}
