<?php
require_once 'vendor/autoload.php';

// Use CodeIgniter's HTTP client to test the participant search
$client = \Config\Services::curlrequest([
    'timeout' => 30,
    'verify'  => false,
]);

$email = 'hendrapolover@gmail.com';
$programId = '7';
$apiBaseUrl = 'https://admin.ybbfoundation.com/api';
$endpoint = "/participants/search?email={$email}&program_id={$programId}";
$url = $apiBaseUrl . $endpoint;

echo "<h3>Testing Participant Search API Call</h3>";
echo "<p><strong>URL:</strong> {$url}</p>";
echo "<p><strong>Email:</strong> {$email}</p>";
echo "<p><strong>Program ID:</strong> {$programId}</p>";

try {
    $response = $client->request('GET', $url, [
        'headers' => [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ]
    ]);
    
    $statusCode = $response->getStatusCode();
    $body = $response->getBody();
    $decoded = json_decode($body, true);
    
    echo "<p><strong>HTTP Status Code:</strong> {$statusCode}</p>";
    echo "<p><strong>Response:</strong></p>";
    echo "<pre>" . json_encode($decoded, JSON_PRETTY_PRINT) . "</pre>";
    
    // Test the response structure
    if (isset($decoded['status']) && $decoded['status'] === 'success') {
        echo "<div style='color: green;'><strong>✓ API Call Successful!</strong></div>";
        
        if (isset($decoded['data'])) {
            $participant = $decoded['data'];
            echo "<div style='margin-top: 20px;'>";
            echo "<h4>Participant Details:</h4>";
            echo "<p><strong>ID:</strong> " . ($participant['id'] ?? 'N/A') . "</p>";
            echo "<p><strong>Name:</strong> " . ($participant['full_name'] ?? 'N/A') . "</p>";
            echo "<p><strong>Email:</strong> " . ($participant['user']['email'] ?? $participant['email'] ?? 'N/A') . "</p>";
            echo "<p><strong>Institution:</strong> " . ($participant['institution'] ?? 'N/A') . "</p>";
            echo "</div>";
        }
    } else {
        echo "<div style='color: red;'><strong>✗ API Call Failed or No Data</strong></div>";
    }
    
} catch (\Exception $e) {
    echo "<p style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
}
?>
