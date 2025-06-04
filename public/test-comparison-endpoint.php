<?php
/**
 * Test script for Abstract Version Comparison with existing endpoint
 * This demonstrates how the comparison feature works with real data
 */

echo "<!DOCTYPE html>\n";
echo "<html><head><title>Comparison Endpoint Test</title>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;}pre{background:#f5f5f5;padding:10px;border-radius:5px;overflow:auto;}</style>";
echo "</head><body>\n";
echo "<h1>Abstract Version Comparison Endpoint Test</h1>\n";

// Test the endpoint URL structure
$testUrl1 = "/abstract-versions/compare/5/9";
echo "<h2>Test Case 1: Example from User</h2>\n";
echo "<p><strong>Endpoint:</strong> <code>{$testUrl1}</code></p>\n";
echo "<p>This should return comparison data between version 5 and version 9.</p>\n";

echo "<h2>Expected Response Structure</h2>\n";
echo "<p>The endpoint should return data in this format:</p>\n";

$expectedStructure = [
    "abstract" => [
        "id" => "6",
        "primary_participant_id" => "32045",
        "program_id" => "4",
        "status" => "submitted"
        // ... other fields
    ],
    "authors" => [
        [
            "id" => "2",
            "full_name" => "suhendra test",
            "institution" => "ysy"
            // ... other fields
        ]
    ],
    "version1" => [
        "id" => "5",
        "title" => "ghggh",
        "content" => "<p><br></p>",
        "version_number" => "1"
        // ... other fields
    ],
    "version2" => [
        "id" => "9", 
        "title" => "YBB organisasi pemuda dunia",
        "content" => "<p>YBB adalah organisasi pemuda dunia</p>",
        "version_number" => "4"
        // ... other fields
    ],
    "comparison" => [
        "summary" => [
            "has_changes" => true,
            "total_changes" => 5,
            "changed_fields" => ["title", "content", "keywords", "status", "version_number"]
        ],
        "fields" => [
            [
                "field" => "title",
                "label" => "Title", 
                "has_change" => true,
                "version1_value" => "ghggh",
                "version2_value" => "YBB organisasi pemuda dunia",
                "version1_word_count" => 1,
                "version2_word_count" => 4,
                "word_count_difference" => 3
            ]
            // ... other fields
        ],
        "metadata" => [
            "version1_created_at" => "2025-05-30 14:26:42",
            "version2_created_at" => "2025-05-31 22:22:40",
            "time_difference" => 114958
        ]
    ]
];

echo "<pre>" . json_encode($expectedStructure, JSON_PRETTY_PRINT) . "</pre>\n";

echo "<h2>How to Test</h2>\n";
echo "<ol>\n";
echo "<li><strong>Browser Test:</strong> Navigate to <code>/abstract-versions/compare/5/9</code></li>\n";
echo "<li><strong>AJAX Test:</strong> Make a request with <code>Accept: application/json</code> header</li>\n"; 
echo "<li><strong>View Test:</strong> The same URL should render the comparison view for regular browser requests</li>\n";
echo "</ol>\n";

echo "<h2>Controller Integration</h2>\n";
echo "<p>The controller now uses the existing comparison endpoint instead of making separate API calls:</p>\n";
echo "<ul>\n";
echo "<li>✓ Simplified logic - no need for separate version fetching</li>\n";
echo "<li>✓ Better performance - single API call instead of multiple</li>\n";
echo "<li>✓ Consistent data format - matches the existing endpoint response</li>\n";
echo "<li>✓ Security validation - participant access control maintained</li>\n";
echo "</ul>\n";

echo "<h2>JavaScript Integration</h2>\n";
echo "<p>The JavaScript module should work seamlessly with the new data structure:</p>\n";
echo "<ul>\n";
echo "<li>✓ Field comparison rendering</li>\n";
echo "<li>✓ Statistics display</li>\n";
echo "<li>✓ Interactive features (expand/collapse, copy, search)</li>\n";
echo "<li>✓ Export and print functionality</li>\n";
echo "</ul>\n";

echo "<h2>Testing Different Scenarios</h2>\n";
echo "<p>Test these scenarios to ensure robust functionality:</p>\n";
echo "<ul>\n";
echo "<li><strong>Valid comparison:</strong> <code>/abstract-versions/compare/5/9</code></li>\n";
echo "<li><strong>Same version:</strong> <code>/abstract-versions/compare/5/5</code> (should return error)</li>\n";
echo "<li><strong>Non-existent version:</strong> <code>/abstract-versions/compare/999/1000</code> (should return 404)</li>\n";
echo "<li><strong>Unauthorized access:</strong> Try with different participant session</li>\n";
echo "</ul>\n";

echo "<h2>Expected Error Responses</h2>\n";
$errorExamples = [
    "missing_parameters" => [
        "status" => "error",
        "message" => "Both version IDs are required.",
        "error_code" => "MISSING_PARAMETERS"
    ],
    "same_version" => [
        "status" => "error", 
        "message" => "Cannot compare a version with itself.",
        "error_code" => "SAME_VERSION"
    ],
    "not_found" => [
        "status" => "error",
        "message" => "Could not retrieve comparison data. Versions may not exist or you may not have access.",
        "error_code" => "COMPARISON_NOT_FOUND"
    ],
    "access_denied" => [
        "status" => "error",
        "message" => "You do not have permission to access these versions.",
        "error_code" => "ACCESS_DENIED"
    ]
];

foreach ($errorExamples as $type => $example) {
    echo "<h4>" . ucwords(str_replace('_', ' ', $type)) . "</h4>\n";
    echo "<pre>" . json_encode($example, JSON_PRETTY_PRINT) . "</pre>\n";
}

echo "<p><strong>Status:</strong> ✅ Implementation updated to use existing comparison endpoint</p>\n";
echo "<p><strong>Next Steps:</strong> Test the endpoint with real data to verify functionality</p>\n";

echo "</body></html>\n";
?>
