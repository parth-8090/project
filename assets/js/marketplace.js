// Marketplace JavaScript with GSAP animations

document.addEventListener('DOMContentLoaded', function() {
    // Animate marketplace cards - GSAP removed to avoid conflict with AOS
    // if (document.querySelectorAll('.marketplace-card').length > 0) {
    //     gsap.from('.marketplace-card', {
    //         duration: 0.6,
    //         y: 50,
    //         opacity: 0,
    //         stagger: 0.05,
    //         ease: 'power3.out'
    //     });
    // }
    
    // Check for highlight param
    const urlParams = new URLSearchParams(window.location.search);
    const highlightId = urlParams.get('highlight');
    if (highlightId) {
        const itemElement = document.getElementById('item-' + highlightId);
        if (itemElement) {
            setTimeout(() => {
                itemElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                const card = itemElement.querySelector('.card');
                if (card) {
                    card.style.transition = 'all 0.5s ease';
                    const originalShadow = card.style.boxShadow;
                    card.style.boxShadow = '0 0 0 4px rgba(99, 102, 241, 0.5), 0 1rem 3rem rgba(0,0,0,.175)';
                    card.style.transform = 'translateY(-5px)';
                    
                    setTimeout(() => {
                        card.style.boxShadow = originalShadow;
                        card.style.transform = '';
                    }, 3000);
                }
            }, 800); // Wait for AOS
        }
    }

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

    // Event delegation for dynamic buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.contact-seller-btn')) {
            const btn = e.target.closest('.contact-seller-btn');
            const itemId = btn.dataset.itemId;
            const sellerName = btn.dataset.sellerName;
            contactSeller(itemId, sellerName);
        }
        
        if (e.target.closest('.mark-sold-btn')) {
            const btn = e.target.closest('.mark-sold-btn');
            const itemId = btn.dataset.itemId;
            markAsSold(itemId);
        }
    });

    // Inquiries button
    const btnInquiries = document.getElementById('btnInquiries');
    if (btnInquiries) {
        btnInquiries.addEventListener('click', function() {
            loadInquiries();
        });
    }
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
    // We need to get the seller ID somehow. Ideally passed as arg or on the button.
    // The button click handler passed itemId and sellerName.
    // Let's find the button to get the seller ID
    const btn = document.querySelector(`.contact-seller-btn[data-item-id="${itemId}"]`);
    const sellerId = btn ? btn.dataset.sellerId : null;

    if (!sellerId) {
        Swal.fire('Error', 'Could not identify seller.', 'error');
        return;
    }

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
            
            // Combine subject and content or just use content
            const fullMessage = `Subject: ${subject}\n\n${content}`;
            
            return { message: fullMessage };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('action', 'send_inquiry');
            formData.append('item_id', itemId);
            formData.append('receiver_id', sellerId);
            formData.append('message', result.value.message);

            fetch('api/marketplace.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Message Sent!',
                        text: `Your message has been sent to ${sellerName}.`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.fire('Error', 'Failed to send message.', 'error');
            });
        }
    });
}

function loadInquiries() {
    const listContainer = document.getElementById('inquiriesList');
    listContainer.innerHTML = '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></div>';

    const formData = new FormData();
    formData.append('action', 'get_inquiries');

    fetch('api/marketplace.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.inquiries.length > 0) {
            let html = '';
            data.inquiries.forEach(inquiry => {
                html += `
                    <div class="list-group-item list-group-item-action p-3 border-bottom">
                        <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                            <h6 class="mb-0 fw-bold text-primary">${inquiry.item_title || 'Item Inquiry'}</h6>
                            <small class="text-muted">${new Date(inquiry.created_at).toLocaleDateString()}</small>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                             <div class="avatar-circle sm bg-secondary-subtle text-secondary me-2" style="width: 24px; height: 24px; font-size: 10px;">
                                ${inquiry.sender_name ? inquiry.sender_name.charAt(0).toUpperCase() : '?'}
                            </div>
                            <small class="fw-semibold text-dark">${inquiry.sender_name || 'Unknown User'}</small>
                        </div>
                        <p class="mb-1 text-muted small" style="white-space: pre-wrap;">${inquiry.message}</p>
                    </div>
                `;
            });
            listContainer.innerHTML = html;
        } else {
            listContainer.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <div class="mb-3"><i class="fas fa-inbox fa-3x opacity-25"></i></div>
                    <p class="mb-0">No inquiries yet.</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error(error);
        listContainer.innerHTML = '<div class="text-center py-3 text-danger">Failed to load inquiries.</div>';
    });
}

function markAsSold(itemId) {
    Swal.fire({
        title: 'Mark as Sold?',
        text: "This item will be removed from the marketplace listing.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Mark as Sold'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('action', 'mark_as_sold');
            formData.append('item_id', itemId);

            fetch('api/marketplace.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Sold!', data.message, 'success')
                    .then(() => window.location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.fire('Error', 'An error occurred.', 'error');
            });
        }
    });
}

