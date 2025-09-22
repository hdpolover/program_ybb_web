<?php
/**
 * Simple test file to verify chat API endpoints
 * Access via: http://localhost:8080/test-chat-api.php
 */

$baseUrl = 'http://localhost:8080/api/chat';

echo '<h1>Chat API Test</h1>';
echo '<div id="results"></div>';
echo '<script>
async function testChatAPI() {
    const results = document.getElementById("results");
    
    // Test 1: Check status endpoint
    console.log("Testing status endpoint...");
    try {
        const statusResponse = await fetch("' . $baseUrl . '/status");
        const statusData = await statusResponse.json();
        results.innerHTML += "<h3>Status Test:</h3><pre>" + JSON.stringify(statusData, null, 2) + "</pre>";
        console.log("Status test passed:", statusData);
    } catch (error) {
        results.innerHTML += "<h3>Status Test (FAILED):</h3><p>Error: " + error.message + "</p>";
        console.error("Status test failed:", error);
    }
    
    // Test 2: Send a message
    console.log("Testing send message endpoint...");
    try {
        const messageResponse = await fetch("' . $baseUrl . '/send", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                message: "Hello, this is a test message",
                session_id: "chat_test_session_123"
            })
        });
        const messageData = await messageResponse.json();
        results.innerHTML += "<h3>Send Message Test:</h3><pre>" + JSON.stringify(messageData, null, 2) + "</pre>";
        console.log("Send message test passed:", messageData);
    } catch (error) {
        results.innerHTML += "<h3>Send Message Test (FAILED):</h3><p>Error: " + error.message + "</p>";
        console.error("Send message test failed:", error);
    }
    
    // Test 3: Get chat history
    console.log("Testing chat history endpoint...");
    try {
        const historyResponse = await fetch("' . $baseUrl . '/history?session_id=chat_test_session_123");
        const historyData = await historyResponse.json();
        results.innerHTML += "<h3>Chat History Test:</h3><pre>" + JSON.stringify(historyData, null, 2) + "</pre>";
        console.log("Chat history test passed:", historyData);
    } catch (error) {
        results.innerHTML += "<h3>Chat History Test (FAILED):</h3><p>Error: " + error.message + "</p>";
        console.error("Chat history test failed:", error);
    }
}

// Run tests when page loads
testChatAPI();
</script>';