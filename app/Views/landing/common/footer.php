<!-- start footer -->
<footer class="custom-footer bg-dark py-5 position-relative">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="mb-4">
                    <a href="<?= base_url() ?>" class="d-flex align-items-center mb-3">
                        <?php if (isset($category['logo_url']) && !empty($category['logo_url'])) : ?>
                            <img src="<?= $category['logo_url'] ?>" alt="Logo" height="40" class="me-2">
                        <?php else : ?>
                            <i class="ri-graduation-cap-line text-primary fs-24 me-2"></i>
                        <?php endif; ?>
                    </a>

                    <h4 class="text-white fw-semibold mb-0"><?= $category['name'] ?? 'Program Name' ?></h3>

                    <br>

                    <p class="text-white-50 mb-4 fs-15"><?= $category['tagline'] ?? 'Empowering through education' ?></p>
                    
                    <p class="text-white mb-3">Connect with us</p>
                    <div class="d-flex gap-2">
                        <?php if (isset($category['email']) && !empty($category['email'])) : ?>
                            <a href="mailto:<?= $category['email'] ?>" class="avatar-xs d-block">
                                <span class="avatar-title rounded-circle bg-soft-light text-white fs-16">
                                    <i class="ri-mail-fill"></i>
                                </span>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (isset($category['instagram']) && !empty($category['instagram'])) : ?>
                            <a href="<?= $category['instagram'] ?>" target="_blank" class="avatar-xs d-block">
                                <span class="avatar-title rounded-circle bg-soft-light text-white fs-16">
                                    <i class="ri-instagram-fill"></i>
                                </span>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (isset($category['tiktok']) && !empty($category['tiktok'])) : ?>
                            <a href="<?= $category['tiktok'] ?>" target="_blank" class="avatar-xs d-block">
                                <span class="avatar-title rounded-circle bg-soft-light text-white fs-16">
                                    <i class="ri-tiktok-fill"></i>
                                </span>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (isset($category['youtube']) && !empty($category['youtube'])) : ?>
                            <a href="<?= $category['youtube'] ?>" target="_blank" class="avatar-xs d-block">
                                <span class="avatar-title rounded-circle bg-soft-light text-white fs-16">
                                    <i class="ri-youtube-fill"></i>
                                </span>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (isset($category['telegram']) && !empty($category['telegram'])) : ?>
                            <a href="<?= $category['telegram'] ?>" target="_blank" class="avatar-xs d-block">
                                <span class="avatar-title rounded-circle bg-soft-light text-white fs-16">
                                    <i class="ri-telegram-fill"></i>
                                </span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-6">
                <div class="mb-4">
                    <h5 class="text-white mb-3">Quick Links</h5>
                    <ul class="list-unstyled footer-list">
                        <li><a href="<?= base_url() ?>">Home</a></li>
                        <li><a href="<?= base_url('about') ?>">About</a></li>
                        <li><a href="<?= base_url('programs') ?>">Programs</a></li>
                        <li><a href="<?= base_url('insights') ?>">Insights</a></li>
                        <li><a href="<?= base_url('contact') ?>">Contact</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="mb-4">
                    <h5 class="text-white mb-3">Useful Resources</h5>
                    <ul class="list-unstyled footer-list">
                        <li>
                            <a href="<?= base_url('privacy-policy') ?>">Privacy Policy</a>
                        </li>
                        <li>
                            <a href="<?= base_url('terms-conditions') ?>">Terms & Conditions</a>
                        </li>
                        <li>
                            <a href="<?= base_url('sitemap.xml') ?>">Sitemap</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="mb-4">
                    <h5 class="text-white mb-3">Subscribe to Our Newsletter</h5>
                    <p class="text-white-50 mb-3 fs-15">Subscribe to our newsletter to receive updates and news about our programs.</p>
                    <form id="newsletterForm" action="<?= base_url('subscribe') ?>" method="post">
                        <div class="position-relative">
                            <input type="email" class="form-control" placeholder="Enter your email" required>
                            <button type="submit" class="btn btn-primary position-absolute top-0 end-0">
                                <i class="ri-send-plane-2-fill"></i>
                            </button>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="privacyCheck" required>
                            <label class="form-check-label text-white-50 fs-13" for="privacyCheck">
                                I agree to the <a href="<?= base_url('privacy-policy') ?>" class="text-white">privacy policy</a>
                            </label>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="mt-4 pt-4 border-top border-white-50">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="text-white-50">
                                <p class="mb-0">&copy; <?= date('Y') ?> <?= $category['name'] ?? 'Program Name' ?>. All rights reserved.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end text-white-50">
                                <p class="mb-0">Designed with <i class="mdi mdi-heart text-danger"></i> by <a href="#" class="text-reset text-decoration-underline">YBB Dev Team</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- end footer -->

