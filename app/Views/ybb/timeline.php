<?php

function formatDateRange($start_date, $end_date)
{
    if (date('Y-m', strtotime($start_date)) == date('Y-m', strtotime($end_date))) {
        return date('F j', strtotime($start_date)) . '-' . date('j, Y', strtotime($end_date));
    } else {
        return date('F j', strtotime($start_date)) . ' - ' . date('F j, Y', strtotime($end_date));
    }
}
?>

<!-- Timeline 4 - Bootstrap Brain Component -->
<section class="bsb-timeline-4 bg-light py-3 py-md-5 py-xl-8">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-10 col-md-12 col-xl-10 col-xxl-9">
                <ul class="timeline">

                    <?php foreach ($program_schedules as $schedule) : ?>
                        <?php $order = $schedule['order_number']; ?>
                        <li class="timeline-item <?= $order % 2 == 0 ? 'right' : 'left' ?>">
                            <div class="timeline-body">
                                <div class="timeline-meta">
                                    <div class="d-inline-flex flex-column px-2 py-1 text-success-emphasis bg-success-subtle border border-success-subtle rounded-2 text-md-end">
                                        <span><?= formatDateRange($schedule['start_date'], $schedule['end_date']) ?> </span>
                                    </div>
                                </div>
                                <div class="timeline-content timeline-indicator">
                                    <div class="card border-0 shadow">
                                        <div class="card-body p-xl-4">
                                            <h1 class="card-title mb-2 display-6"><?= $schedule['name'] ?></h1>
                                            <p class="card-text m-0"><?= $schedule['description'] ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                    <?php endforeach; ?>

                </ul>

            </div>
        </div>
    </div>
</section>