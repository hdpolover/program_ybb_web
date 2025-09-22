<?php
/**
 * Chat Widget Component - HTML Structure Only
 * 
 * This component contains only the HTML structure for the chat widget.
 * CSS and JS should be included separately in the appropriate sections.
 * 
 * Usage:
 * 1. Include CSS in head: <?= $this->include('components/chat-widget/chat-widget-styles') ?>
 * 2. Include HTML before closing body: <?= $this->include('components/chat-widget/chat-widget', [...]) ?>  
 * 3. Include JS at bottom: <?= $this->include('components/chat-widget/chat-widget-scripts') ?>
 */

// Default configuration
$config = [
    'enabled' => $enabled ?? true,
    'welcome_message' => $welcome_message ?? 'Hi! How can I help you today?',
    'api_endpoint' => $api_endpoint ?? base_url('/api/chat'),
    'position' => $position ?? 'bottom-right',
    'theme_color' => $theme_color ?? '#007bff',
    'widget_title' => $widget_title ?? 'Support Chat',
    'placeholder_text' => $placeholder_text ?? 'Type your message...',
    'send_button_text' => $send_button_text ?? 'Send',
    'minimize_button_text' => $minimize_button_text ?? 'Minimize Chat',
    'offline_message' => $offline_message ?? 'We are currently offline. Please leave a message.',
    'typing_indicator' => $typing_indicator ?? true,
    'sound_enabled' => $sound_enabled ?? false,
    'max_messages' => $max_messages ?? 100
];

// Don't render if disabled
if (!$config['enabled']) {
    return;
}
?>

<!-- Chat Widget HTML Structure -->
<div id="chat-widget" class="chat-widget <?= $config['position'] ?>" data-config='<?= json_encode($config) ?>'>
    <!-- Chat Button (Minimized State) -->
    <div id="chat-button" class="chat-button" title="<?= esc($config['widget_title']) ?>">
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
                <div class="chat-title"><?= esc($config['widget_title']) ?></div>
                <div class="chat-status">
                    <span class="status-indicator online"></span>
                    <span class="status-text">Online</span>
                </div>
            </div>
            <div class="chat-controls">
                <button id="chat-minimize" class="chat-control-btn" title="<?= esc($config['minimize_button_text']) ?>">
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
                    placeholder="<?= esc($config['placeholder_text']) ?>"
                    rows="1"
                    maxlength="1000"
                ></textarea>
                <button id="chat-send" class="chat-send-btn" title="<?= esc($config['send_button_text']) ?>">
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