<button class="btn btn-danger btn-icon" id="back-to-top">
    <i class="ri-arrow-up-line"></i>
</button>

<!-- Chat Widget -->
<div id="chat-widget" class="chat-widget bottom-right">
    <!-- Chat Button (Minimized State) -->
    <div id="chat-button" class="chat-button" title="YBB Support Assistant">
        <svg class="chat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
        <span class="chat-notification-badge" id="chat-notification" style="display: none;"></span>
    </div>

    <!-- Chat Window (Expanded State) -->
    <div id="chat-window" class="chat-window" style="display: none;">
        <!-- Chat Header -->
        <div class="chat-header">
            <div class="chat-header-info">
                <div class="chat-title">YBB Support Assistant</div>
                <div class="chat-status">
                    <span class="status-indicator online"></span>
                    <span class="status-text">Online</span>
                </div>
            </div>
            <div class="chat-controls">
                <button id="chat-minimize" class="chat-control-btn" title="Minimize Chat">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Chat Messages Container -->
        <div id="chat-messages" class="chat-messages">
            <!-- Welcome message will be added here by JavaScript -->
        </div>

        <!-- Typing Indicator -->
        <div id="typing-indicator" class="typing-indicator" style="display: none;">
            <div class="typing-dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <span class="typing-text">Bot is typing...</span>
        </div>

        <!-- Chat Input -->
        <div class="chat-input-container">
            <div class="chat-input-wrapper">
                <textarea 
                    id="chat-input" 
                    class="chat-input" 
                    placeholder="Type a message..."
                    rows="1"
                    maxlength="1000"
                ></textarea>
                <button id="chat-send" class="chat-send-btn" title="Send">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22,2 15,22 11,13 2,9 22,2"></polygon>
                    </svg>
                </button>
            </div>
            <div class="chat-input-footer">
                <span class="character-count">0/1000</span>
                <span class="powered-by">Powered by AI</span>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification Container -->
<div id="toast-container"></div>

<style>
/* Footer custom styling */
.custom-footer {
    background-color: #0b1729 !important;
    position: relative;
}

.custom-footer::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('/assets/images/footer-bg.png');
    background-size: cover;
    background-position: center;
    opacity: 0.05;
}

.footer-list li {
    margin-bottom: 10px;
}

.footer-list li a {
    color: rgba(255, 255, 255, 0.5);
    transition: all 0.3s ease;
    display: inline-block;
}

.footer-list li a:hover {
    color: #fff;
    transform: translateX(5px);
    text-decoration: none;
}

#back-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 99;
    display: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    justify-content: center;
    align-items: center;
    animation: bounce 2s infinite;
}

