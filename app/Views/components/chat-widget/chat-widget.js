/**
 * Chat Widget Main Controller
 * 
 * Handles UI interactions, message management, and local storage
 * Provides a complete interactive chat experience
 */

class ChatWidget {
    constructor() {
        this.config = null;
        this.chatAPI = null;
        this.sessionId = null;
        this.isOpen = false;
        this.messages = [];
        this.typingTimeout = null;
        this.isTyping = false;

        // DOM elements
        this.elements = {};

        // Initialize the widget
        this.init();
    }

    /**
     * Initialize the chat widget
     */
    init() {
        try {
            this.loadConfig();
            this.initializeElements();
            this.setupEventListeners();
            this.initializeSession();
            this.loadChatHistory();
            this.addWelcomeMessage();
            this.checkServiceStatus();
            
            console.log('Chat Widget initialized successfully');
        } catch (error) {
            console.error('Failed to initialize Chat Widget:', error);
        }
    }

    /**
     * Load configuration from data attribute
     */
    loadConfig() {
        const widgetElement = document.getElementById('chat-widget');
        if (!widgetElement) {
            throw new Error('Chat widget element not found');
        }

        const configData = widgetElement.getAttribute('data-config');
        if (!configData) {
            throw new Error('Chat widget configuration not found');
        }

        this.config = JSON.parse(configData);
        
        // Initialize API service
        this.chatAPI = new ChatAPI({
            endpoint: this.config.api_endpoint,
            timeout: 15000
        });
    }

