<?php

/**
 * Program Card Widget
 *
 * A reusable card component to display program information
 * 
 * @param array $program Array containing program details with keys:
 *                       - id: Program ID
 *                       - title: Program title
 *                       - image: URL to program image
 *                       - description: Short description of the program
 *                       - start_date: Program start date
 *                       - end_date: Program end date
 *                       - url: URL to program details page
 *                       - status: Program status (optional, for ribbon)
 *                       - slug: Program slug for application URL
 */

// Default values if not provided
$program = isset($program) ? $program : [];

// Extract program values into local variables with defaults
$id = $program['id'] ?? 0;
$title = $program['name'] ?? 'Program Title';
$image = $program['banner_url'] ?? '/assets/images/default-program.jpg';
$description = $program['description'] ?? 'Program description goes here';
$description = strlen($description) > 150 ? substr($description, 0, 150) . '...' : $description;
$start_date = $program['start_date'] ?? null;
$end_date = $program['end_date'] ?? null;
$status = $program['status'] ?? 'Upcoming'; // Default status

//  generate slug
helper('url'); // Ensure the helper is loaded
$slug = create_slug($title);

// Calculate duration (in days) from start_date and end_date
$duration = null;
if (!empty($start_date) && !empty($end_date)) {
    $start_date_obj = new DateTime($start_date);
    $end_date_obj = new DateTime($end_date);
    $interval = $start_date_obj->diff($end_date_obj);
    $duration = $interval->days;

    // Format duration for display
    if ($duration == 0) {
        $duration = "1 day"; // Same day program
    } elseif ($duration == 1) {
        $duration = "1 day";
    } else {
        $duration = $duration . " days";
    }
}

// Calculate time until program starts
$days_until_start = null;
if (!empty($start_date)) {
    $start_date_obj = new DateTime($start_date);
    $current_date = new DateTime();
    $interval = $current_date->diff($start_date_obj);
    $days_until_start = $interval->days;

    // Only show days until start if the program hasn't begun yet
    if ($current_date > $start_date_obj) {
        $days_until_start = null;
    }
}

// Format dates for display
$formatted_start_date = !empty($start_date) ? date('M d, Y', strtotime($start_date)) : null;
$formatted_end_date = !empty($end_date) ? date('M d, Y', strtotime($end_date)) : null;

// Build the URLs with program slug
$details_url = "/programs/{$slug}/details";
$apply_url = "/sign-up?program=" . urlencode($slug);
?>

<div class="card ribbon-box h-100 border-0 shadow-sm" id="program-<?= esc($id) ?>">
    <div class="row g-0 h-100">
        <!-- Left column: Program details with ribbons -->
        <div class="col-md-8 position-relative ribbon-box">
            <div class="card-body p-4 mt-5">
                <span class="ribbon-three ribbon-three-danger"><span><?= esc($status) ?></span></span>

                <h2 class="mb-2"><?= esc($title) ?></h2>
                
                <!-- Program details with enhanced styling -->
                <div class="mt-3 pt-2 border-top">
                    <div class="row">
                        <?php if (!empty($duration)): ?>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="avatar-xs">
                                            <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                <i class="ri-calendar-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fs-13">Program Duration</h6>
                                        <small class="text-muted"><?= esc($duration) ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($start_date)): ?>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="avatar-xs">
                                            <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                <i class="ri-calendar-event-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fs-13">Start Date</h6>
                                        <small class="text-muted"><?= esc($formatted_start_date) ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($end_date)): ?>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="avatar-xs">
                                            <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                <i class="ri-calendar-check-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fs-13">End Date</h6>
                                        <small class="text-muted"><?= esc($formatted_end_date) ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($days_until_start)): ?>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="avatar-xs">
                                            <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                <i class="ri-timer-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fs-13">Days Until Start</h6>
                                        <small class="text-muted"><?= esc($days_until_start) ?> days</small>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Two buttons: Learn More and Apply Now -->
                <div class="mt-4 d-flex gap-2">
                    <a href="<?= esc($details_url) ?>" class="btn btn-primary">
                        <i class="ri-information-line me-1"></i> Learn More
                    </a>
                    <a href="<?= esc($apply_url) ?>" class="btn btn-success">
                        <i class="ri-user-add-line me-1"></i> Apply Now
                    </a>
                </div>
            </div>
        </div>

        <!-- Right column: Image -->
        <div class="col-md-4 position-relative">
            <?php if (!empty($image)): ?>
                <img src="<?= esc($image) ?>" alt="<?= esc($title) ?>"
                    class="img-fluid h-100 w-100 program-img"
                    style="object-fit: cover; border-top-right-radius: 0.375rem; border-bottom-right-radius: 0.375rem;"
                    loading="lazy">
            <?php else: ?>
                <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                    <i class="ri-image-line display-4 text-muted"></i>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>