/* Chat Widget Styles */
.chat-widget {
    position: fixed;
    z-index: 1000;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.chat-widget.bottom-right {
    bottom: 90px;
    right: 30px;
}

.chat-button {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #007bff, #0056b3);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 6px 24px rgba(0, 123, 255, 0.4);
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    position: relative;
    animation: pulse 2s infinite;
}

.chat-button:hover {
    transform: scale(1.15) rotate(5deg);
    box-shadow: 0 8px 32px rgba(0, 123, 255, 0.5);
}

.chat-button:active {
    transform: scale(0.95);
}

@keyframes pulse {
    0% {
        box-shadow: 0 6px 24px rgba(0, 123, 255, 0.4);
    }
    50% {
        box-shadow: 0 6px 24px rgba(0, 123, 255, 0.6), 0 0 0 10px rgba(0, 123, 255, 0.1);
    }
    100% {
        box-shadow: 0 6px 24px rgba(0, 123, 255, 0.4);
    }
}

.chat-icon {
    width: 24px;
    height: 24px;
    color: white;
}

.chat-window {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 380px;
    height: 520px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transform: scale(0.8) translateY(20px);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    border: 1px solid rgba(0, 123, 255, 0.1);
}

.chat-window.show {
    transform: scale(1) translateY(0);
    opacity: 1;
}

.chat-header {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
}

.chat-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
}

.chat-title {
    font-weight: 600;
    font-size: 16px;
}

.chat-status {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    opacity: 0.9;
}

.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #28a745;
}

.chat-control-btn {
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    opacity: 0.8;
    transition: opacity 0.2s;
}

.chat-control-btn:hover {
    opacity: 1;
}

.chat-control-btn svg {
    width: 16px;
    height: 16px;
}

.chat-messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    background: linear-gradient(135deg, #ffffff, #f8f9fa);
    scroll-behavior: smooth;
}

.chat-messages::-webkit-scrollbar {
    width: 6px;
}

