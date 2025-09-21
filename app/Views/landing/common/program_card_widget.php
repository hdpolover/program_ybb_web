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

// Use existing slug from program data if available, otherwise generate from title
helper('url'); // Ensure the helper is loaded
$slug = $program['slug'] ?? create_slug($title);

// Calculate duration (in days) from start_date and end_date
$duration = null;
if (!empty($start_date) && !empty($end_date)) {
    $start_date_obj = new DateTime($start_date);
    $end_date_obj = new DateTime($end_date);
    $interval = $start_date_obj->diff($end_date_obj);
    $duration = $interval->days;

    // include the start date in the duration calculation
    if ($start_date_obj->format('Y-m-d') == $end_date_obj->format('Y-m-d')) {
        $duration = 0; // Same day program
    } else {
        $duration = $interval->days + 1; // Include the start date in the duration
    }

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
$details_url = "/programs/{$slug}";
$apply_url = "/sign-up?program=" . urlencode($slug);

// Map status to color classes and labels
$status_color = 'secondary';
$status_lower = strtolower($status);
switch (true) {
    case str_contains($status_lower, 'ongoing'):
    case str_contains($status_lower, 'active'):
        $status_color = 'success';
        break;
    case str_contains($status_lower, 'upcoming'):
    case str_contains($status_lower, 'new'):
        $status_color = 'warning';
        break;
    case str_contains($status_lower, 'completed'):
    case str_contains($status_lower, 'ended'):
        $status_color = 'secondary';
        break;
    case str_contains($status_lower, 'cancel'):
    case str_contains($status_lower, 'closed'):
        $status_color = 'danger';
        break;
}

// Progress percent if current date within range
$progress_percent = null;
try {
    if (!empty($start_date) && !empty($end_date)) {
        $now = new DateTime();
        $s = new DateTime($start_date);
        $e = new DateTime($end_date);
        if ($now >= $s && $now <= $e && $e > $s) {
            $elapsed = $now->getTimestamp() - $s->getTimestamp();
            $total = $e->getTimestamp() - $s->getTimestamp();
            $progress_percent = min(100, max(0, round(($elapsed / $total) * 100)));
        }
    }
} catch (Exception $ex) {
    $progress_percent = null;
}
?>

<style>
/* Enhanced Program Card - Theme Consistent Design */
.program-card {
    position: relative;
    border: none;
    border-radius: 1.25rem;
    overflow: hidden;
    background: linear-gradient(145deg, #ffffff 0%, var(--vz-light, #f8f9fa) 50%, rgba(var(--vz-primary-rgb), 0.02) 100%);
    box-shadow: 0 8px 32px -8px rgba(var(--vz-primary-rgb), 0.15), 0 2px 8px -2px rgba(0, 0, 0, 0.05);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    cursor: pointer;
    border: 1px solid rgba(var(--vz-primary-rgb), 0.08);
}

.program-card:hover {
    transform: translateY(-6px) scale(1.015);
    box-shadow: 0 20px 40px -12px rgba(var(--vz-primary-rgb), 0.25), 
                0 8px 32px -8px rgba(var(--vz-primary-rgb), 0.15),
                0 0 0 1px rgba(var(--vz-primary-rgb), 0.1);
    background: linear-gradient(145deg, #ffffff 0%, var(--vz-light, #f8f9fa) 30%, rgba(var(--vz-primary-rgb), 0.03) 100%);
}

.program-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, 
        var(--vz-primary), 
        var(--vz-info), 
        var(--vz-success));
    z-index: 10;
}

.program-card .program-media {
    position: relative;
    isolation: isolate;
    overflow: hidden;
    padding: 1rem;
    background: linear-gradient(135deg, 
        rgba(var(--vz-primary-rgb), 0.08) 0%, 
        rgba(var(--vz-light-rgb), 0.12) 25%,
        rgba(var(--vz-info-rgb), 0.06) 50%,
        rgba(var(--vz-light-rgb), 0.12) 75%,
        rgba(var(--vz-primary-rgb), 0.08) 100%);
}

.program-card .program-media::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: 
        radial-gradient(circle at 25% 25%, rgba(var(--vz-primary-rgb), 0.03) 0%, transparent 50%),
        radial-gradient(circle at 75% 75%, rgba(var(--vz-info-rgb), 0.03) 0%, transparent 50%);
    z-index: 1;
    pointer-events: none;
}

