#!/usr/bin/env php
<?php
/**
 * Generate Encryption Key
 * 
 * This script generates a secure random encryption key for the application.
 * Usage: php generate-encryption-key.php
 */

echo "=== YBB Platform Encryption Key Generator ===\n\n";

// Generate a random 32-byte (256-bit) key
$key = bin2hex(random_bytes(32));

echo "Generated Encryption Key:\n";
echo "------------------------\n";
echo $key . "\n";
echo "------------------------\n\n";

echo "Instructions:\n";
echo "1. Copy the key above\n";
echo "2. Open your .env file\n";
echo "3. Find the line: encryption.key = \n";
echo "4. Replace it with: encryption.key = $key\n";
echo "5. Save the file and restart your application\n\n";

echo "⚠️  IMPORTANT:\n";
echo "   - Keep this key secret and secure\n";
echo "   - Do not commit this key to version control\n";
echo "   - Store it securely (use environment variables in production)\n";
echo "   - If you lose this key, all encrypted data will be unrecoverable\n\n";

// Optional: Write to .env file if user confirms
echo "Would you like to automatically update your .env file? (y/n): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
if (trim(strtolower($line)) === 'y') {
    $envFile = dirname(__DIR__) . '/.env';
    
    if (!file_exists($envFile)) {
        echo "❌ Error: .env file not found at: $envFile\n";
        echo "Please create a .env file first (copy from .env.example)\n";
        exit(1);
    }
    
    $content = file_get_contents($envFile);
    
    // Check if encryption.key already exists
    if (preg_match('/^encryption\.key\s*=\s*.*$/m', $content)) {
        // Replace existing key
        $newContent = preg_replace(
            '/^encryption\.key\s*=\s*.*$/m',
            "encryption.key = $key",
            $content
        );
    } else {
        // Add new key
        $newContent = $content . "\nencryption.key = $key\n";
    }
    
    if (file_put_contents($envFile, $newContent)) {
        echo "✅ Successfully updated .env file!\n";
    } else {
        echo "❌ Error: Could not write to .env file\n";
        echo "Please update manually with the key above.\n";
    }
} else {
    echo "Skipped automatic update. Please update .env manually.\n";
}

echo "\n✅ Done!\n";