.chat-messages::-webkit-scrollbar-track {
    background: #f1f3f4;
    border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb {
    background: #c1c8cd;
    border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
    background: #a8b3ba;
}

.chat-input-container {
    border-top: 1px solid #e9ecef;
    background: white;
}

.chat-input-wrapper {
    display: flex;
    align-items: flex-end;
    padding: 16px;
    gap: 12px;
}

.chat-input {
    flex: 1;
    border: 2px solid #e9ecef;
    border-radius: 24px;
    padding: 14px 18px;
    resize: none;
    outline: none;
    font-size: 14px;
    line-height: 1.5;
    max-height: 120px;
    min-height: 48px;
    transition: all 0.3s ease;
    background: #fafbfc;
}

.chat-input:focus {
    border-color: #007bff;
    background: white;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.chat-send-btn {
    background: linear-gradient(135deg, #007bff, #0056b3);
    border: none;
    border-radius: 50%;
    width: 42px;
    height: 42px;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
}

.chat-send-btn:hover {
    background: linear-gradient(135deg, #0056b3, #004494);
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.4);
}

.chat-send-btn:active {
    transform: scale(0.95);
}

.chat-send-btn svg {
    width: 16px;
    height: 16px;
}

.chat-input-footer {
    display: flex;
    justify-content: space-between;
    padding: 8px 12px;
    font-size: 11px;
    color: #6c757d;
}

.typing-indicator {
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(135deg, #ffffff, #f8f9fa);
    border-top: 1px solid #e9ecef;
    animation: typingSlideIn 0.3s ease-out;
}

.typing-dots {
    display: flex;
    gap: 4px;
    padding: 8px 12px;
    background: #e9ecef;
    border-radius: 16px;
}

.typing-dots span {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #007bff;
    animation: typing 1.4s infinite ease-in-out;
}

.typing-dots span:nth-child(1) { animation-delay: -0.32s; }
.typing-dots span:nth-child(2) { animation-delay: -0.16s; }

.typing-text {
    font-size: 13px;
    color: #6c757d;
    font-style: italic;
}

@keyframes typing {
    0%, 80%, 100% { 
        transform: scale(0.3);
        opacity: 0.3;
    }
    40% { 
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes typingSlideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.message {
    margin-bottom: 16px;
    display: flex;
    flex-direction: column;
    animation: messageSlideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    opacity: 0;
    transform: translateY(10px);
    animation-fill-mode: forwards;
}

.message.user {
    align-items: flex-end;
}

.message-content {
    max-width: 85%;
    padding: 12px 16px;
    border-radius: 20px;
    font-size: 14px;
    line-height: 1.5;
    position: relative;
    word-wrap: break-word;
}

.message.bot .message-content {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    color: #495057;
    border: 1px solid #e9ecef;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.message.user .message-content {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
}

.message.bot .message-content::before {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 12px;
    width: 0;
    height: 0;
    border-left: 8px solid transparent;
    border-right: 8px solid transparent;
    border-top: 8px solid #f8f9fa;
}

.message.user .message-content::before {
    content: '';
    position: absolute;
    bottom: -5px;
    right: 12px;
    width: 0;
    height: 0;
    border-left: 8px solid transparent;
    border-right: 8px solid transparent;
    border-top: 8px solid #007bff;
}

@keyframes messageSlideIn {
    from {
        opacity: 0;
        transform: translateY(15px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@media (max-width: 768px) {
    .chat-window {
        width: 320px;
        height: 450px;
        bottom: 90px;
        right: 10px;
    }
    
    .chat-widget.bottom-right {
        bottom: 80px;
        right: 20px;
    }
    
    .chat-button {
        width: 56px;
        height: 56px;
    }
    
    .chat-messages {
        padding: 16px;
    }
    
    .chat-header {
        padding: 16px;
    }
    
    .chat-input-wrapper {
        padding: 12px;
    }
}

@media (max-width: 480px) {
    .chat-window {
        position: fixed;
        top: 20px;
        left: 20px;
        right: 20px;
        bottom: 20px;
        width: auto;
        height: auto;
        border-radius: 12px;
    }
    
    .chat-widget.bottom-right {
        bottom: 20px;
        right: 20px;
    }
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

/* Custom toast styling */
.toastify.notification-toast {
    background: linear-gradient(135deg, #2563eb, #3b82f6);
    border-left: 4px solid #1e40af;
    padding: 12px 20px;
    color: #fff;
    border-radius: 8px;
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.25);
    font-family: inherit;
    cursor: pointer;
    transition: all 0.3s ease;
}

.toastify.notification-toast:hover {
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.35);
    transform: translateY(-2px);
}

.toastify.notification-toast .toast-content {
    display: flex;
    align-items: center;
}

.toastify.notification-toast .toast-icon {
    margin-right: 16px;
    background-color: rgba(255, 255, 255, 0.25);
    height: 34px;
    width: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.toastify.notification-toast .toast-icon i {
    font-size: 18px;
}

.toastify.notification-toast .toast-message {
    flex-grow: 1;
    line-height: 1.4;
}

.toastify.notification-toast .notification-message {
    font-size: 0.95rem;
    font-weight: 500;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Back to top button
    var backToTopBtn = document.getElementById("back-to-top");
    
    window.addEventListener("scroll", function() {
        if (window.pageYOffset > 300) {
            backToTopBtn.style.display = "flex";
        } else {
            backToTopBtn.style.display = "none";
        }
    });
    
    backToTopBtn.addEventListener("click", function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
    
    // Chat Widget Functionality
    const chatButton = document.getElementById('chat-button');
    const chatWindow = document.getElementById('chat-window');
    const chatMinimize = document.getElementById('chat-minimize');
    const chatInput = document.getElementById('chat-input');
    const chatSend = document.getElementById('chat-send');
    const chatMessages = document.getElementById('chat-messages');
    const typingIndicator = document.getElementById('typing-indicator');
    
    let isOpen = false;
    let messages = [];
    
    // Initialize chat
    function initChat() {
        addMessage('bot', 'Welcome to <?= env('DEFAULT_SITE_NAME', 'Japan Youth Summit') ?>! How can I help you explore our programs?');
    }
    
    // Toggle chat window
    function toggleChat() {
        if (isOpen) {
            // Closing
            chatWindow.classList.remove('show');
            setTimeout(() => {
                chatWindow.style.display = 'none';
            }, 300);
        } else {
            // Opening
            chatWindow.style.display = 'flex';
            setTimeout(() => {
                chatWindow.classList.add('show');
            }, 10);
            if (messages.length === 0) {
                setTimeout(() => {
                    initChat();
                }, 400);
            }
        }
        isOpen = !isOpen;
    }
    
    // Add message to chat
    function addMessage(sender, text) {
        messages.push({sender, text, timestamp: new Date()});
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}`;
        
        const contentDiv = document.createElement('div');
        contentDiv.className = 'message-content';
        contentDiv.textContent = text;
        
        messageDiv.appendChild(contentDiv);
        chatMessages.appendChild(messageDiv);
        
        // Scroll to bottom
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Send message to API
    async function sendMessage(text) {
        addMessage('user', text);
        
        // Show typing indicator
        typingIndicator.style.display = 'flex';
        
        try {
            const response = await fetch('<?= base_url('/api/chat') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    message: text,
                    conversation_id: 'landing-' + Date.now()
                })
            });
            
            const data = await response.json();
            
            setTimeout(() => {
                typingIndicator.style.display = 'none';
                addMessage('bot', data.response || 'Sorry, I encountered an error. Please try again.');
            }, 1000);
            
        } catch (error) {
            setTimeout(() => {
                typingIndicator.style.display = 'none';
                addMessage('bot', 'Sorry, I\'m having trouble connecting. Please try again later.');
            }, 1000);
        }
    }
    
    // Event listeners
    chatButton.addEventListener('click', toggleChat);
    chatMinimize.addEventListener('click', toggleChat);
    
    chatSend.addEventListener('click', () => {
        const text = chatInput.value.trim();
        if (text) {
            sendMessage(text);
            chatInput.value = '';
            updateCharacterCount();
        }
    });
    
    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            const text = chatInput.value.trim();
            if (text) {
                sendMessage(text);
                chatInput.value = '';
                updateCharacterCount();
            }
        }
    });
    
    // Character count
    function updateCharacterCount() {
        const count = chatInput.value.length;
        document.querySelector('.character-count').textContent = `${count}/1000`;
    }
    
    chatInput.addEventListener('input', updateCharacterCount);
    
    // Newsletter form 
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Here you would normally send the form data via AJAX
            const email = this.querySelector('input[type="email"]').value;
            
            // For demo: Add success message
            const formHTML = this.innerHTML;
            this.innerHTML = '<div class="alert alert-success mb-0">Thank you for subscribing!</div>';
            
            // Reset form after 3 seconds
            setTimeout(() => {
                this.innerHTML = formHTML;
                this.querySelector('input[type="email"]').value = '';
            }, 3000);
        });
    }    // Registration Toast Notifications
    function showRegistrationToast() {
        // Fetch recent registration data
        fetch('<?= base_url('popup-notification/getRecentRegistrations') ?>')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data && data.data.notif) {
                    const notifMessage = data.data.notif;
                    
                    // Create toast content with HTML - simple message display
                    const toastContent = `
                        <div class="toast-content">
                            <div class="toast-icon">
                                <i class="ri-notification-line"></i>
                            </div>
                            <div class="toast-message">
                                <span class="notification-message">${notifMessage}</span>
                            </div>
                        </div>
                    `;
                      // Show toast notification
                    Toastify({
                        node: (() => {
                            const div = document.createElement("div");
                            div.innerHTML = toastContent;
                            return div;
                        })(),
                        className: "notification-toast",
                        gravity: "bottom",
                        position: "left",
                        duration: 5000,
                        close: false, // Remove close button
                        stopOnFocus: true,
                      
                    }).showToast();
                }
            })
            .catch(error => console.error('Error fetching notification data:', error));
        
        // Schedule next toast with random interval between 2-5 minutes (reduced frequency)
        const nextInterval = Math.floor(Math.random() * (300000 - 120000 + 1)) + 120000;
        setTimeout(showRegistrationToast, nextInterval);
    }
    
    // Start showing registration toasts after initial page load
    setTimeout(showRegistrationToast, 10000);
});
</script>