@media (min-width: 768px) {
    .program-card .program-media { 
        border-radius: 0 1.25rem 1.25rem 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .program-card .program-media .ratio { 
        position: relative;
        width: 100%;
    }
}

@media (max-width: 767.98px) {
    .program-card .program-media { 
        border-radius: 1.25rem 1.25rem 0 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .program-card .program-media .ratio { 
        position: relative;
        width: 100%;
    }
}

.program-card .program-img {
    max-width: 100%;
    max-height: 100%;
    width: auto;
    height: auto;
    object-fit: contain;
    object-position: center;
    transition: all 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
    filter: brightness(1.02) saturate(1.1);
    position: relative;
    z-index: 2;
    display: block;
}

.program-card:hover .program-img {
    transform: scale(1.08);
    filter: brightness(1.08) saturate(1.15);
}

.program-card .media-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, 
        rgba(var(--vz-primary-rgb), 0.08) 0%, 
        rgba(var(--vz-info-rgb), 0.04) 30%, 
        rgba(0, 0, 0, 0.3) 70%, 
        rgba(0, 0, 0, 0.5) 100%);
    opacity: 0.75;
    transition: opacity 0.4s ease;
    pointer-events: none;
}

.program-card:hover .media-overlay {
    opacity: 0.85;
}

/* Enhanced Ribbon Design - Card Positioned */
.program-status-ribbon {
    position: absolute;
    top: -8px;
    right: 20px;
    z-index: 15;
    padding: 0.5rem 1rem;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    border-radius: 0 0 0.5rem 0.5rem;
    color: white;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    box-shadow: 0 4px 12px -4px rgba(0, 0, 0, 0.3);
    transform: translateY(0);
    transition: all 0.3s ease;
}

.program-card:hover .program-status-ribbon {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px -4px rgba(0, 0, 0, 0.4);
}

.program-status-ribbon.status-success {
    background: linear-gradient(135deg, var(--vz-success), rgba(var(--vz-success-rgb), 0.9));
}

.program-status-ribbon.status-warning {
    background: linear-gradient(135deg, var(--vz-warning), rgba(var(--vz-warning-rgb), 0.9));
}

.program-status-ribbon.status-secondary {
    background: linear-gradient(135deg, var(--vz-secondary), rgba(var(--vz-secondary-rgb), 0.9));
}

.program-status-ribbon.status-danger {
    background: linear-gradient(135deg, var(--vz-danger), rgba(var(--vz-danger-rgb), 0.9));
}

.program-status-ribbon.status-info {
    background: linear-gradient(135deg, var(--vz-info), rgba(var(--vz-info-rgb), 0.9));
}

.program-card .content-col {
    position: relative;
    display: flex;
    flex-direction: column;
    height: 100%;
    background: linear-gradient(180deg, 
        rgba(255, 255, 255, 0.98) 0%, 
        rgba(var(--vz-light-rgb), 0.95) 100%);
}

.program-card h2 {
    font-size: 1.45rem;
    font-weight: 700;
    line-height: 1.3;
    background: linear-gradient(135deg, 
        var(--vz-dark), 
        rgba(var(--vz-dark-rgb), 0.8));
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 0.75rem;
}

.program-description {
    color: var(--vz-body-color);
    font-size: 0.95rem;
    line-height: 1.6;
    margin-top: 0.5rem;
    font-weight: 400;
    opacity: 0.85;
}

