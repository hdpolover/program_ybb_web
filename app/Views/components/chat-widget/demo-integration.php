<?php
/**
 * Chat Widget Demo Integration Example
 * 
 * This file shows how to integrate the chat widget into any view or layout
 * Copy the examples below to use the chat widget in your pages
 */

// Example 1: Basic Integration (Default settings)
// Just include this in any view where you want the chat widget to appear
?>

<!-- Basic Chat Widget Integration -->
<?= view('components/chat-widget/chat-widget', [
    'enabled' => true
]) ?>

<?php
// Example 2: Custom Configuration
// You can customize various aspects of the chat widget
?>

<!-- Custom Configured Chat Widget -->
<?= view('components/chat-widget/chat-widget', [
    'enabled' => true,
    'welcome_message' => 'Welcome to YBB Support! How can I help you today?',
    'api_endpoint' => base_url('/api/chat'),
    'position' => 'bottom-right', // Options: bottom-right, bottom-left, top-right, top-left
    'theme_color' => '#007bff',
    'widget_title' => 'YBB Support Chat',
    'placeholder_text' => 'Ask me anything about YBB programs...',
    'send_button_text' => 'Send Message',
    'minimize_button_text' => 'Minimize Chat',
    'offline_message' => 'We are currently offline. Please leave a message and we\'ll get back to you.',
    'typing_indicator' => true,
    'sound_enabled' => false,
    'max_messages' => 100
]) ?>

<?php
// Example 3: Conditional Integration
// Show chat widget only on specific pages or for specific users
?>

<!-- Conditional Chat Widget Integration -->
<?php if (current_url() !== base_url('contact')): // Don't show on contact page ?>
    <?= view('components/chat-widget/chat-widget', [
        'enabled' => true,
        'welcome_message' => 'Hi! Need help navigating our website?',
        'widget_title' => 'Website Assistant'
    ]) ?>
<?php endif; ?>

<?php
// Example 4: Integration in Layout Files
// Add this to your main layout file (e.g., app/Views/layouts/main.php)
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'YBB Website' ?></title>
    <!-- Your existing head content -->
</head>
<body>
    <!-- Your existing header -->
    <header>
        <!-- Navigation, etc. -->
    </header>

    <!-- Main content -->
    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Your existing footer -->
    <footer>
        <!-- Footer content -->
    </footer>

    <!-- Chat Widget Integration -->
    <?= view('components/chat-widget/chat-widget', [
        'enabled' => $enableChat ?? true,
        'welcome_message' => 'Welcome to YBB! How can I assist you today?',
        'widget_title' => 'YBB Support',
        'api_endpoint' => base_url('/api/chat')
    ]) ?>

    <!-- Your existing scripts -->
</body>
</html>

<?php
// Example 5: Dynamic Configuration Based on User or Page
// Configure the widget dynamically based on context
?>

<?php
$chatConfig = [
    'enabled' => true,
    'api_endpoint' => base_url('/api/chat')
];

// Customize based on current page
$currentController = service('router')->controllerName();
$currentMethod = service('router')->methodName();

switch ($currentController) {
    case 'Programs':
        $chatConfig['welcome_message'] = 'Interested in our programs? I\'m here to help!';
        $chatConfig['widget_title'] = 'Program Assistant';
        break;
    case 'Payments':
        $chatConfig['welcome_message'] = 'Having payment issues? I can help you resolve them.';
        $chatConfig['widget_title'] = 'Payment Support';
        break;
    case 'AbstractPaper':
        $chatConfig['welcome_message'] = 'Need help with your abstract submission?';
        $chatConfig['widget_title'] = 'Abstract Support';
        break;
    default:
        $chatConfig['welcome_message'] = 'Hi! How can I help you today?';
        $chatConfig['widget_title'] = 'YBB Support';
}

// Show different message for logged-in users
if (session()->has('participant_data')) {
    $participant = session('participant_data');
    $chatConfig['welcome_message'] = "Hello " . ($participant['first_name'] ?? 'there') . "! How can I assist you today?";
}
?>

<!-- Dynamic Chat Widget -->
<?= view('components/chat-widget/chat-widget', $chatConfig) ?>

