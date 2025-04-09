<?php
// This file is part of the registration CTA section for a program detail page.
// It includes a card that provides information about registration guidelines and a link to download the guidelines.

// The card is styled with Bootstrap classes and includes an icon, title, description, and a button to view the guidelines.

// The program data is assumed to be passed to this view, which contains the registration guidelines link.

$program = $program ?? [];
$guidelineLink = $program['guideline'] ?? '#';
?>

<!-- Registration Guidelines CTA Section -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 bg-primary position-relative overflow-hidden">
            <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
            <div class="card-body p-5 position-relative">
                <div class="row align-items-center">
                    <div class="col-lg-2 col-md-2 text-center">
                        <div class="avatar-lg mx-auto mb-4">
                            <div class="avatar-title bg-white bg-opacity-25 rounded-circle">
                                <i class="ri-file-list-3-line text-white fs-24"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-10 col-md-10">
                        <h2 class="text-white mb-2">Registration Guidelines</h2>
                        <p class="text-white mb-4 fs-16">Download our comprehensive registration guide to understand the application process, requirements, and important deadlines for this program.</p>                        <div class="d-flex flex-wrap gap-2">
                            <a href="<?= $guidelineLink ?>" class="btn btn-info" target="_blank">
                                <i class="ri-eye-line me-1 align-bottom"></i> View Guidelines
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>