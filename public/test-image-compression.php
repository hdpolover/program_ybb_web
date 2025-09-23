<!DOCTYPE html>
<html>
<head>
    <title>Image Compression Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        .test-result { margin: 10px 0; }
        img { max-width: 200px; max-height: 150px; margin: 10px; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <h1>Image Compression Test</h1>
    
    <div class="test-section">
        <h2>Local/Relative Images (Should be processed)</h2>
        
        <?php
        // Test with a local/relative URL
        $local_image = '/assets/images/logo.png';
        $processed_local = function_exists('compress_thumbnail') ? compress_thumbnail($local_image) : $local_image;
        ?>
        
        <div class="test-result">
            <strong>Local Image Test:</strong><br>
            Original: <?= htmlspecialchars($local_image) ?><br>
            Processed: <?= htmlspecialchars($processed_local) ?><br>
            Status: <?= ($local_image !== $processed_local) ? '✅ Processed' : '⚠️ Not processed' ?>
        </div>
    </div>
    
    <div class="test-section">
        <h2>External Images (Should be bypassed in safe mode)</h2>
        
        <?php
        // Test with external URLs
        $external_images = [
            'https://storage.ybbfoundation.com/test.jpg',
            'https://example.com/test.jpg',
            'https://admin.ybbfoundation.com/image.jpg'
        ];
        
        foreach ($external_images as $external_image) {
            $processed_external = function_exists('compress_thumbnail') ? compress_thumbnail($external_image) : $external_image;
            echo '<div class="test-result">';
            echo '<strong>External Image Test:</strong><br>';
            echo 'Original: ' . htmlspecialchars($external_image) . '<br>';
            echo 'Processed: ' . htmlspecialchars($processed_external) . '<br>';
            echo 'Status: ' . (($external_image === $processed_external) ? '✅ Bypassed (Safe)' : '⚠️ Processed (Risk)') . '<br>';
            echo '</div>';
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>Configuration Status</h2>
        <div class="test-result">
            <strong>DISABLE_IMAGE_PROCESSING:</strong> <?= defined('DISABLE_IMAGE_PROCESSING') && DISABLE_IMAGE_PROCESSING ? '✅ Enabled (All processing disabled)' : '❌ Disabled' ?><br>
            <strong>SAFE_IMAGE_PROCESSING_ONLY:</strong> <?= defined('SAFE_IMAGE_PROCESSING_ONLY') && SAFE_IMAGE_PROCESSING_ONLY ? '✅ Enabled (Only safe processing)' : '❌ Disabled' ?><br>
            <strong>Current Host:</strong> <?= $_SERVER['HTTP_HOST'] ?? 'Unknown' ?><br>
        </div>
    </div>
    
    <div class="test-section">
        <h2>Available Functions</h2>
        <div class="test-result">
            <?php
            $functions = ['compress_image', 'compress_hero_image', 'compress_gallery_image', 'compress_thumbnail', 'compress_card_image'];
            foreach ($functions as $func) {
                echo '<strong>' . $func . ':</strong> ' . (function_exists($func) ? '✅ Available' : '❌ Not found') . '<br>';
            }
            ?>
        </div>
    </div>
</body>
</html>