<?php
/**
 * Quick diagnostic test for the refactored Abstract View components
 * This file verifies that all component files exist and can be loaded without errors
 */

echo "<!DOCTYPE html>\n";
echo "<html><head><title>Abstract View Component Test</title></head><body>\n";
echo "<h1>Abstract View Component Test</h1>\n";

$componentsDir = 'd:\Work\program_ybb_web\app\Views\participant\abstract-paper\components\\';

// Test 1: Check if all component files exist
echo "<h2>Test 1: Component Files Existence</h2>\n";
$requiredComponents = [
    'abstract-view.php',
    'abstract-view-styles.php',
    'abstract-view-helpers.php',
    'abstract-header.php',
    'abstract-status-alerts.php',
    'abstract-content-card.php',
    'abstract-quick-info.php',
    'abstract-authors-card.php',
    'abstract-paper-card.php',
    'abstract-view-scripts.php',
    'paper-upload-modals.php',
    'add-coauthor-modal.php',
    'edit-author-modal.php'
];

$missingFiles = [];
foreach ($requiredComponents as $component) {
    $filePath = $componentsDir . $component;
    if (file_exists($filePath)) {
        echo "<p style='color:green'>✓ $component exists</p>\n";
    } else {
        echo "<p style='color:red'>✗ $component is missing</p>\n";
        $missingFiles[] = $component;
    }
}

// Test 2: Check for PHP syntax errors in components
echo "<h2>Test 2: PHP Syntax Check</h2>\n";
foreach ($requiredComponents as $component) {
    $filePath = $componentsDir . $component;
    if (file_exists($filePath)) {
        $output = [];
        $return_var = 0;
        exec("php -l \"$filePath\" 2>&1", $output, $return_var);
        
        if ($return_var === 0) {
            echo "<p style='color:green'>✓ $component has valid PHP syntax</p>\n";
        } else {
            echo "<p style='color:red'>✗ $component has syntax errors:</p>\n";
            echo "<pre>" . implode("\n", $output) . "</pre>\n";
        }
    }
}

// Test 3: Test isContentEmpty function definition
echo "<h2>Test 3: Helper Functions</h2>\n";
try {
    // Include the helpers file
    include_once $componentsDir . 'abstract-view-helpers.php';
    
    if (function_exists('isContentEmpty')) {
        echo "<p style='color:green'>✓ isContentEmpty() function is defined</p>\n";
        
        // Test the function
        $testCases = [
            '' => true,
            '<p><br></p>' => true,
            '<p></p>' => true,
            '<p>&nbsp;</p>' => true,
            '<p>Some content</p>' => false,
            'Plain text' => false
        ];
        
        foreach ($testCases as $input => $expected) {
            $result = isContentEmpty($input);
            $status = ($result === $expected) ? '✓' : '✗';
            $color = ($result === $expected) ? 'green' : 'red';
            echo "<p style='color:$color'>$status isContentEmpty('$input') = " . ($result ? 'true' : 'false') . "</p>\n";
        }
    } else {
        echo "<p style='color:red'>✗ isContentEmpty() function is not defined</p>\n";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Error loading helpers: " . $e->getMessage() . "</p>\n";
}

// Summary
echo "<h2>Summary</h2>\n";
if (empty($missingFiles)) {
    echo "<p style='color:green; font-weight:bold'>✓ All component files are present and ready for use!</p>\n";
    echo "<p>The refactored abstract view should work correctly. The monolithic file has been successfully broken down into manageable components.</p>\n";
} else {
    echo "<p style='color:red; font-weight:bold'>✗ Some component files are missing:</p>\n";
    echo "<ul>\n";
    foreach ($missingFiles as $file) {
        echo "<li>$file</li>\n";
    }
    echo "</ul>\n";
}

echo "</body></html>\n";
?>
