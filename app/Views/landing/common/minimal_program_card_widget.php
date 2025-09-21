<?php

/**
 * Minimal Program Card Widget
 *
 * A compact card component to display secondary program information
 * 
 * @param array $program Array containing program details
 */

// Default values if not provided
$program = isset($program) ? $program : [];

// Extract program values into local variables with defaults
$id = $program['id'] ?? 0;
$title = $program['name'] ?? 'Program Title';
$image = $program['banner_url'] ?? '/assets/images/default-program.jpg';
$description = $program['description'] ?? 'Program description goes here';
$description = strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description;
$start_date = $program['start_date'] ?? null;
$end_date = $program['end_date'] ?? null;
$status = $program['status'] ?? 'Upcoming'; // Default status

// Use existing slug from program data if available, otherwise generate from title
helper('url'); // Ensure the helper is loaded
$slug = $program['slug'] ?? create_slug($title);

// Format dates for display
$formatted_start_date = !empty($start_date) ? date('M d, Y', strtotime($start_date)) : null;

// Build the URLs with program slug
$apply_url = "/sign-up?program=" . urlencode($slug);
?>

<div class="card h-100 border-0 shadow-sm" id="program-<?= esc($id) ?>">
    <div class="position-relative">
        <?php if (!empty($image)): ?>
            <img src="<?= esc($image) ?>" alt="<?= esc($title) ?>"
                class="card-img-top program-img"
                style="height: 140px; object-fit: cover;"
                loading="lazy">
        <?php else: ?>
            <div class="d-flex align-items-center justify-content-center bg-light" style="height: 140px;">
                <i class="ri-image-line fs-1 text-muted"></i>
            </div>
        <?php endif; ?>
        <?php if (!empty($status)): ?>
            <div class="position-absolute top-0 end-0 m-2">
                <span class="badge bg-primary"><?= esc($status) ?></span>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="card-body p-3">
        <h5 class="card-title mb-2"><?= esc($title) ?></h5>
        
        <?php if (!empty($formatted_start_date)): ?>
        <div class="d-flex align-items-center mt-2 mb-3">
            <i class="ri-calendar-event-line text-muted me-1"></i>
            <small class="text-muted"><?= esc($formatted_start_date) ?></small>
        </div>
        <?php endif; ?>
        
        <p class="card-text small text-muted mb-3"><?= esc($description) ?></p>
        
        <?php if (is_registration_actually_available($program)) : ?>
            <a href="<?= esc($apply_url) ?>" class="btn btn-sm btn-outline-primary w-100">
                <i class="ri-user-add-line me-1"></i> Apply Now
            </a>
        <?php else: ?>
            <button class="btn btn-sm btn-outline-secondary w-100" disabled>
                <i class="ri-calendar-event-line me-1"></i> Registration Closed
            </button>
        <?php endif; ?>
    </div>
</div>