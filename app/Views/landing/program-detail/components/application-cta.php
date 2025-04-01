<?php
/**
 * Application CTA Component
 * 
 * Displays a call-to-action for program applications
 * 
 * @param array $program The program data array
 * @param string $style (Optional) Style of the CTA ('card', 'banner', 'minimal')
 */
$style = $style ?? 'card';
$buttonText = $buttonText ?? 'Apply Now';
$bgClass = $bgClass ?? 'bg-primary';
?>

<?php if ($style === 'banner'): ?>
<div class="application-cta application-cta-banner mb-4">
    <div class="card <?= $bgClass ?>">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="text-white mb-2">Ready to join our <?= $program['name'] ?? 'Program' ?>?</h4>
                    <p class="text-white-75 mb-md-0">
                        <?php if(isset($program['application_deadline'])): ?>
                            Applications close on <?= date('F j, Y', strtotime($program['application_deadline'])) ?>. 
                        <?php endif; ?>
                        <?= isset($program['available_spots']) ? $program['available_spots'] . ' spots remaining.' : 'Limited spots available!' ?>
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <?php if(isset($program['registration_open']) && $program['registration_open']): ?>
                        <a href="<?= base_url('apply/' . ($program['slug'] ?? $program['id'] ?? '')) ?>" class="btn btn-light">
                            <i class="ri-user-add-line me-1"></i> <?= $buttonText ?>
                        </a>
                    <?php else: ?>
                        <button class="btn btn-light" disabled>
                            <i class="ri-time-line me-1"></i> Applications Closed
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php elseif ($style === 'minimal'): ?>
<div class="application-cta application-cta-minimal mb-4">
    <div class="d-flex align-items-center justify-content-between border rounded p-3">
        <div>
            <?php if(isset($program['application_deadline'])): ?>
                <p class="text-muted mb-0"><i class="ri-calendar-event-line me-1"></i> Applications close on <?= date('M j, Y', strtotime($program['application_deadline'])) ?></p>
            <?php endif; ?>
        </div>
        <div>
            <?php if(isset($program['registration_open']) && $program['registration_open']): ?>
                <a href="<?= base_url('apply/' . ($program['slug'] ?? $program['id'] ?? '')) ?>" class="btn btn-sm btn-primary">
                    <i class="ri-user-add-line me-1"></i> <?= $buttonText ?>
                </a>
            <?php else: ?>
                <button class="btn btn-sm btn-light" disabled>
                    <i class="ri-time-line me-1"></i> Applications Closed
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php else: /* Default: card style */ ?>
<div class="application-cta application-cta-card mb-4">
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Program Application</h5>
        </div>
        <div class="card-body">
            <div class="text-center mb-4">
                <div class="avatar-md mx-auto mb-4">
                    <div class="avatar-title bg-light text-primary rounded-circle fs-24">
                        <i class="ri-file-list-3-line"></i>
                    </div>
                </div>
                <h5 class="fs-16">Join our <?= $program['name'] ?? 'Program' ?></h5>
                
                <?php if(isset($program['available_spots'])): ?>
                    <p class="text-muted mb-4">Only <?= $program['available_spots'] ?> spots remaining!</p>
                <?php endif; ?>
                
                <?php if(isset($program['application_deadline'])): ?>
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="ri-calendar-event-line fs-20 text-primary me-2"></i>
                            <div class="text-start">
                                <h6 class="mb-0">Application Deadline</h6>
                                <p class="fs-14 text-muted mb-0"><?= date('F j, Y', strtotime($program['application_deadline'])) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="d-grid">
                <?php if(isset($program['registration_open']) && $program['registration_open']): ?>
                    <a href="<?= base_url('apply/' . ($program['slug'] ?? $program['id'] ?? '')) ?>" class="btn btn-primary">
                        <i class="ri-user-add-line me-1"></i> <?= $buttonText ?>
                    </a>
                <?php else: ?>
                    <button class="btn btn-light" disabled>
                        <i class="ri-time-line me-1"></i> Applications Closed
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>