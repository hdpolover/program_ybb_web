<!-- Program Overview with improved modular structure -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-4">
            <div class="flex-shrink-0">
                <div class="avatar-sm">
                    <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-18">
                        <i class="ri-information-line"></i>
                    </div>
                </div>
            </div>
            <div class="flex-grow-1 ms-3">
                <h3 class="card-title mb-0">Overview</h3>
            </div>
        </div>

        <?php if (isset($program['description']) && !empty($program['description'])) : ?>
            <div class="program-description mb-4">
                <?= $program['description'] ?>
            </div>
        <?php else : ?>
            <p class="text-muted mb-4">No description available for this program.</p>
        <?php endif; ?>
        
        <!-- Program Info display -->
        <?php if (isset($program) && !empty($program)): ?>
            <?php
            // Display program info with inline style
            $display_style = 'inline';
            echo $this->include('landing/program-detail/components/program-info', ['program' => $program, 'display_style' => $display_style]);
            ?>
        <?php endif; ?>
        
        <!-- Program Highlights Section -->
        <?php if (isset($program['highlights']) && !empty($program['highlights'])): ?>
            <div class="mt-4">
                <?php echo $this->include('landing/program-detail/components/program-highlights', ['highlights' => $program['highlights']]); ?>
            </div>
        <?php endif; ?>

        <!-- Program Objectives Section -->
        <?php if (isset($program['objectives']) && !empty($program['objectives'])): ?>
            <div class="mt-4">
                <?php echo $this->include('landing/program-detail/components/program-objectives', ['objectives' => $program['objectives']]); ?>
            </div>
        <?php endif; ?>
        
        <!-- Program Requirements Section -->
        <?php if (isset($program['requirements']) && !empty($program['requirements'])): ?>
            <div class="mt-4">
                <?php echo $this->include('landing/program-detail/components/program-requirements', ['requirements' => $program['requirements']]); ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Program Instructors Section (if available) -->
<?php if (isset($program['instructors']) && !empty($program['instructors'])): ?>
    <?php echo $this->include('landing/program-detail/components/program-instructors', ['instructors' => $program['instructors']]); ?>
<?php endif; ?>