<?php
// Helper function to check if content is effectively empty (handles Quill's empty states)
function isContentEmpty($content)
{
    if (empty($content)) return true;

    // Remove common Quill empty states
    $cleanContent = str_replace(['<p><br></p>', '<p></p>', '<p>&nbsp;</p>', '<br>', '&nbsp;'], '', $content);
    $cleanContent = trim(strip_tags($cleanContent));

    return empty($cleanContent);
}

// Prepare sorted versions for use throughout the template with safety checks
$participant_data = $participant_data ?? [];
$abstract = $participant_data['abstract'] ?? [];
$versions = !empty($abstract['versions']) ? $abstract['versions'] : [];

// Sort versions by version_number in descending order to ensure latest is first
if (!empty($versions)) {
    usort($versions, function ($a, $b) {
        $a_version = isset($a['version_number']) ? (int)$a['version_number'] : 0;
        $b_version = isset($b['version_number']) ? (int)$b['version_number'] : 0;
        return $b_version - $a_version; // Descending order
    });
    
    // Update the versions array in participant_data to use our sorted version
    if (isset($participant_data['abstract'])) {
        $participant_data['abstract']['versions'] = $versions;
    }
}

// Get the latest version (first after sorting)
$latestVersion = !empty($versions) ? $versions[0] : null;
$versionCount = count($versions);
$latestVersionNumber = isset($latestVersion['version_number']) ? $latestVersion['version_number'] : 1;

// Abstract status and editing permissions
$abstractStatus = isset($abstract['status']) ? strtolower($abstract['status']) : 'draft';
$hasFeedback = !empty($abstract['feedbacks']);

// Participants can only edit if:
// 1. Status is 'draft' OR 'under_review', OR
// 2. Status is 'submitted' AND there is reviewer feedback requiring revisions
// 3. Status is NOT 'accepted' (accepted abstracts are final)
$canEdit = ($abstractStatus === 'draft' || $abstractStatus === 'under_review') ||
    ($abstractStatus === 'submitted' && $hasFeedback);

// Never allow editing if status is 'accepted'
if ($abstractStatus === 'accepted') {
    $canEdit = false;
}
