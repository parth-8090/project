// Marketplace JavaScript with GSAP animations

document.addEventListener('DOMContentLoaded', function() {
    // Animate marketplace cards
    gsap.from('.marketplace-card', {
        duration: 0.6,
        y: 50,
        opacity: 0,
        stagger: 0.05,
        ease: 'power3.out'
    });
    
    // Sell item form
    const sellForm = document.getElementById('sellItemForm');
    if (sellForm) {
        sellForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleSellItem();
        });
    }
    
    // Add hover effects
    const cards = document.querySelectorAll('.marketplace-card');
    cards.forEach(card => {
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

    // Event delegation for dynamic contact buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.contact-seller-btn')) {
            const btn = e.target.closest('.contact-seller-btn');
            const itemId = btn.dataset.itemId;
            const sellerName = btn.dataset.sellerName;
            contactSeller(itemId, sellerName);
        }
    });
});

function handleSellItem() {
    const form = document.getElementById('sellItemForm');
    const formData = new FormData(form);
    formData.append('action', 'sell_item');
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Posting...';
    
    fetch('api/marketplace.php', {
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
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('sellItemModal'));
                modal.hide();
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

function contactSeller(itemId, sellerName) {
    Swal.fire({
        title: `Contact ${sellerName}`,
        html: `
            <div class="text-start">
                <div class="mb-3">
                    <label class="form-label">Subject</label>
                    <input type="text" id="msgSubject" class="form-control" placeholder="Inquiry about item...">
                </div>
                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <textarea id="msgContent" class="form-control" rows="4" placeholder="Hi, is this item still available?"></textarea>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Send Message',
        confirmButtonColor: '#6366f1',
        cancelButtonColor: '#6b7280',
        preConfirm: () => {
            const subject = document.getElementById('msgSubject').value;
            const content = document.getElementById('msgContent').value;
            
            if (!subject || !content) {
                Swal.showValidationMessage('Please fill in all fields');
                return false;
            }
            
            return { subject, content };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Here you would typically send an API request
            // For now we'll simulate success
            Swal.fire({
                icon: 'success',
                title: 'Message Sent!',
                text: `Your message has been sent to ${sellerName}.`,
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}

// Removed legacy showNotification function in favor of SweetAlert2

