<!-- Program Timeline Section -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-4">
            <div class="flex-shrink-0">
                <div class="avatar-sm">
                    <div class="avatar-title bg-success-subtle text-success rounded-circle fs-18">
                        <i class="ri-calendar-event-line"></i>
                    </div>
                </div>
            </div>
            <div class="flex-grow-1 ms-3">
                <h3 class="card-title mb-0">Program Timeline</h3>
            </div>
        </div>

        <div class="table-responsive mt-2">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col" style="width: 30%" class="text-center">Period</th>
                        <th scope="col" class="text-center">Activity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $counter = 0;
                    $currentDate = date('Y-m-d');
                    foreach ($schedules as $schedule) :
                        $counter++;

                        // Check if schedule is currently active based on date range
                        $isCurrentlyActive = false;
                        if (isset($schedule['start_date']) && isset($schedule['end_date'])) {
                            $startDate = date('Y-m-d', strtotime($schedule['start_date']));
                            $endDate = date('Y-m-d', strtotime($schedule['end_date']));
                            $isCurrentlyActive = ($currentDate >= $startDate && $currentDate <= $endDate);
                        }

                        // Only display if is_active is true (not deleted)
                        if (isset($schedule['is_active']) && $schedule['is_active']):
                    ?>
                            <tr class="<?= $isCurrentlyActive ? 'table-active' : '' ?>">
                                <td>
                                    <?php if (isset($schedule['start_date'])) : ?>
                                        <div class="d-flex flex-column">
                                            <span class="badge bg-soft-primary text-primary px-3 py-2 mb-2 fs-12 rounded-pill">
                                                <i class="ri-calendar-line me-1"></i>
                                                <?= date('M d, Y', strtotime($schedule['start_date'])) ?>
                                            </span>
                                            <?php if (isset($schedule['end_date']) && $schedule['end_date'] != $schedule['start_date']) : ?>
                                                <span class="badge bg-soft-danger text-danger px-3 py-2 fs-12 rounded-pill">
                                                    <i class="ri-arrow-right-line me-1"></i>
                                                    <?= date('M d, Y', strtotime($schedule['end_date'])) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    <?php else : ?>
                                        <span class="badge bg-soft-secondary text-secondary px-3 py-2 fs-12 rounded-pill">
                                            <i class="ri-time-line me-1"></i> To Be Announced
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="avatar-sm">
                                                <span class="avatar-title rounded-circle <?= $isCurrentlyActive ? 'bg-info text-white' : 'bg-soft-info text-info' ?> shadow-sm">
                                                    <?= isset($schedule['order_number']) ? $schedule['order_number'] : $counter ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="timeline-event">
                                            <h5 class="fs-14 mb-1 <?= $isCurrentlyActive ? 'fw-semibold text-primary' : '' ?>">
                                                <?= $schedule['name'] ?? 'TBA' ?>
                                                <?php if ($isCurrentlyActive): ?>
                                                    <span class="badge bg-success-subtle text-success ms-1 fs-10">Current</span>
                                                <?php endif; ?>
                                            </h5>
                                            <?php if (isset($schedule['description']) && !empty($schedule['description'])) : ?>
                                                <p class="text-muted mb-0 fs-13"><?= $schedule['description'] ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                    <?php endif; // end of is_active check
                    endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="alert alert-info mt-3 mb-0">
            <div class="d-flex">
                <div class="flex-shrink-0">
                    <i class="ri-information-line fs-16 align-middle"></i>
                </div>
                <div class="flex-grow-1 ms-2">
                    <p class="mb-0">Please note that the timeline is subject to change. Check back regularly for updates.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Program Timeline Section -->