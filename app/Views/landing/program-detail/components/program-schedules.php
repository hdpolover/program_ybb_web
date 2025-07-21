<!-- Program Schedules Section -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-4">
            <div class="flex-shrink-0">
                <div class="avatar-sm">
                    <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-18">
                        <i class="ri-calendar-schedule-line"></i>
                    </div>
                </div>
            </div>
            <div class="flex-grow-1 ms-3">
                <h3 class="card-title mb-0">Program Schedules</h3>
                <p class="text-muted mb-0 mt-1">Important dates and deadlines for this program</p>
            </div>
        </div>

        <?php if (isset($schedules) && !empty($schedules)): ?>
            <?php 
                // Sort schedules by order_number
                usort($schedules, function($a, $b) {
                    return $a['order_number'] - $b['order_number'];
                });
                
                $currentDate = date('Y-m-d');
            ?>
            <div class="table-responsive">
                <table class="table table-bordered align-middle table-hover">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 10%">#</th>
                            <th class="text-center" style="width: 25%">Date Range</th>
                            <th style="width: 30%">Schedule Name</th>
                            <th style="width: 35%">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedules as $schedule): 
                            // Check if schedule is currently active
                            $isCurrentlyActive = false;
                            if (isset($schedule['start_date']) && isset($schedule['end_date'])) {
                                $startDate = date('Y-m-d', strtotime($schedule['start_date']));
                                $endDate = date('Y-m-d', strtotime($schedule['end_date']));
                                $isCurrentlyActive = ($currentDate >= $startDate && $currentDate <= $endDate);
                            }
                            
                            // Only display if is_active is true
                            if (isset($schedule['is_active']) && $schedule['is_active'] == '1'):
                        ?>
                        <tr class="<?= $isCurrentlyActive ? 'table-warning' : '' ?>">
                            <td class="text-center">
                                <div class="avatar-sm mx-auto">
                                    <span class="avatar-title rounded-circle <?= $isCurrentlyActive ? 'bg-warning text-dark' : 'bg-soft-primary text-primary' ?> shadow-sm fs-14 fw-medium">
                                        <?= $schedule['order_number'] ?>
                                    </span>
                                </div>
                            </td>
                            <td class="text-center">
                                <?php if (isset($schedule['start_date'])): ?>
                                    <div class="d-flex flex-column">
                                        <span class="badge bg-soft-primary text-primary px-3 py-2 mb-1 fs-12 rounded-pill">
                                            <i class="ri-calendar-line me-1"></i>
                                            <?= date('M d, Y', strtotime($schedule['start_date'])) ?>
                                        </span>
                                        <?php if (isset($schedule['end_date']) && $schedule['end_date'] != $schedule['start_date']): ?>
                                            <span class="badge bg-soft-danger text-danger px-3 py-2 fs-12 rounded-pill">
                                                <i class="ri-arrow-right-line me-1"></i>
                                                <?= date('M d, Y', strtotime($schedule['end_date'])) ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($isCurrentlyActive): ?>
                                            <span class="badge bg-success mt-1 fs-10">
                                                <i class="ri-play-circle-line me-1"></i>Active Now
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="badge bg-soft-secondary text-secondary px-3 py-2 fs-12 rounded-pill">
                                        <i class="ri-time-line me-1"></i> TBA
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div>
                                    <h5 class="fs-14 mb-0 <?= $isCurrentlyActive ? 'fw-semibold text-warning' : '' ?>">
                                        <?= $schedule['name'] ?? 'To Be Announced' ?>
                                    </h5>
                                </div>
                            </td>
                            <td>
                                <p class="text-muted mb-0 fs-13">
                                    <?= !empty($schedule['description']) ? nl2br(htmlspecialchars($schedule['description'])) : 'No description available' ?>
                                </p>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="alert alert-info mt-3 mb-0">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-information-line fs-16 align-middle"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="mb-0"><strong>Important:</strong> All dates and deadlines are subject to change. Please check this page regularly for the most up-to-date information. Active schedules are highlighted in yellow.</p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center p-4 border rounded-3 bg-light">
                <div class="avatar-md mx-auto mb-3">
                    <div class="avatar-title bg-soft-primary text-primary display-6 rounded-circle">
                        <i class="ri-calendar-schedule-line"></i>
                    </div>
                </div>
                <h5>No Schedules Available</h5>
                <p class="text-muted">The program schedule will be published soon. Please check back later for important dates and deadlines.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<!-- End Program Schedules Section -->
