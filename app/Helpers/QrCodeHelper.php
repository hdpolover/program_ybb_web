<?php

/**
 * QR Code Helper
 * 
 * Helper functions to generate QR codes for receipts and other documents
 */

if (!function_exists('generateQRCode')) {
    /**
     * Generate a base64 encoded QR code image for verification purposes
     * 
     * @param string|int $data The data to encode in the QR code
     * @param int $size Size of the QR code in pixels (default 200)
     * @param string $level Error correction level (L, M, Q, H) - default M
     * @return string Base64 encoded string of the QR code image
     */
    function generateQRCode($data, $size = 150, $level = 'M')
    {
        try {
            // Use Google Chart API for QR code generation
            $url = 'https://chart.googleapis.com/chart?cht=qr&chs=' . $size . 'x' . $size . 
                  '&chl=' . urlencode($data) . 
                  '&chld=' . $level . '|0';
                  
            // Initialize cURL session for more reliable fetching
            $ch = curl_init();
            
            // Set cURL options
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            // Execute cURL session
            $qrCode = curl_exec($ch);
            
            // Check if any error occurred
            if (curl_errno($ch)) {
                log_message('error', 'QR Code generation failed: ' . curl_error($ch));
                // Try alternate method if cURL fails
                $qrCode = @file_get_contents($url);
                
                if ($qrCode === false) {
                    return _generateFallbackQR($data);
                }
            }
            
            curl_close($ch);
            
            // Return base64 encoded QR code image
            return base64_encode($qrCode);
            
        } catch (\Exception $e) {
            log_message('error', 'QR Code generation exception: ' . $e->getMessage());
            return _generateFallbackQR($data);
        }
    }
    
    /**
     * Generate a fallback QR code as plain text in an SVG
     * Only used when all other QR generation methods fail
     * 
     * @param string $data The data that would have been in the QR code
     * @return string Base64 encoded SVG with text
     */
    function _generateFallbackQR($data) 
    {
        // Create a simple SVG with the verification URL as text
        $svg = '<?xml version="1.0" encoding="UTF-8"?>';
        $svg .= '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="150" height="150">';
        $svg .= '<rect width="150" height="150" fill="#f8f9fa" stroke="#dee2e6" stroke-width="2"/>';
        $svg .= '<text x="75" y="75" font-family="Arial" font-size="12" text-anchor="middle">Verification URL:</text>';
        $svg .= '<text x="75" y="95" font-family="Arial" font-size="10" text-anchor="middle" fill="#3b7ddd">' . htmlspecialchars($data) . '</text>';
        $svg .= '</svg>';
        
        return base64_encode($svg);
    }
}

if (!function_exists('generateReceiptVerificationURL')) {
    /**
     * Generate a URL that can be used to verify a receipt
     * 
     * @param string|int $paymentId The payment ID
     * @param string|null $transactionCode Optional transaction code
     * @return string URL for verifying the receipt
     */
    function generateReceiptVerificationURL($paymentId, $transactionCode = null)
    {
        $baseUrl = base_url();
        $code = !empty($transactionCode) ? $transactionCode : 'YBB-' . $paymentId;
        
        return $baseUrl . '/verify-payment/' . $code;
    }
}