    /**
     * Initialize DOM elements
     */
    initializeElements() {
        this.elements = {
            widget: document.getElementById('chat-widget'),
            button: document.getElementById('chat-button'),
            window: document.getElementById('chat-window'),
            messages: document.getElementById('chat-messages'),
            input: document.getElementById('chat-input'),
            sendBtn: document.getElementById('chat-send'),
            minimizeBtn: document.getElementById('chat-minimize'),
            typingIndicator: document.getElementById('typing-indicator'),
            notification: document.getElementById('chat-notification'),
            characterCount: document.querySelector('.character-count'),
            statusIndicator: document.querySelector('.status-indicator'),
            statusText: document.querySelector('.status-text')
        };

        // Validate required elements
        const requiredElements = ['widget', 'button', 'window', 'messages', 'input', 'sendBtn'];
        for (const elementName of requiredElements) {
            if (!this.elements[elementName]) {
                throw new Error(`Required element not found: ${elementName}`);
            }
        }
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Chat button click
        this.elements.button.addEventListener('click', () => this.toggleChat());

        // Minimize button click
        if (this.elements.minimizeBtn) {
            this.elements.minimizeBtn.addEventListener('click', () => this.closeChat());
        }

        // Send button click
        this.elements.sendBtn.addEventListener('click', () => this.sendMessage());

        // Input events
        this.elements.input.addEventListener('keydown', (e) => this.handleInputKeydown(e));
        this.elements.input.addEventListener('input', () => this.handleInputChange());
        this.elements.input.addEventListener('focus', () => this.clearNotification());

        // Auto-resize textarea
        this.elements.input.addEventListener('input', () => this.autoResizeTextarea());

        // Click outside to close (optional)
        document.addEventListener('click', (e) => this.handleOutsideClick(e));

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => this.handleGlobalKeydown(e));
    }

    /**
     * Initialize or restore session
     */
    initializeSession() {
        const storedSessionId = localStorage.getItem('chat_session_id');
        const sessionTimestamp = localStorage.getItem('chat_session_timestamp');
        const currentTime = Date.now();
        const sessionExpiry = 24 * 60 * 60 * 1000; // 24 hours

        if (storedSessionId && sessionTimestamp && 
            (currentTime - parseInt(sessionTimestamp)) < sessionExpiry) {
            this.sessionId = storedSessionId;
        } else {
            this.sessionId = ChatAPI.generateSessionId();
            localStorage.setItem('chat_session_id', this.sessionId);
            localStorage.setItem('chat_session_timestamp', currentTime.toString());
        }
    }

    /**
     * Load chat history from local storage
     */
    loadChatHistory() {
        try {
            const storedMessages = localStorage.getItem(`chat_history_${this.sessionId}`);
            if (storedMessages) {
                this.messages = JSON.parse(storedMessages);
                this.renderMessages();
            }
        } catch (error) {
            console.warn('Failed to load chat history:', error);
            this.messages = [];
        }
    }

    /**
     * Save chat history to local storage
     */
    saveChatHistory() {
        try {
            // Keep only the last 100 messages
            const messagesToSave = this.messages.slice(-this.config.max_messages);
            localStorage.setItem(`chat_history_${this.sessionId}`, JSON.stringify(messagesToSave));
        } catch (error) {
            console.warn('Failed to save chat history:', error);
        }
    }

    /**
     * Add welcome message
     */
    addWelcomeMessage() {
        if (this.messages.length === 0 && this.config.welcome_message) {
            this.addMessage({
                text: this.config.welcome_message,
                sender: 'bot',
                timestamp: new Date().toISOString(),
                isWelcome: true
            });
        }
    }

    /**
     * Check service status
     */
    async checkServiceStatus() {
        try {
            const isOnline = await this.chatAPI.checkStatus();
            this.updateStatus(isOnline);
        } catch (error) {
            console.warn('Failed to check service status:', error);
            this.updateStatus(false);
        }
    }

    /**
     * Update service status UI
     */
    updateStatus(isOnline) {
        if (this.elements.statusIndicator && this.elements.statusText) {
            if (isOnline) {
                this.elements.statusIndicator.classList.remove('offline');
                this.elements.statusText.textContent = 'Online';
            } else {
                this.elements.statusIndicator.classList.add('offline');
                this.elements.statusText.textContent = 'Offline';
            }
        }
    }

    /**
     * Toggle chat window
     */
    toggleChat() {
        if (this.isOpen) {
            this.closeChat();
        } else {
            this.openChat();
        }
    }

    /**
     * Open chat window
     */
    openChat() {
        // Immediately hide the chat button and show the window
        this.elements.button.classList.add('hidden');
        this.elements.window.style.display = 'flex';
        // Trigger animation after display
        setTimeout(() => {
            this.elements.window.classList.add('show');
        }, 10);
        
        this.isOpen = true;
        this.elements.input.focus();
        this.clearNotification();
        this.scrollToBottom();
    }

    /**
     * Close chat window
     */
    closeChat() {
        this.elements.window.classList.remove('show');
        // Immediately show the chat button and hide the window after animation
        setTimeout(() => {
            this.elements.window.style.display = 'none';
            this.elements.button.classList.remove('hidden');
        }, 300);
        
        this.isOpen = false;
    }

    /**
     * Handle input keydown events
     */
    handleInputKeydown(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            this.sendMessage();
        }
    }

    /**
     * Handle input changes
     */
    handleInputChange() {
        const text = this.elements.input.value;
        const length = text.length;
        
        // Update character count
        if (this.elements.characterCount) {
            this.elements.characterCount.textContent = `${length}/1000`;
        }

        // Update send button state
        this.elements.sendBtn.disabled = length === 0 || length > 1000;

        // Handle typing indicator
        this.handleTypingIndicator(length > 0);
    }

    /**
     * Handle typing indicator
     */
    handleTypingIndicator(isTyping) {
        if (isTyping && !this.isTyping) {
            this.isTyping = true;
            this.chatAPI.sendTypingIndicator(this.sessionId, true);
        }

        // Clear existing timeout
        if (this.typingTimeout) {
            clearTimeout(this.typingTimeout);
        }

        // Set new timeout to stop typing
        this.typingTimeout = setTimeout(() => {
            if (this.isTyping) {
                this.isTyping = false;
                this.chatAPI.sendTypingIndicator(this.sessionId, false);
            }
        }, 1000);
    }

    /**
     * Auto-resize textarea
     */
    autoResizeTextarea() {
        const textarea = this.elements.input;
        textarea.style.height = 'auto';
        textarea.style.height = Math.min(textarea.scrollHeight, 100) + 'px';
    }

    /**
     * Send message
     */
    async sendMessage() {
        const messageText = this.elements.input.value.trim();
        
        if (!messageText) return;

        // Validate message
        const validation = ChatAPI.validateMessage(messageText);
        if (!validation.valid) {
            this.showError(validation.error);
            return;
        }

        // Add user message to UI
        this.addMessage({
            text: messageText,
            sender: 'user',
            timestamp: new Date().toISOString()
        });

        // Clear input
        this.elements.input.value = '';
        this.elements.input.style.height = 'auto';
        this.handleInputChange();

        // Show typing indicator
        this.showTypingIndicator();

        try {
            // Send to API
            const response = await this.chatAPI.sendMessage(messageText, this.sessionId);
            
            if (response.success && response.data?.message) {
                this.addMessage({
                    text: response.data.message,
                    sender: 'bot',
                    timestamp: response.timestamp || new Date().toISOString()
                });
            } else {
                throw new Error(response.error || 'Invalid response format');
            }
        } catch (error) {
            console.error('Send message error:', error);
            this.addMessage({
                text: 'Sorry, I\'m having trouble responding right now. Please try again later.',
                sender: 'bot',
                timestamp: new Date().toISOString(),
                isError: true
            });
        } finally {
            this.hideTypingIndicator();
        }
    }

    /**
     * Add message to chat
     */
    addMessage(message) {
        this.messages.push(message);
        this.renderMessage(message);
        this.saveChatHistory();
        this.scrollToBottom();

        // Show notification if chat is closed and it's a bot message
        if (!this.isOpen && message.sender === 'bot' && !message.isWelcome) {
            this.showNotification();
        }
    }

    /**
     * Render all messages
     */
    renderMessages() {
        this.elements.messages.innerHTML = '';
        this.messages.forEach(message => this.renderMessage(message));
        this.scrollToBottom();
    }

    /**
     * Render single message
     */
    renderMessage(message) {
        const messageElement = document.createElement('div');
        messageElement.className = `chat-message ${message.sender}`;
        
        const bubbleClass = message.isWelcome ? 'welcome-message' : 'message-bubble';
        const timestamp = new Date(message.timestamp).toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit'
        });

        messageElement.innerHTML = `
            <div class="${bubbleClass}">${this.escapeHtml(message.text)}</div>
            <div class="message-time">${timestamp}</div>
        `;

        this.elements.messages.appendChild(messageElement);
    }

    /**
     * Show typing indicator
     */
    showTypingIndicator() {
        if (this.elements.typingIndicator) {
            this.elements.typingIndicator.style.display = 'flex';
            this.scrollToBottom();
        }
    }

    /**
     * Hide typing indicator
     */
    hideTypingIndicator() {
        if (this.elements.typingIndicator) {
            this.elements.typingIndicator.style.display = 'none';
        }
    }

    /**
     * Show notification badge
     */
    showNotification() {
        if (this.elements.notification) {
            this.elements.notification.style.display = 'flex';
            this.elements.notification.textContent = '1';
        }
    }

    /**
     * Clear notification badge
     */
    clearNotification() {
        if (this.elements.notification) {
            this.elements.notification.style.display = 'none';
        }
    }

    /**
     * Show error message
     */
    showError(message) {
        this.addMessage({
            text: `Error: ${message}`,
            sender: 'bot',
            timestamp: new Date().toISOString(),
            isError: true
        });
    }

    /**
     * Scroll to bottom of messages
     */
    scrollToBottom() {
        setTimeout(() => {
            this.elements.messages.scrollTop = this.elements.messages.scrollHeight;
        }, 100);
    }

    /**
     * Handle clicks outside the widget
     */
    handleOutsideClick(e) {
        if (this.isOpen && !this.elements.widget.contains(e.target)) {
            // Optional: close chat when clicking outside
            // this.closeChat();
        }
    }

    /**
     * Handle global keyboard shortcuts
     */
    handleGlobalKeydown(e) {
        // Escape to close chat
        if (e.key === 'Escape' && this.isOpen) {
            this.closeChat();
        }
    }

    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Clear chat history
     */
    clearHistory() {
        this.messages = [];
        this.elements.messages.innerHTML = '';
        localStorage.removeItem(`chat_history_${this.sessionId}`);
        this.addWelcomeMessage();
    }

    /**
     * Public API methods
     */
    
    // Open chat programmatically
    open() {
        this.openChat();
    }

    // Close chat programmatically
    close() {
        this.closeChat();
    }

    // Check if chat is open
    isOpened() {
        return this.isOpen;
    }

    // Send a programmatic message
    sendProgrammaticMessage(text) {
        if (text && text.trim()) {
            this.elements.input.value = text.trim();
            this.sendMessage();
        }
    }

    // Get current session ID
    getSessionId() {
        return this.sessionId;
    }

    // Update configuration
    updateConfig(newConfig) {
        this.config = { ...this.config, ...newConfig };
        this.chatAPI = new ChatAPI({
            endpoint: this.config.api_endpoint
        });
    }
}

// Auto-initialize if not already done
if (typeof window !== 'undefined') {
    window.ChatWidget = ChatWidget;
}