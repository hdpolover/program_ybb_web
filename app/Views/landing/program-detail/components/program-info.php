<?php
/**
 * Program Info Component
 * 
 * Displays key information about a program in a consistent format
 * 
 * @param array $program The program data array
 * @param string $display_style (Optional) Style of display ('card', 'inline', 'badge')
 */
?>

<div class="program-info <?= isset($display_style) ? "program-info-{$display_style}" : "program-info-card" ?>">
    <?php if (isset($display_style) && $display_style === 'inline'): ?>
        <div class="d-flex flex-wrap gap-3 align-items-center">
            <?php if(isset($program['duration'])): ?>
            <div class="d-flex align-items-center">
                <i class="ri-time-line text-primary me-1"></i>
                <span><?= $program['duration'] ?></span>
            </div>
            <?php endif; ?>

            <?php if(isset($program['location'])): ?>
            <div class="d-flex align-items-center">
                <i class="ri-map-pin-line text-primary me-1"></i>
                <span><?= $program['location'] ?></span>
            </div>
            <?php endif; ?>

            <?php if(isset($program['start_date'])): ?>
            <div class="d-flex align-items-center">
                <i class="ri-calendar-line text-primary me-1"></i>
                <span><?= date('M d, Y', strtotime($program['start_date'])) ?></span>
            </div>
            <?php endif; ?>
            
            <?php if(isset($program['price']) && $program['price'] > 0): ?>
            <div class="d-flex align-items-center">
                <i class="ri-money-dollar-circle-line text-primary me-1"></i>
                <span>$<?= number_format($program['price'], 2) ?></span>
            </div>
            <?php endif; ?>
        </div>
    <?php elseif (isset($display_style) && $display_style === 'badge'): ?>
        <div class="program-badges d-flex flex-wrap gap-2 mb-3">
            <?php if(isset($program['format'])): ?>
                <?php if ($program['format'] == 'Online'): ?>
                    <span class="badge bg-info"><i class="ri-global-line me-1"></i> Online</span>
                <?php elseif ($program['format'] == 'Hybrid'): ?>
                    <span class="badge bg-warning"><i class="ri-compass-3-line me-1"></i> Hybrid</span>
                <?php else: ?>
                    <span class="badge bg-success"><i class="ri-map-pin-line me-1"></i> In-person</span>
                <?php endif; ?>
            <?php endif; ?>

            <?php if(isset($program['level'])): ?>
                <?php if ($program['level'] == 'Beginner'): ?>
                    <span class="badge bg-success">Beginner</span>
                <?php elseif ($program['level'] == 'Intermediate'): ?>
                    <span class="badge bg-primary">Intermediate</span>
                <?php else: ?>
                    <span class="badge bg-danger">Advanced</span>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php if(isset($program['category'])): ?>
                <span class="badge bg-secondary"><?= ucfirst($program['category']) ?></span>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Default card view -->
        <div class="row g-3">
            <?php if(isset($program['duration'])): ?>
            <div class="col-sm-6 col-lg-3">
                <div class="p-2 border rounded text-center">
                    <div class="avatar-sm mx-auto mb-2">
                        <div class="avatar-title bg-soft-primary text-primary rounded-circle fs-18">
                            <i class="ri-time-line"></i>
                        </div>
                    </div>
                    <h5 class="fs-15 mb-0"><?= $program['duration'] ?></h5>
                    <p class="text-muted mb-0 fs-13">Duration</p>
                </div>
            </div>
            <?php endif; ?>

            <?php if(isset($program['location'])): ?>
            <div class="col-sm-6 col-lg-3">
                <div class="p-2 border rounded text-center">
                    <div class="avatar-sm mx-auto mb-2">
                        <div class="avatar-title bg-soft-primary text-primary rounded-circle fs-18">
                            <i class="ri-map-pin-line"></i>
                        </div>
                    </div>
                    <h5 class="fs-15 mb-0"><?= $program['location'] ?></h5>
                    <p class="text-muted mb-0 fs-13">Location</p>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if(isset($program['start_date'])): ?>
            <div class="col-sm-6 col-lg-3">
                <div class="p-2 border rounded text-center">
                    <div class="avatar-sm mx-auto mb-2">
                        <div class="avatar-title bg-soft-primary text-primary rounded-circle fs-18">
                            <i class="ri-calendar-line"></i>
                        </div>
                    </div>
                    <h5 class="fs-15 mb-0"><?= date('M d, Y', strtotime($program['start_date'])) ?></h5>
                    <p class="text-muted mb-0 fs-13">Start Date</p>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if(isset($program['capacity'])): ?>
            <div class="col-sm-6 col-lg-3">
                <div class="p-2 border rounded text-center">
                    <div class="avatar-sm mx-auto mb-2">
                        <div class="avatar-title bg-soft-primary text-primary rounded-circle fs-18">
                            <i class="ri-group-line"></i>
                        </div>
                    </div>
                    <h5 class="fs-15 mb-0"><?= $program['capacity'] ?></h5>
                    <p class="text-muted mb-0 fs-13">Capacity</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>