.program-meta-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
    margin-top: 1.5rem;
    padding: 1.25rem;
    background: linear-gradient(135deg, 
        rgba(var(--vz-primary-rgb), 0.03) 0%, 
        rgba(var(--vz-light-rgb), 0.8) 50%,
        rgba(255, 255, 255, 0.9) 100%);
    border-radius: 1rem;
    border: 1px solid rgba(var(--vz-border-color-rgb), 0.3);
    backdrop-filter: blur(8px);
}

.program-meta-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.6rem;
    border-radius: 0.75rem;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.4);
}

.program-meta-item:hover {
    background: rgba(255, 255, 255, 0.8);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px -4px rgba(var(--vz-primary-rgb), 0.2);
}

.program-meta-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--vz-primary), var(--vz-info));
    color: #fff;
    font-size: 1rem;
    box-shadow: 0 8px 16px -6px rgba(var(--vz-primary-rgb), 0.4);
    position: relative;
    overflow: hidden;
}

.program-meta-icon::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, transparent 0%, rgba(255, 255, 255, 0.25) 50%, transparent 100%);
    transform: translateX(-100%);
    transition: transform 0.6s ease;
}

.program-meta-item:hover .program-meta-icon::before {
    transform: translateX(100%);
}

.program-card .actions {
    margin-top: auto;
    padding-top: 1.5rem;
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.program-card .actions .btn {
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 0.75rem;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: none;
    position: relative;
    overflow: hidden;
    box-shadow: 0 2px 8px -2px currentColor;
}

.program-card .actions .btn::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, transparent 0%, rgba(255, 255, 255, 0.2) 50%, transparent 100%);
    transform: translateX(-100%);
    transition: transform 0.5s ease;
}

.program-card .actions .btn:hover::before {
    transform: translateX(100%);
}

.program-card .actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px -6px currentColor;
}

.program-progress {
    margin-top: 1.25rem;
    padding: 1rem;
    background: linear-gradient(135deg, 
        rgba(var(--vz-success-rgb), 0.08) 0%, 
        rgba(var(--vz-primary-rgb), 0.05) 100%);
    border-radius: 0.75rem;
    border: 1px solid rgba(var(--vz-success-rgb), 0.15);
}

.program-progress small {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 700;
    color: var(--vz-success);
}

.program-progress .progress {
    height: 8px;
    border-radius: 4px;
    background: rgba(var(--vz-border-color-rgb), 0.2);
    overflow: hidden;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
}

.program-progress .progress-bar {
    background: linear-gradient(90deg, var(--vz-success), var(--vz-primary));
    border-radius: 4px;
    position: relative;
    overflow: hidden;
}

.program-progress .progress-bar::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.4) 50%, transparent 100%);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Registration Options Styles */
.registration-options {
    position: relative;
    overflow: hidden;
}

.registration-options::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, var(--vz-primary), var(--vz-info));
    border-radius: 1px;
}

.registration-card {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.registration-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px -4px rgba(var(--vz-primary-rgb), 0.15);
}

.registration-card .btn {
    transition: all 0.3s ease;
    font-weight: 600;
    border-radius: 0.5rem;
}

.registration-card .btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px -4px currentColor;
}

/* Badge enhancements for availability */
.badge {
    font-size: 0.65rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    padding: 0.35rem 0.65rem;
    border-radius: 0.4rem;
}

/* Registration type specific styling */
.registration-card[style*="warning"] {
    border-left: 3px solid var(--vz-warning) !important;
}

.registration-card[style*="success"] {
    border-left: 3px solid var(--vz-success) !important;
}

/* Responsive adjustments for registration cards */
@media (max-width: 767.98px) {
    .registration-options .col-md-6 {
        margin-bottom: 1rem;
    }
    
    .registration-options .col-md-6:last-child {
        margin-bottom: 0;
    }
}

