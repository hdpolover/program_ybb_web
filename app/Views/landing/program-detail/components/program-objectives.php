<?php
/**
 * Program Objectives Component
 * 
 * Displays program objectives in a structured format
 * 
 * @param array|string $objectives The program objectives array or string
 * @param string $title (Optional) Custom title for the objectives section
 */
$title = $title ?? 'Program Objectives';
?>

<div class="program-objectives">
    <div class="card border border-light">
        <div class="card-header bg-light py-3">
            <h4 class="mb-0 fs-16"><?= $title ?></h4>
        </div>
        <div class="card-body">
            <div class="d-flex flex-column gap-3">
                <?php
                if (!empty($objectives)):
                    // Handle both array and string formats
                    $objectiveItems = is_array($objectives) ? $objectives : explode("\n", $objectives);
                    foreach ($objectiveItems as $objective):
                        if (trim($objective)):
                ?>
                <div class="d-flex align-items-start">
                    <i class="ri-checkbox-circle-fill text-success fs-16 mt-1 me-2"></i>
                    <p class="mb-0"><?= trim($objective) ?></p>
                </div>
                <?php
                        endif;
                    endforeach;
                else:
                ?>
                <p class="text-muted mb-0">No objectives specified for this program.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>