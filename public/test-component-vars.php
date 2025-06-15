<?php
/**
 * Simple test to check if the abstract view components can be loaded without errors
 */

// Simulate the variables that would be passed from the controller
$participant_data = [
    'abstract' => [
        'id' => '123',
        'status' => 'draft',
        'created_at' => '2025-06-15 10:00:00',
        'updated_at' => '2025-06-15 10:30:00',
        'subtheme_name' => 'Test Subtheme',
        'versions' => [
            [
                'id' => '1',
                'version_number' => 1,
                'title' => 'Test Abstract',
                'content' => 'Test content',
                'created_at' => '2025-06-15 10:00:00',
                'updated_at' => '2025-06-15 10:30:00',
                'status' => 'draft'
            ]
        ],
        'authors' => [
            [
                'id' => '1',
                'full_name' => 'Test Author',
                'email' => 'test@example.com',
                'institution' => 'Test University'
            ]
        ],
        'feedbacks' => []
    ]
];

echo "<h1>Abstract View Component Variable Test</h1>\n";
echo "<p>Testing if essential variables are properly defined...</p>\n";

// Test each component
$components = [
    'abstract-view-helpers.php',
    'abstract-header.php', 
    'abstract-status-alerts.php',
    'abstract-content-card.php',
    'abstract-quick-info.php'
];

foreach ($components as $component) {
    echo "<h3>Testing: $component</h3>\n";
    
    try {
        // Start output buffering to capture any errors
        ob_start();
        
        // Include the component
        include 'd:\Work\program_ybb_web\app\Views\participant\abstract-paper\components\\' . $component;
        
        // Get any output
        $output = ob_get_clean();
        
        echo "<p style='color:green'>✓ $component loaded successfully</p>\n";
        
        // Check if essential variables were defined
        if (isset($latestVersion)) {
            echo "<p style='color:green'>✓ \$latestVersion defined</p>\n";
        }
        if (isset($latestVersionNumber)) {
            echo "<p style='color:green'>✓ \$latestVersionNumber defined</p>\n";
        }
        if (isset($abstractStatus)) {
            echo "<p style='color:green'>✓ \$abstractStatus defined: $abstractStatus</p>\n";
        }
        if (isset($abstract)) {
            echo "<p style='color:green'>✓ \$abstract array defined</p>\n";
        }
        
    } catch (Throwable $e) {
        echo "<p style='color:red'>✗ Error in $component: " . $e->getMessage() . "</p>\n";
        echo "<p style='color:red'>Line: " . $e->getLine() . "</p>\n";
    }
    
    echo "<hr>\n";
}

echo "<h3>Test Summary</h3>\n";
echo "<p>If all components show green checkmarks, the refactored abstract view should work correctly.</p>\n";
?>