/* Dark mode support for registration options */
[data-bs-theme="dark"] .registration-options {
    background: linear-gradient(135deg, 
        rgba(var(--vz-primary-rgb), 0.08) 0%, 
        rgba(var(--vz-info-rgb), 0.05) 100%) !important;
    border-color: rgba(var(--vz-primary-rgb), 0.2) !important;
}

[data-bs-theme="dark"] .registration-card {
    background: rgba(var(--vz-dark-rgb), 0.5) !important;
    border-color: rgba(var(--vz-border-color-rgb), 0.3) !important;
}

/* Padding refinements */
.program-card .card-body {
    padding: 1.75rem 2rem 2rem;
}

@media (min-width: 768px) {
    .program-card .card-body {
        padding: 2.25rem 2.25rem 2.5rem;
    }
}

/* Dark mode support */
[data-bs-theme="dark"] .program-card {
    background: linear-gradient(145deg, 
        var(--vz-dark) 0%, 
        rgba(var(--vz-dark-rgb), 0.95) 50%, 
        rgba(var(--vz-primary-rgb), 0.03) 100%);
    border-color: rgba(var(--vz-border-color-rgb), 0.3);
}

[data-bs-theme="dark"] .program-card .content-col {
    background: linear-gradient(180deg, 
        rgba(var(--vz-dark-rgb), 0.98) 0%, 
        rgba(var(--vz-dark-rgb), 0.95) 100%);
}

[data-bs-theme="dark"] .program-description {
    color: var(--vz-body-color);
    opacity: 0.9;
}

[data-bs-theme="dark"] .program-meta-grid {
    background: linear-gradient(135deg, 
        rgba(var(--vz-primary-rgb), 0.05) 0%, 
        rgba(var(--vz-dark-rgb), 0.8) 50%,
        rgba(var(--vz-dark-rgb), 0.9) 100%);
    border-color: rgba(var(--vz-border-color-rgb), 0.2);
}

[data-bs-theme="dark"] .program-card h2 {
    background: linear-gradient(135deg, 
        var(--vz-light), 
        rgba(var(--vz-light-rgb), 0.8));
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Registration Options Modal Styles */
.registration-comparison {
    background: linear-gradient(135deg, 
        rgba(var(--vz-light-rgb), 0.8) 0%, 
        rgba(255, 255, 255, 0.9) 100%);
    border-radius: 1rem;
    padding: 2rem;
    margin: 1rem 0;
}

.registration-option-detail {
    background: rgba(255, 255, 255, 0.8);
    border: 2px solid rgba(var(--vz-border-color-rgb), 0.3);
    border-radius: 1rem;
    padding: 1.5rem;
    transition: all 0.3s ease;
    height: 100%;
}

.registration-option-detail:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px -8px rgba(0, 0, 0, 0.15);
}

.registration-option-detail.self-funded {
    border-left: 4px solid #d47e00;
}

.registration-option-detail.fully-funded {
    border-left: 4px solid var(--vz-success);
}

.option-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

/* Dark mode support for registration modal */
[data-bs-theme="dark"] .registration-comparison {
    background: linear-gradient(135deg, 
        rgba(var(--vz-dark-rgb), 0.8) 0%, 
        rgba(var(--vz-dark-rgb), 0.9) 100%);
}

