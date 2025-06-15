<?php
// Test version sorting logic
$testVersions = [
    ['id' => 1, 'version_number' => 1, 'title' => 'Initial Abstract', 'created_at' => '2024-01-01 10:00:00'],
    ['id' => 2, 'version_number' => 2, 'title' => 'Revised Abstract', 'created_at' => '2024-01-02 10:00:00'],
    ['id' => 3, 'version_number' => 3, 'title' => 'Final Abstract', 'created_at' => '2024-01-03 10:00:00'],
];

echo "Original order:\n";
foreach ($testVersions as $i => $version) {
    echo "[$i] v{$version['version_number']} - {$version['title']} ({$version['created_at']})\n";
}

// Sort versions by version_number descending (latest first)
usort($testVersions, function($a, $b) {
    // Primary sort by version_number descending
    $versionCompare = intval($b['version_number']) - intval($a['version_number']);
    if ($versionCompare !== 0) {
        return $versionCompare;
    }
    // Secondary sort by created_at descending
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

echo "\nAfter sorting (latest first):\n";
foreach ($testVersions as $i => $version) {
    echo "[$i] v{$version['version_number']} - {$version['title']} ({$version['created_at']})\n";
    // Compare with previous (older) version
    if ($i < count($testVersions) - 1) {
        echo "    → Compare with v{$testVersions[$i+1]['version_number']} (previous version)\n";
    } else {
        echo "    → Original version - no comparison available\n";
    }
}
?>
