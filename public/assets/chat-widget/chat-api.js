/**
 * Chat API Service
 * 
 * Handles communication with the backend chat API
 * Provides abstraction layer for easy backend integration
 */

class ChatAPI {
    constructor(config = {}) {
        this.config = {
            endpoint: config.endpoint || '/api/chat',
            timeout: config.timeout || 10000,
            retryAttempts: config.retryAttempts || 3,
            retryDelay: config.retryDelay || 1000,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...config.headers
            }
        };
    }

    /**
     * Send a message to the chat API
     * @param {string} message - The user's message
     * @param {string} sessionId - Unique session identifier
     * @param {Object} context - Additional context (optional)
     * @returns {Promise<Object>} API response
     */
    async sendMessage(message, sessionId, context = {}) {
        const payload = {
            message: message.trim(),
            session_id: sessionId,
            timestamp: new Date().toISOString(),
            context: context
        };

        try {
            const response = await this._makeRequest('POST', this.config.endpoint, payload);
            return this._handleResponse(response);
        } catch (error) {
            console.error('Chat API Error:', error);
            throw error;
        }
    }

    /**
     * Get chat history for a session
     * @param {string} sessionId - Session identifier
     * @param {number} limit - Number of messages to retrieve
     * @returns {Promise<Array>} Array of chat messages
     */
    async getChatHistory(sessionId, limit = 50) {
        const endpoint = `${this.config.endpoint}/history`;
        const params = new URLSearchParams({
            session_id: sessionId,
            limit: limit.toString()
        });

        try {
            const response = await this._makeRequest('GET', `${endpoint}?${params}`);
            return this._handleResponse(response);
        } catch (error) {
            console.error('Chat History Error:', error);
            return { success: false, messages: [] };
        }
    }

    /**
     * Check if the chat service is online
     * @returns {Promise<boolean>} Service status
     */
    async checkStatus() {
        const endpoint = `${this.config.endpoint}/status`;
        
        try {
            const response = await this._makeRequest('GET', endpoint);
            const result = this._handleResponse(response);
            return result.success && result.data?.online === true;
        } catch (error) {
            console.error('Chat Status Error:', error);
            return false;
        }
    }

    /**
     * Send typing indicator to the API (optional feature)
     * @param {string} sessionId - Session identifier
     * @param {boolean} isTyping - Whether user is typing
     */
    async sendTypingIndicator(sessionId, isTyping) {
        const endpoint = `${this.config.endpoint}/typing`;
        const payload = {
            session_id: sessionId,
            is_typing: isTyping,
            timestamp: new Date().toISOString()
        };

        try {
            await this._makeRequest('POST', endpoint, payload);
        } catch (error) {
            // Typing indicators are not critical, so we'll just log the error
            console.warn('Typing indicator error:', error);
        }
    }

    /**
     * Make HTTP request with retry logic
     * @private
     */
    async _makeRequest(method, url, data = null, attempt = 1) {
        const requestOptions = {
            method: method,
            headers: this.config.headers,
            signal: AbortSignal.timeout(this.config.timeout)
        };

        if (data && (method === 'POST' || method === 'PUT' || method === 'PATCH')) {
            requestOptions.body = JSON.stringify(data);
        }

        try {
            const response = await fetch(url, requestOptions);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            return response;
        } catch (error) {
            if (attempt < this.config.retryAttempts && !error.name === 'AbortError') {
                console.warn(`Request failed (attempt ${attempt}/${this.config.retryAttempts}):`, error.message);
                await this._delay(this.config.retryDelay * attempt);
                return this._makeRequest(method, url, data, attempt + 1);
            }
            throw error;
        }
    }

    /**
     * Handle API response and normalize the format
     * @private
     */
    async _handleResponse(response) {
        try {
            const data = await response.json();
            
            // Normalize response format
            return {
                success: data.success ?? true,
                message: data.message || '',
                data: data.data || data,
                timestamp: data.timestamp || new Date().toISOString(),
                session_id: data.session_id,
                error: data.error || null
            };
        } catch (error) {
            console.error('Response parsing error:', error);
            return {
                success: false,
                message: 'Failed to parse response',
                data: null,
                error: error.message
            };
        }
    }

    /**
     * Simple delay utility
     * @private
     */
    _delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    /**
     * Generate a unique session ID
     * @static
     */
    static generateSessionId() {
        const timestamp = Date.now().toString(36);
        const randomPart = Math.random().toString(36).substring(2, 15);
        return `chat_${timestamp}_${randomPart}`;
    }

    /**
     * Validate message before sending
     * @static
     */
    static validateMessage(message) {
        if (!message || typeof message !== 'string') {
            return { valid: false, error: 'Message must be a non-empty string' };
        }

        const trimmed = message.trim();
        if (trimmed.length === 0) {
            return { valid: false, error: 'Message cannot be empty' };
        }

        if (trimmed.length > 1000) {
            return { valid: false, error: 'Message is too long (max 1000 characters)' };
        }

        return { valid: true, message: trimmed };
    }
}

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ChatAPI;
} else if (typeof window !== 'undefined') {
    window.ChatAPI = ChatAPI;
}