[data-bs-theme="dark"] .registration-option-detail {
    background: rgba(var(--vz-dark-rgb), 0.6);
    border-color: rgba(var(--vz-border-color-rgb), 0.2);
}
</style>
<div class="card program-card border-0 shadow-sm" id="program-<?= esc($id) ?>">
    
    <div class="row g-0 h-100 flex-md-row-reverse">
        <!-- Media / Image -->
        <div class="col-md-4 program-media d-flex p-md-0">
            <?php if (!empty($image)): ?>
                <img src="<?= esc($image) ?>" alt="Image illustrating <?= esc($title) ?>" class="program-img" loading="lazy">
                <div class="media-overlay"></div>
            <?php else: ?>
                <div class="d-flex align-items-center justify-content-center w-100 bg-light position-relative" style="min-height:220px;">
                    <i class="ri-image-line display-6 text-muted"></i>
                </div>
            <?php endif; ?>
        </div>
        <!-- Content -->
        <div class="col-md-8 content-col">
            <div class="card-body p-4 p-md-5 d-flex flex-column">
                <div>
                    <h1 class="mb-2 pe-md-4"><?= esc($title) ?></h2>
                </div>
                <div class="program-meta-grid">
                    <?php if (!empty($duration)): ?>
                        <div class="program-meta-item">
                            <div class="program-meta-icon"><i class="ri-calendar-line"></i></div>
                            <div>
                                <div class="fw-semibold small text-uppercase text-muted mb-1">Duration</div>
                                <div class="fw-medium text-dark"><?= esc($duration) ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($start_date) || !empty($end_date)): ?>
                        <div class="program-meta-item">
                            <div class="program-meta-icon"><i class="ri-calendar-event-line"></i></div>
                            <div>
                                <div class="fw-semibold small text-uppercase text-muted mb-1">Event Dates</div>
                                <div class="fw-medium text-dark">
                                    <?php if (!empty($formatted_start_date) && !empty($formatted_end_date)): ?>
                                        <?= esc($formatted_start_date) ?> - <?= esc($formatted_end_date) ?>
                                    <?php elseif (!empty($formatted_start_date)): ?>
                                        From <?= esc($formatted_start_date) ?>
                                    <?php elseif (!empty($formatted_end_date)): ?>
                                        Until <?= esc($formatted_end_date) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($days_until_start)): ?>
                        <div class="program-meta-item">
                            <div class="program-meta-icon"><i class="ri-timer-line"></i></div>
                            <div>
                                <div class="fw-semibold small text-uppercase text-muted mb-1">Starts In</div>
                                <div class="fw-medium text-dark"><?= esc($days_until_start) ?> day<?= $days_until_start == 1 ? '' : 's' ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if ($progress_percent !== null): ?>
                    <div class="program-progress w-100">
                        <div class="d-flex justify-content-between align-items-end mb-1">
                            <small>Progress</small>
                            <small class="fw-semibold text-muted"><?= esc($progress_percent) ?>%</small>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: <?= esc($progress_percent) ?>%;" aria-valuenow="<?= esc($progress_percent) ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                <?php endif; ?>
                <!-- Registration Payment Options -->
                <?php if (isset($program['registration_payments']) && !empty($program['registration_payments'])): ?>
                    <div class="registration-options mt-3 p-3" style="background: linear-gradient(135deg, rgba(var(--vz-primary-rgb), 0.05) 0%, rgba(var(--vz-info-rgb), 0.03) 100%); border-radius: 0.75rem; border: 1px solid rgba(var(--vz-primary-rgb), 0.1);">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-semibold text-primary mb-0"><i class="ri-bank-card-line me-2"></i>Registration Options</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#registrationOptionsModal">
                                <i class="ri-information-line me-1"></i> Learn More
                            </button>
                        </div>
                        <div class="row g-3">
                            <?php 
                            $registrationPayments = $program['registration_payments'];
                            $currentDate = new DateTime();
                            ?>
                            
                            <?php if (isset($registrationPayments['self_funded'])): ?>
                                <?php 
                                $selfFunded = $registrationPayments['self_funded'];
                                $startDate = new DateTime($selfFunded['start_date']);
                                $endDate = new DateTime($selfFunded['end_date']);
                                $isAvailable = $selfFunded['is_available'] && 
                                              $selfFunded['is_active'] && 
                                              $currentDate >= $startDate && 
                                              $currentDate <= $endDate;
                                ?>
                                <div class="col-md-6">
                                    <div class="registration-card p-3 h-100" style="background: rgba(255, 255, 255, 0.7); border-radius: 0.5rem; border: 1px solid rgba(var(--vz-warning-rgb), 0.2);">
                                        <div class="d-flex align-items-start justify-content-between mb-2">
                                            <h6 class="fw-bold text-warning mb-0"><i class="ri-user-line me-1"></i>Self Funded</h6>
                                            <?php if ($isAvailable): ?>
                                                <span class="badge bg-success-subtle text-success">Available</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary">Not Available</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="mb-2">
                                            <div class="text-warning fw-bold fs-5">$<?= esc($selfFunded['usd_amount']) ?></div>
                                            <small class="text-muted"><?= esc($selfFunded['description']) ?></small>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted d-block">
                                                <i class="ri-calendar-line me-1"></i>
                                                <?= date('M d, Y', strtotime($selfFunded['start_date'])) ?> - 
                                                <?= date('M d, Y', strtotime($selfFunded['end_date'])) ?>
                                            </small>
                                        </div>
                                        <?php if ($isAvailable): ?>
                                            <a href="<?= esc($apply_url . '&type=self_funded') ?>" 
                                               class="btn btn-warning btn-sm w-100">
                                                <i class="ri-user-add-line me-1"></i>Register as Self Funded
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-outline-secondary btn-sm w-100" disabled>
                                                <i class="ri-lock-line me-1"></i>Registration Closed
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($registrationPayments['fully_funded'])): ?>
                                <?php 
                                $fullyFunded = $registrationPayments['fully_funded'];
                                $startDate = new DateTime($fullyFunded['start_date']);
                                $endDate = new DateTime($fullyFunded['end_date']);
                                $isAvailable = $fullyFunded['is_available'] && 
                                              $fullyFunded['is_active'] && 
                                              $currentDate >= $startDate && 
                                              $currentDate <= $endDate;
                                ?>
                                <div class="col-md-6">
                                    <div class="registration-card p-3 h-100" style="background: rgba(255, 255, 255, 0.7); border-radius: 0.5rem; border: 1px solid rgba(var(--vz-success-rgb), 0.2);">
                                        <div class="d-flex align-items-start justify-content-between mb-2">
                                            <h6 class="fw-bold text-success mb-0"><i class="ri-shield-check-line me-1"></i>Fully Funded</h6>
                                            <?php if ($isAvailable): ?>
                                                <span class="badge bg-success-subtle text-success">Available</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary">Not Available</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="mb-2">
                                            <div class="text-success fw-bold fs-5">$<?= esc($fullyFunded['usd_amount']) ?></div>
                                            <small class="text-muted"><?= esc($fullyFunded['description']) ?></small>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted d-block">
                                                <i class="ri-calendar-line me-1"></i>
                                                <?= date('M d, Y', strtotime($fullyFunded['start_date'])) ?> - 
                                                <?= date('M d, Y', strtotime($fullyFunded['end_date'])) ?>
                                            </small>
                                        </div>
                                        <?php if ($isAvailable): ?>
                                            <a href="<?= esc($apply_url . '&type=fully_funded') ?>" 
                                               class="btn btn-success btn-sm w-100">
                                                <i class="ri-shield-check-line me-1"></i>Register as Fully Funded
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-outline-secondary btn-sm w-100" disabled>
                                                <i class="ri-lock-line me-1"></i>Registration Closed
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="actions mt-auto">
                    <a href="<?= esc($details_url) ?>" class="btn btn-primary px-4"><i class="ri-information-line me-1"></i> Details</a>
                    <?php if (is_registration_actually_available($program)): ?>
                        <!-- Show general Apply button only if no registration payments are available -->
                        <?php if (!isset($program['registration_payments']) || empty($program['registration_payments'])): ?>
                            <a href="<?= esc($apply_url) ?>" class="btn btn-success px-4"><i class="ri-user-add-line me-1"></i> Apply</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Registration Options Information Modal -->
