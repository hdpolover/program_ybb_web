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
                        <th scope="col" style="width: 30%">Period</th>
                        <th scope="col">Activity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $schedule) : ?>
                        <tr>
                            <td>
                                <?php if (isset($schedule['start_date'])) : ?>
                                    <div class="d-flex flex-column">
                                        <span class="badge bg-soft-primary text-primary px-3 py-2 mb-2 fs-12">
                                            <?= date('M d, Y', strtotime($schedule['start_date'])) ?>
                                        </span>
                                        <?php if (isset($schedule['end_date']) && $schedule['end_date'] != $schedule['start_date']) : ?>
                                            <span class="badge bg-soft-danger text-danger px-3 py-2 fs-12">
                                                to <?= date('M d, Y', strtotime($schedule['end_date'])) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php else : ?>
                                    <span class="badge bg-soft-secondary text-secondary">To Be Announced</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if (isset($schedule['order_number'])): ?>
                                        <div class="me-3">
                                            <div class="avatar-sm">
                                                <span class="avatar-title rounded-circle bg-soft-info text-info">
                                                    <?= $schedule['order_number'] ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h5 class="fs-14 mb-1"><?= $schedule['name'] ?? 'TBA' ?></h5>
                                        <?php if (isset($schedule['description']) && !empty($schedule['description'])) : ?>
                                            <p class="text-muted mb-0 fs-12"><?= $schedule['description'] ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
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
                    <p class="mb-0">Please note that the timeline is subject to change. Check back regularly for updates.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Program Timeline Section -->