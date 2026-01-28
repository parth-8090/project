// Chat JavaScript with GSAP animations and real-time updates

let lastMessageId = 0;
let pollInterval;

document.addEventListener('DOMContentLoaded', function() {
    // Animate chat container
    gsap.from('.chat-container', {
        duration: 0.8,
        y: 30,
        opacity: 0,
        ease: 'power3.out'
    });
    
    // Scroll to bottom
    scrollToBottom();
    
    // Add Member Form
    const addMemberForm = document.getElementById('addMemberForm');
    if (addMemberForm) {
        addMemberForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding...';
            
            const formData = new FormData(this);
            formData.append('action', 'add_member');
            
            fetch('api/groups.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Add Member';
                
                if (data.success) {
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addMemberModal'));
                    modal.hide();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Member added successfully',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    // Reset form
                    this.reset();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Add Member';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.'
                });
            });
        });
    }
    
    // File Input Handling
    const attachmentInput = document.getElementById('attachmentInput');
    if (attachmentInput) {
        attachmentInput.addEventListener('change', function() {
            const file = this.files[0];
            const filePreview = document.getElementById('filePreview');
            const fileName = document.getElementById('fileName');
            
            if (file) {
                fileName.textContent = file.name;
                filePreview.classList.remove('d-none');
            } else {
                clearAttachment();
            }
        });
    }
    
    // Send message form (replaced button click with form submit)
    const messageForm = document.getElementById('messageForm');
    if (messageForm) {
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            sendMessage();
        });
    }
    
    const messageInput = document.getElementById('messageInput');
    if (messageInput) {
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Prevent default form submit if handled by keypress
                sendMessage();
            }
        });
    }
    
    // Get last message ID for polling
    const messages = document.querySelectorAll('.message-item');
    if (messages.length > 0) {
        const lastMsg = messages[messages.length - 1];
        lastMessageId = parseInt(lastMsg.getAttribute('data-message-id') || 0);
    }
    
    // Start polling for new messages
    const sendBtn = document.getElementById('sendMessageBtn');
    const groupId = sendBtn ? sendBtn.getAttribute('data-group-id') : 0;
    if (groupId) {
        startPolling(groupId);
    }
});

function clearAttachment() {
    const attachmentInput = document.getElementById('attachmentInput');
    const filePreview = document.getElementById('filePreview');
    attachmentInput.value = '';
    filePreview.classList.add('d-none');
}

function sendMessage() {
    const sendBtn = document.getElementById('sendMessageBtn');
    const messageInput = document.getElementById('messageInput');
    const attachmentInput = document.getElementById('attachmentInput');
    const groupId = sendBtn.getAttribute('data-group-id');
    const message = messageInput.value.trim();
    const hasAttachment = attachmentInput.files.length > 0;
    
    if (!message && !hasAttachment) return;
    
    // Disable input while sending
    messageInput.disabled = true;
    sendBtn.disabled = true;
    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    const formData = new FormData();
    formData.append('action', 'send_message');
    formData.append('group_id', groupId);
    formData.append('message', message);
    if (hasAttachment) {
        formData.append('attachment', attachmentInput.files[0]);
    }
    
    fetch('api/groups.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Clear input and attachment
            messageInput.value = '';
            clearAttachment();
            
            messageInput.disabled = false;
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
            messageInput.focus();
            
            // Add message to chat (will be picked up by polling, but add immediately for better UX)
            addMessageToChat(data.message, true);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
            messageInput.disabled = false;
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred. Please try again.'
        });
        messageInput.disabled = false;
        sendBtn.disabled = false;
        sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
    });
}

function addMessageToChat(messageData, isSent = false) {
    const chatMessages = document.querySelector('#chatMessages .p-4');
    if (!chatMessages) return; // Guard clause
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `d-flex ${isSent ? 'justify-content-end' : 'justify-content-start'} message-item`;
    messageDiv.setAttribute('data-message-id', messageData.id);
    
    const sentAt = new Date(messageData.sent_at);
    const formattedTime = sentAt.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit'
    });
    
    const cardClass = isSent ? 'bg-primary text-white' : 'bg-white';
    const borderRadius = isSent ? 'border-bottom-right-radius: 0.25rem;' : 'border-bottom-left-radius: 0.25rem;';
    const nameColor = isSent ? 'text-white-50' : 'text-primary';
    const timeColor = isSent ? 'text-white-50' : 'text-muted';
    
    let nameHtml = '';
    if (!isSent) {
        nameHtml = `<div class="fw-bold small mb-1 ${nameColor}">${escapeHtml(messageData.full_name)}</div>`;
    }
    
    let attachmentHtml = '';
    if (messageData.attachment_url) {
        attachmentHtml = `
            <div class="mb-2">
                <img src="${messageData.attachment_url}" class="img-fluid rounded" style="max-height: 200px; object-fit: cover;" alt="Attachment">
            </div>
        `;
    }
    
    messageDiv.innerHTML = `
        <div class="card border-0 shadow-sm ${cardClass}" style="max-width: 75%; border-radius: 1rem; ${borderRadius}">
            <div class="card-body p-3">
                ${nameHtml}
                ${attachmentHtml}
                ${messageData.message ? `<p class="mb-1">${escapeHtml(messageData.message).replace(/\n/g, '<br>')}</p>` : ''}
                <div class="small ${timeColor} text-end" style="font-size: 0.7rem;">
                    ${formattedTime}
                </div>
            </div>
        </div>
    `;
    
    chatMessages.appendChild(messageDiv);
    
    // Animate new message
    gsap.from(messageDiv, {
        opacity: 0,
        y: 20,
        duration: 0.3,
        ease: 'power2.out'
    });
    
    scrollToBottom();
    lastMessageId = messageData.id;
}

function startPolling(groupId) {
    pollInterval = setInterval(() => {
        fetch(`api/groups.php?action=get_messages&group_id=${groupId}&last_message_id=${lastMessageId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.messages && data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        // Check if message already exists
                        const existing = document.querySelector(`[data-message-id="${msg.id}"]`);
                        if (!existing) {
                            // Check if the message is from me (optimization: though usually polling gets other's messages if I added mine locally)
                            // But if I sent it from another tab, I should see it.
                            // We need student_id to know if it is sent by me.
                            // Since we don't have session user_id here easily without passing it, 
                            // we can rely on a data attribute or just assume received if not added by sendMessage
                            // Better: pass current user id in a global variable or data attribute.
                            // For now, let's check a data attribute on body or assume received (styled as received).
                            // Wait, if I sent it, it should be styled as sent.
                            // I can check if msg.student_id matches current user id.
                            // I'll add current-user-id to body or chat container.
                            const currentUserId = document.body.getAttribute('data-user-id');
                            const isSent = currentUserId && msg.student_id == currentUserId;
                            addMessageToChat(msg, isSent);
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Polling error:', error);
            });
    }, 3000); // Poll every 3 seconds
}

function scrollToBottom() {
    const chatMessages = document.getElementById('chatMessages');
    if (chatMessages) {
        gsap.to(chatMessages, {
            scrollTop: chatMessages.scrollHeight,
            duration: 0.5,
            ease: 'power2.out'
        });
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Clean up polling on page unload
window.addEventListener('beforeunload', () => {
    if (pollInterval) {
        clearInterval(pollInterval);
    }
});
