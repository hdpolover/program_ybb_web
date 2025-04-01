<?php
/**
 * Program Highlights Component
 * 
 * Displays key highlights or features of a program
 * 
 * @param array|string $highlights The program highlights array or string
 */
?>

<div class="program-highlights mb-4">
    <div class="d-flex align-items-center mb-4">
        <div class="flex-shrink-0">
            <div class="avatar-sm">
                <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-18">
                    <i class="ri-award-line"></i>
                </div>
            </div>
        </div>
        <div class="flex-grow-1 ms-3">
            <h4 class="mb-0">Program Highlights</h4>
        </div>
    </div>

    <div class="row g-3">
        <?php 
        if (!empty($highlights)):
            // Handle both array and string formats
            $highlightItems = is_array($highlights) ? $highlights : explode("\n", $highlights);
            foreach ($highlightItems as $index => $highlight):
                if (trim($highlight)):
                    // Determine which icon to use based on index
                    $icons = ['ri-rocket-line', 'ri-medal-line', 'ri-team-line', 'ri-calendar-check-line', 'ri-lightbulb-flash-line', 'ri-tools-line'];
                    $icon = $icons[$index % count($icons)];
        ?>
        <div class="col-md-6">
            <div class="d-flex p-3 border rounded">
                <div class="flex-shrink-0 me-3">
                    <div class="avatar-sm">
                        <div class="avatar-title bg-light text-primary rounded fs-18">
                            <i class="<?= $icon ?>"></i>
                        </div>
                    </div>
                </div>
                <div>
                    <p class="fs-15 fw-medium mb-1"><?= trim($highlight) ?></p>
                </div>
            </div>
        </div>
        <?php 
                endif;
            endforeach;
        else:
        ?>
        <div class="col-12">
            <p class="text-muted">No highlights available for this program.</p>
        </div>
        <?php endif; ?>
    </div>
</div>