<?php
// Example 6: Integration with Specific Styling
// If you want to customize the position or add custom CSS
?>

<style>
/* Custom positioning for specific pages */
.custom-chat-position .chat-widget {
    bottom: 100px; /* Adjust if you have a fixed footer */
}

/* Hide chat widget on mobile for specific pages */
@media (max-width: 768px) {
    .hide-chat-mobile .chat-widget {
        display: none;
    }
}
</style>

<div class="custom-chat-position">
    <?= view('components/chat-widget/chat-widget', [
        'enabled' => true,
        'position' => 'bottom-right'
    ]) ?>
</div>

<?php
// Example 7: Multiple Chat Widgets (Advanced Use Case)
// If you need different chat widgets for different sections
?>

<!-- Support Chat Widget -->
<?= view('components/chat-widget/chat-widget', [
    'enabled' => true,
    'welcome_message' => 'Technical support is here to help!',
    'widget_title' => 'Technical Support',
    'api_endpoint' => base_url('/api/chat/support'),
    'position' => 'bottom-right'
]) ?>

<!-- Sales Chat Widget (if needed) -->
<!-- Note: You'd need to modify the widget to support multiple instances -->
<!--
<?= view('components/chat-widget/chat-widget', [
    'enabled' => false, // Only one active at a time recommended
    'welcome_message' => 'Interested in our programs? Let\'s chat!',
    'widget_title' => 'Sales Assistant',
    'api_endpoint' => base_url('/api/chat/sales'),
    'position' => 'bottom-left'
]) ?>
-->

<?php
// Example 8: A/B Testing Integration
// Show different variants for testing
?>

<?php
$chatVariant = session('chat_variant', 'default');
$variantConfig = [
    'enabled' => true,
    'api_endpoint' => base_url('/api/chat')
];

switch ($chatVariant) {
    case 'friendly':
        $variantConfig['welcome_message'] = '👋 Hey there! I\'m here to make your YBB experience amazing!';
        $variantConfig['widget_title'] = '🌟 YBB Helper';
        break;
    case 'professional':
        $variantConfig['welcome_message'] = 'Good day! I\'m your dedicated YBB support representative.';
        $variantConfig['widget_title'] = 'YBB Support';
        break;
    default:
        $variantConfig['welcome_message'] = 'Hi! How can I help you today?';
        $variantConfig['widget_title'] = 'Chat Support';
}
?>

<!-- A/B Test Chat Widget -->
<?= view('components/chat-widget/chat-widget', $variantConfig) ?>

<?php
/**
 * USAGE NOTES:
 * 
 * 1. File Paths: The widget automatically loads CSS/JS from:
 *    - app/Views/components/chat-widget/chat-widget.css
 *    - app/Views/components/chat-widget/chat-widget.js
 *    - app/Views/components/chat-widget/chat-api.js
 * 
 * 2. API Endpoints: The widget communicates with these routes:
 *    - POST /api/chat (send message)
 *    - GET /api/chat/history (get chat history)
 *    - GET /api/chat/status (check service status)
 *    - POST /api/chat/typing (typing indicators)
 * 
 * 3. Local Storage: The widget stores chat history in browser localStorage
 *    - Key format: chat_history_{session_id}
 *    - Session expires after 24 hours
 * 
 * 4. Configuration Options:
 *    - enabled: bool - Show/hide the widget
 *    - welcome_message: string - First message shown
 *    - api_endpoint: string - Backend API URL
 *    - position: string - Widget position (bottom-right, bottom-left, etc.)
 *    - theme_color: string - Primary color (CSS color value)
 *    - widget_title: string - Header title
 *    - placeholder_text: string - Input placeholder
 *    - max_messages: int - Maximum messages to store locally
 * 
 * 5. JavaScript API: Access the widget programmatically
 *    - window.chatWidget.open() - Open chat
 *    - window.chatWidget.close() - Close chat
 *    - window.chatWidget.sendProgrammaticMessage(text) - Send message
 *    - window.chatWidget.clearHistory() - Clear chat history
 * 
 * 6. CSS Customization: Override CSS variables in your stylesheet
 *    --chat-primary: #your-color;
 *    --chat-border-radius: 8px;
 *    etc.
 */
?>