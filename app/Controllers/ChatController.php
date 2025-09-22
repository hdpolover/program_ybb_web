<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Chat API Controller
 * 
 * Handles chat API requests for the chat widget
 * Provides endpoints for sending messages, getting history, and status checks
 */
class ChatController extends BaseController
{
    use ResponseTrait;

    protected $format = 'json';

    /**
     * Send a chat message and get bot response
     * 
     * @return ResponseInterface
     */
    public function sendMessage(): ResponseInterface
    {
        try {
            // Validate request method
            if ($this->request->getMethod() !== 'post') {
                return $this->fail('Invalid request method', 405);
            }

            // Get request object
            $request = service('request');
            
            $message = $request->getVar('message');
            $sessionId = $request->getVar('session_id');
            $context = $request->getVar('context') ?? [];
            
            if (empty($message)) {
                return $this->fail('Message is required', 400);
            }
            
            if (empty($sessionId)) {
                return $this->fail('Session ID is required', 400);
            }

            // Validate message content
            $message = trim($message);
            if (strlen($message) === 0) {
                return $this->fail('Message cannot be empty', 400);
            }
            if (strlen($message) > 1000) {
                return $this->fail('Message is too long (maximum 1000 characters)', 400);
            }            // Log the incoming message (optional)
            log_message('info', "Chat message received - Session: {$sessionId}, Message: " . substr($message, 0, 100));

            // Process the message and get bot response
            $botResponse = $this->processChatMessage($message, $sessionId, $context);

            // Save chat history (if you want to store in database)
            $this->saveChatMessage($sessionId, $message, 'user');
            $this->saveChatMessage($sessionId, $botResponse['message'], 'bot');

            // Return successful response
            return $this->respond([
                'success' => true,
                'data' => [
                    'message' => $botResponse['message'],
                    'session_id' => $sessionId,
                    'timestamp' => date('c'),
                    'context' => $botResponse['context'] ?? []
                ],
                'message' => 'Message processed successfully'
            ], 200);

        } catch (\Exception $e) {
            log_message('error', 'Chat API Error: ' . $e->getMessage());
            
            return $this->fail([
                'message' => 'An error occurred while processing your message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get chat history for a session
     * 
     * @return ResponseInterface
     */
    public function getChatHistory(): ResponseInterface
    {
        try {
            $request = service('request');
            $sessionId = $request->getVar('session_id');
            $limit = (int) $request->getVar('limit') ?: 50;

            if (empty($sessionId)) {
                return $this->fail('Session ID is required', 400);
            }

            // Validate limit
            $limit = max(1, min($limit, 100)); // Between 1 and 100

            // Get chat history from database or storage
            $messages = $this->getChatHistoryFromStorage($sessionId, $limit);

            return $this->respond([
                'success' => true,
                'data' => [
                    'messages' => $messages,
                    'session_id' => $sessionId,
                    'count' => count($messages)
                ],
                'message' => 'Chat history retrieved successfully'
            ], 200);

        } catch (\Exception $e) {
            log_message('error', 'Chat History Error: ' . $e->getMessage());
            
            return $this->fail([
                'message' => 'Failed to retrieve chat history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check chat service status
     * 
     * @return ResponseInterface
     */
    public function getStatus(): ResponseInterface
    {
        try {
            // Check if chat service is available
            $isOnline = $this->checkChatServiceAvailability();
            
            // Get system status
            $status = [
                'online' => $isOnline,
                'timestamp' => date('c'),
                'version' => '1.0.0',
                'uptime' => $this->getSystemUptime(),
                'active_sessions' => $this->getActiveSessionsCount()
            ];

            return $this->respond([
                'success' => true,
                'data' => $status,
                'message' => $isOnline ? 'Service is online' : 'Service is offline'
            ], 200);

        } catch (\Exception $e) {
            log_message('error', 'Chat Status Error: ' . $e->getMessage());
            
            return $this->respond([
                'success' => false,
                'data' => [
                    'online' => false,
                    'timestamp' => date('c'),
                    'error' => 'Status check failed'
                ],
                'message' => 'Unable to determine service status'
            ], 200); // Still return 200 so frontend can handle gracefully
        }
    }

    /**
     * Handle typing indicator (optional feature)
     * 
     * @return ResponseInterface
     */
    public function typing(): ResponseInterface
    {
        try {
            $request = service('request');
            $sessionId = $request->getVar('session_id');
            $isTyping = filter_var($request->getVar('is_typing'), FILTER_VALIDATE_BOOLEAN);
            
            if (empty($sessionId)) {
                return $this->fail('Message is required', 400);
            }            // Handle typing indicator logic (e.g., broadcast to other clients, store in cache)
            $this->handleTypingIndicator($sessionId, $isTyping);

            return $this->respond([
                'success' => true,
                'message' => 'Typing indicator updated'
            ], 200);

        } catch (\Exception $e) {
            // Typing indicators are not critical, so we log but don't fail
            log_message('warning', 'Typing indicator error: ' . $e->getMessage());
            
            return $this->respond([
                'success' => false,
                'message' => 'Typing indicator update failed'
            ], 200);
        }
    }

    /**
     * Get request input data
     * 
     * @return array
     */
    private function getRequestInput(): array
    {
        $request = service('request');
        
        // Try to get JSON input first
        $json = $request->getJSON(true);
        
        if (!empty($json)) {
            return $json;
        }
        
        // Fall back to POST data
        return $request->getVar() ?? [];
    }

    /**
     * Validate message input
     * 
     * @param array $input
     * @return array
     */
    private function validateMessageInput(array $input): array
    {
        $errors = [];

        // Check required fields
        if (empty($input['message'])) {
            $errors[] = 'Message is required';
        }

        if (empty($input['session_id'])) {
            $errors[] = 'Session ID is required';
        }

        // Validate message content
        if (!empty($input['message'])) {
            $message = trim($input['message']);
            
            if (strlen($message) === 0) {
                $errors[] = 'Message cannot be empty';
            } elseif (strlen($message) > 1000) {
                $errors[] = 'Message is too long (maximum 1000 characters)';
            }
        }

        // Validate session ID format
        if (!empty($input['session_id']) && !preg_match('/^chat_[a-z0-9_]+$/', $input['session_id'])) {
            $errors[] = 'Invalid session ID format';
        }

        if (!empty($errors)) {
            return [
                'valid' => false,
                'message' => implode(', ', $errors)
            ];
        }

        return [
            'valid' => true,
            'data' => [
                'message' => trim($input['message']),
                'session_id' => $input['session_id'],
                'context' => $input['context'] ?? []
            ]
        ];
    }

    /**
     * Process chat message and get bot response
     * This is where you'll integrate with your actual bot API
     * 
     * @param string $message
     * @param string $sessionId
     * @param array $context
     * @return array
     */
    private function processChatMessage(string $message, string $sessionId, array $context = []): array
    {
        // TODO: Replace this with your actual bot API integration
        
        // For now, return a placeholder response
        $responses = [
            'Hello! I\'m here to help you. How can I assist you today?',
            'That\'s an interesting question. Let me help you with that.',
            'I understand your concern. Here\'s what I can tell you about that.',
            'Thank you for your message. I\'m processing your request.',
            'I\'m here to provide support. What specific information do you need?',
            'That\'s a great question! Let me provide you with some helpful information.',
            'I appreciate you reaching out. How else can I assist you?'
        ];

        // Simple keyword-based responses (replace with your bot logic)
        $lowercaseMessage = strtolower($message);
        
        if (strpos($lowercaseMessage, 'hello') !== false || strpos($lowercaseMessage, 'hi') !== false) {
            $response = 'Hello! How can I help you today?';
        } elseif (strpos($lowercaseMessage, 'help') !== false) {
            $response = 'I\'m here to help! You can ask me about our services, support, or any questions you have.';
        } elseif (strpos($lowercaseMessage, 'thank') !== false) {
            $response = 'You\'re welcome! Is there anything else I can help you with?';
        } elseif (strpos($lowercaseMessage, 'bye') !== false) {
            $response = 'Goodbye! Feel free to come back if you have any more questions.';
        } else {
            // Random response for other messages
            $response = $responses[array_rand($responses)];
        }

        /* 
         * TODO: Replace the above with your actual bot API call
         * Example integration:
         * 
         * $botApiUrl = 'https://your-bot-api.com/chat';
         * $botRequest = [
         *     'message' => $message,
         *     'session_id' => $sessionId,
         *     'context' => $context
         * ];
         * 
         * $client = \Config\Services::curlrequest();
         * $response = $client->post($botApiUrl, [
         *     'json' => $botRequest,
         *     'timeout' => 30
         * ]);
         * 
         * $botData = json_decode($response->getBody(), true);
         * $response = $botData['message'] ?? 'Sorry, I couldn\'t process that request.';
         */

        return [
            'message' => $response,
            'context' => $context,
            'confidence' => 0.9, // If your bot provides confidence scores
            'intent' => 'general_inquiry' // If your bot provides intent detection
        ];
    }

    /**
     * Save chat message to storage
     * TODO: Implement actual database storage if needed
     * 
     * @param string $sessionId
     * @param string $message
     * @param string $sender
     * @return bool
     */
    private function saveChatMessage(string $sessionId, string $message, string $sender): bool
    {
        try {
            // TODO: Implement database storage
            // Example:
            // $this->model->insert([
            //     'session_id' => $sessionId,
            //     'message' => $message,
            //     'sender' => $sender,
            //     'timestamp' => date('Y-m-d H:i:s'),
            //     'created_at' => date('Y-m-d H:i:s')
            // ]);

            return true;
        } catch (\Exception $e) {
            log_message('error', 'Failed to save chat message: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get chat history from storage
     * TODO: Implement actual database retrieval if needed
     * 
     * @param string $sessionId
     * @param int $limit
     * @return array
     */
    private function getChatHistoryFromStorage(string $sessionId, int $limit): array
    {
        // TODO: Implement database retrieval
        // For now, return empty array since we're using local storage on frontend
        return [];
    }

    /**
     * Check if chat service is available
     * 
     * @return bool
     */
    private function checkChatServiceAvailability(): bool
    {
        // TODO: Implement actual service availability check
        // For example, ping your bot API or check database connection
        
        try {
            // Simple check: ensure we can write to logs and basic functionality works
            log_message('debug', 'Chat service availability check');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get system uptime (placeholder)
     * 
     * @return string
     */
    private function getSystemUptime(): string
    {
        // TODO: Implement actual uptime calculation
        return '99.9%';
    }

    /**
     * Get active sessions count (placeholder)
     * 
     * @return int
     */
    private function getActiveSessionsCount(): int
    {
        // TODO: Implement actual session counting
        return 0;
    }

    /**
     * Handle typing indicator
     * 
     * @param string $sessionId
     * @param bool $isTyping
     * @return void
     */
    private function handleTypingIndicator(string $sessionId, bool $isTyping): void
    {
        // TODO: Implement typing indicator logic
        // For example, store in cache or broadcast to other clients
        
        // Simple implementation: just log it
        $status = $isTyping ? 'started' : 'stopped';
        log_message('debug', "Typing indicator {$status} for session: {$sessionId}");
    }
}