<div class="modal fade" id="registrationOptionsModal" tabindex="-1" aria-labelledby="registrationOptionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registrationOptionsModalLabel">
                    <i class="ri-user-settings-line me-2"></i>Registration Options for <?= esc($title) ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="registration-comparison">
                    <div class="text-center mb-4">
                        <h6 class="text-primary mb-2">Understanding Your Registration Path</h6>
                        <p class="text-muted">Each registration type offers different experiences and requirements. Both options follow the same payment schedule with fees paid in batches over time, but the funding source differs based on your selection status.</p>
                    </div>

                    <div class="row g-4">
                        <!-- Self Funded Option -->
                        <div class="col-md-6">
                            <div class="registration-option-detail self-funded">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="option-icon bg-warning-subtle me-3" style="background-color: rgba(235, 144, 49, 0.1) !important; color: #d47e00 !important;">
                                        <i class="ri-user-line"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0" style="color: #d47e00 !important;">Self Funded</h6>
                                        <small class="text-muted">Standard Registration</small>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <h6 class="fs-6 mb-2">Requirements:</h6>
                                    <ul class="list-unstyled small">
                                        <li><i class="ri-check-line text-success me-1"></i> Complete registration form and documentation</li>
                                        <li><i class="ri-check-line text-success me-1"></i> Submit required documents on time</li>
                                        <li><i class="ri-calendar-check-line text-success me-1"></i> Pay fees according to scheduled payment batches</li>
                                        <li><i class="ri-speed-line text-info me-1"></i> Streamlined administrative processing</li>
                                    </ul>
                                </div>

                                <div class="mb-3">
                                    <h6 class="fs-6 mb-2">Benefits:</h6>
                                    <ul class="list-unstyled small">
                                        <li><i class="ri-shield-check-line text-success me-1"></i> Guaranteed program participation</li>
                                        <li><i class="ri-timer-flash-line text-success me-1"></i> Faster application processing</li>
                                        <li><i class="ri-user-check-line text-success me-1"></i> No competitive selection required</li>
                                    </ul>
                                </div>

                                <div class="p-2 rounded" style="background-color: rgba(235, 144, 49, 0.1) !important;">
                                    <small style="color: #d47e00 !important;"><i class="ri-wallet-3-line me-1"></i> <strong>Payment:</strong> You pay all scheduled fee batches yourself</small>
                                </div>
                            </div>
                        </div>

                        <!-- Fully Funded Option -->
                        <div class="col-md-6">
                            <div class="registration-option-detail fully-funded">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="option-icon bg-success-subtle text-success me-3">
                                        <i class="ri-shield-check-line"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-success">Fully Funded</h6>
                                        <small class="text-muted">Reimbursement System</small>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <h6 class="fs-6 mb-2">Requirements:</h6>
                                    <ul class="list-unstyled small">
                                        <li><i class="ri-check-line text-success me-1"></i> Complete registration form and documentation</li>
                                        <li><i class="ri-file-text-line text-warning me-1"></i> Submit detailed essays and applications</li>
                                        <li><i class="ri-question-answer-line text-warning me-1"></i> Participate in interviews and evaluations</li>
                                        <li><i class="ri-calendar-check-line text-success me-1"></i> Pay fees according to scheduled payment batches initially</li>
                                        <li><i class="ri-award-line text-info me-1"></i> Meet selection criteria and deadlines</li>
                                    </ul>
                                </div>

                                <div class="mb-3">
                                    <h6 class="fs-6 mb-2">Benefits (If Selected):</h6>
                                    <ul class="list-unstyled small">
                                        <li><i class="ri-money-dollar-circle-line text-success me-1"></i> Full reimbursement of all payments</li>
                                        <li><i class="ri-star-line text-success me-1"></i> Enhanced program recognition</li>
                                        <li><i class="ri-team-line text-success me-1"></i> Access to exclusive fully funded activities</li>
                                        <li><i class="ri-graduation-cap-line text-success me-1"></i> Additional mentorship opportunities</li>
                                    </ul>
                                </div>

                                <div class="p-2 bg-success-subtle rounded">
                                    <small class="text-success"><i class="ri-money-dollar-circle-line me-1"></i> <strong>Payment:</strong> Pay scheduled batches initially, get <strong>full refund</strong> if selected</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Important Notes -->
                    <div class="mt-4 p-3 bg-info-subtle rounded">
                        <h6 class="text-info mb-2"><i class="ri-information-line me-1"></i> Important Payment & Selection Information</h6>
                        <ul class="list-unstyled small mb-0">
                            <li class="mb-2"><i class="ri-calendar-schedule-line text-info me-1"></i> <strong>Payment Schedule:</strong> All participants pay program fees in scheduled batches over time - not as a single upfront payment</li>
                            <li class="mb-2"><i class="ri-shield-check-line text-info me-1"></i> <strong>Fully Funded Process:</strong> Pay each batch as scheduled, complete essays and interviews, then get full reimbursement if selected</li>
                            <li class="mb-2"><i class="ri-group-line text-info me-1"></i> <strong>Selection Quota:</strong> Fully funded slots are limited and competitive based on qualifications and available funding</li>
                            <li class="mb-2"><i class="ri-user-check-line text-info me-1"></i> <strong>Self Funded Guarantee:</strong> Guaranteed participation without competitive selection - just follow payment schedule</li>
                            <li class="mb-0"><i class="ri-alert-line text-info me-1"></i> <strong>Important:</strong> If not selected for fully funded, you continue as self-funded with no refund</li>
                        </ul>
                    </div>

                    <!-- Available Options for this Program -->
                    <?php if (isset($program['registration_payments']) && !empty($program['registration_payments'])): ?>
                        <div class="mt-4 p-3 bg-light rounded">
                            <h6 class="text-dark mb-3"><i class="ri-calendar-check-line me-1"></i> Available for <?= esc($title) ?></h6>
                            <div class="row g-2">
                                <?php if (isset($registrationPayments['self_funded'])): ?>
                                    <?php 
                                    $selfFunded = $registrationPayments['self_funded'];
                                    $startDate = new DateTime($selfFunded['start_date']);
                                    $endDate = new DateTime($selfFunded['end_date']);
                                    $isAvailable = $selfFunded['is_available'] && 
                                                  $selfFunded['is_active'] && 
                                                  $currentDate >= $startDate && 
                                                  $currentDate <= $endDate;
                                    ?>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-2 rounded" style="background-color: rgba(235, 144, 49, 0.1);">
                                            <i class="ri-user-line me-2" style="color: #d47e00;"></i>
                                            <div class="flex-grow-1">
                                                <div class="fw-medium" style="color: #d47e00;">Self Funded - $<?= esc($selfFunded['usd_amount']) ?></div>
                                                <small class="text-muted"><?= $isAvailable ? 'Available Now' : 'Not Currently Available' ?></small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (isset($registrationPayments['fully_funded'])): ?>
                                    <?php 
                                    $fullyFunded = $registrationPayments['fully_funded'];
                                    $startDate = new DateTime($fullyFunded['start_date']);
                                    $endDate = new DateTime($fullyFunded['end_date']);
                                    $isAvailable = $fullyFunded['is_available'] && 
                                                  $fullyFunded['is_active'] && 
                                                  $currentDate >= $startDate && 
                                                  $currentDate <= $endDate;
                                    ?>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-2 bg-success-subtle rounded">
                                            <i class="ri-shield-check-line text-success me-2"></i>
                                            <div class="flex-grow-1">
                                                <div class="fw-medium text-success">Fully Funded - $<?= esc($fullyFunded['usd_amount']) ?></div>
                                                <small class="text-muted"><?= $isAvailable ? 'Available Now' : 'Not Currently Available' ?></small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>