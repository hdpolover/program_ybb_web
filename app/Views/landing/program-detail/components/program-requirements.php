<?php
/**
 * Program Requirements Component
 * 
 * Displays program requirements or prerequisites in a structured format
 * 
 * @param array|string $requirements The program requirements array or string
 * @param string $title (Optional) Custom title for the requirements section
 */
$title = $title ?? 'Program Requirements';
?>

<div class="program-requirements">
    <div class="card border border-light">
        <div class="card-header bg-light py-3">
            <h4 class="mb-0 fs-16"><?= $title ?></h4>
        </div>
        <div class="card-body">
            <div class="d-flex flex-column gap-3">
                <?php
                if (!empty($requirements)):
                    // Handle both array and string formats
                    $requirementItems = is_array($requirements) ? $requirements : explode("\n", $requirements);
                    foreach ($requirementItems as $requirement):
                        if (trim($requirement)):
                ?>
                <div class="d-flex align-items-start">
                    <i class="ri-arrow-right-circle-fill text-info fs-16 mt-1 me-2"></i>
                    <p class="mb-0"><?= trim($requirement) ?></p>
                </div>
                <?php
                        endif;
                    endforeach;
                else:
                ?>
                <p class="text-muted mb-0">No specific requirements for this program.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>