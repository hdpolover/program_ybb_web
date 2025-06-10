<?php
// Simple test script to verify API call
$email = 'hendrapolover@gmail.com';
$programId = '7';
$apiBaseUrl = 'https://admin.ybbfoundation.com/api';
$endpoint = "/participants/search?email={$email}&program_id={$programId}";
$url = $apiBaseUrl . $endpoint;

echo "<h3>Testing API Call</h3>";
echo "<p><strong>URL:</strong> {$url}</p>";

// Initialize curl
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "<p><strong>HTTP Code:</strong> {$httpCode}</p>";

if ($error) {
    echo "<p><strong>cURL Error:</strong> {$error}</p>";
}

echo "<p><strong>Response:</strong></p>";
echo "<pre>" . json_encode(json_decode($response, true), JSON_PRETTY_PRINT) . "</pre>";
?>
